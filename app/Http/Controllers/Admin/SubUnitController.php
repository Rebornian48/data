<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubUnit;
use App\Models\SubUnitSong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubUnitController extends Controller
{
    public function index()
    {
        $subUnits = SubUnit::withCount('songs')->orderBy('name')->get();

        return view('admin.sub_units.index', compact('subUnits'));
    }

    public function create()
    {
        return view('admin.sub_units.create', [
            'subUnit' => new SubUnit,
            'songs' => collect(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validate_($request);
        $subUnit = SubUnit::create($data);
        $this->syncSongs($subUnit, $request);

        return redirect()->route('admin.sub-units.index')
            ->with('success', "Sub-unit '{$subUnit->name}' berhasil ditambahkan.");
    }

    public function edit(SubUnit $subUnit)
    {
        $subUnit->load('songs');

        return view('admin.sub_units.edit', [
            'subUnit' => $subUnit,
            'songs' => $subUnit->songs,
        ]);
    }

    public function update(Request $request, SubUnit $subUnit)
    {
        $data = $this->validate_($request);
        $subUnit->update($data);
        $this->syncSongs($subUnit, $request);

        return redirect()->route('admin.sub-units.index')
            ->with('success', "Sub-unit '{$subUnit->name}' berhasil diupdate.");
    }

    public function destroy(SubUnit $subUnit)
    {
        $name = $subUnit->name;
        $subUnit->delete();

        return redirect()->route('admin.sub-units.index')
            ->with('success', "Sub-unit '{$name}' berhasil dihapus.");
    }

    private function validate_(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
    }

    /**
     * Songs dikirim sebagai array of {id?, title, title_original, origin_group, debut_date, ...}.
     * Yang tidak ada di request akan dihapus.
     */
    private function syncSongs(SubUnit $subUnit, Request $request): void
    {
        $rows = $request->input('songs', []);
        $keepIds = [];

        DB::transaction(function () use ($subUnit, $rows, &$keepIds) {
            foreach ($rows as $r) {
                $title = trim($r['title'] ?? '');
                if ($title === '') {
                    continue;
                }
                $attrs = [
                    'title' => $title,
                    'title_original' => $r['title_original'] ?? null,
                    'origin_group' => $r['origin_group'] ?? null,
                    'debut_date' => $r['debut_date'] ?? null,
                    'debut_at' => $r['debut_at'] ?? null,
                    'released' => ! empty($r['released']),
                    'has_mv' => ! empty($r['has_mv']),
                    'preview_url' => $r['preview_url'] ?? null,
                ];
                if (! empty($r['id'])) {
                    $song = SubUnitSong::where('sub_unit_id', $subUnit->id)->find($r['id']);
                    if ($song) {
                        $song->update($attrs);
                        $keepIds[] = $song->id;

                        continue;
                    }
                }
                $created = $subUnit->songs()->create($attrs);
                $keepIds[] = $created->id;
            }
            $subUnit->songs()->whereNotIn('id', $keepIds)->delete();
        });
    }
}
