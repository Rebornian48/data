<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CouplingSong;
use App\Models\Member;
use App\Models\Single;
use Illuminate\Http\Request;

class CouplingSongController extends Controller
{
    private const GROUPS = ['AKB48', 'SKE48', 'NMB48', 'HKT48', 'NGT48', 'Original'];

    public function index()
    {
        $rows = CouplingSong::with(['single:id,code,title'])
            ->withCount('members')
            ->orderBy('single_id')
            ->orderBy('id')
            ->paginate(30);

        return view('admin.coupling_songs.index', compact('rows'));
    }

    public function create()
    {
        return view('admin.coupling_songs.create', [
            'row' => new CouplingSong,
            'singles' => Single::orderBy('sequence')->get(['id', 'code', 'title']),
            'members' => Member::orderBy('name')->get(['id', 'name', 'nickname']),
            'groups' => self::GROUPS,
            'selectedMembers' => [],
            'selectedCenters' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validate_($request);
        $row = CouplingSong::create($data);
        $this->syncMembers($row, $request);

        return redirect()->route('admin.coupling-songs.index')
            ->with('success', "Coupling '{$row->title}' berhasil ditambahkan.");
    }

    public function edit(CouplingSong $couplingSong)
    {
        $couplingSong->load('members');
        $selectedMembers = $couplingSong->members
            ->sortBy('pivot.position')
            ->pluck('id')
            ->all();
        $selectedCenters = $couplingSong->members
            ->filter(fn ($m) => $m->pivot->role === 'center')
            ->pluck('id')
            ->all();

        return view('admin.coupling_songs.edit', [
            'row' => $couplingSong,
            'singles' => Single::orderBy('sequence')->get(['id', 'code', 'title']),
            'members' => Member::orderBy('name')->get(['id', 'name', 'nickname']),
            'groups' => self::GROUPS,
            'selectedMembers' => $selectedMembers,
            'selectedCenters' => $selectedCenters,
        ]);
    }

    public function update(Request $request, CouplingSong $couplingSong)
    {
        $data = $this->validate_($request);
        $couplingSong->update($data);
        $this->syncMembers($couplingSong, $request);

        return redirect()->route('admin.coupling-songs.index')
            ->with('success', "Coupling '{$couplingSong->title}' berhasil diupdate.");
    }

    public function destroy(CouplingSong $couplingSong)
    {
        $title = $couplingSong->title;
        $couplingSong->delete();

        return redirect()->route('admin.coupling-songs.index')
            ->with('success', "Coupling '{$title}' berhasil dihapus.");
    }

    private function validate_(Request $request): array
    {
        return $request->validate([
            'single_id' => ['required', 'integer', 'exists:singles,id'],
            'title' => ['required', 'string', 'max:255'],
            'title_jp' => ['nullable', 'string', 'max:255'],
            'origin_group' => ['nullable', 'string', 'max:32'],
            'release_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'mv_title' => ['nullable', 'string', 'max:255'],
            'mv_url' => ['nullable', 'string', 'max:2048'],
            'audio_file' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function syncMembers(CouplingSong $row, Request $request): void
    {
        $memberIds = array_map('intval', $request->input('member_ids', []));
        $centerIds = array_flip(array_map('intval', $request->input('center_ids', [])));

        $sync = [];
        $pos = 1;
        foreach ($memberIds as $id) {
            if (! $id || isset($sync[$id])) {
                continue;
            }
            $sync[$id] = [
                'role' => isset($centerIds[$id]) ? 'center' : 'member',
                'position' => $pos++,
            ];
        }
        // Center yang tidak ada di member_ids tetap dimasukkan.
        foreach (array_keys($centerIds) as $id) {
            if (! isset($sync[$id])) {
                $sync[$id] = ['role' => 'center', 'position' => $pos++];
            }
        }
        $row->members()->sync($sync);
    }
}
