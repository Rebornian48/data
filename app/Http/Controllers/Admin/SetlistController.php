<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setlist;
use App\Models\Song;
use Illuminate\Http\Request;

class SetlistController extends Controller
{
    public function index()
    {
        $setlists = Setlist::withCount('songs')->orderBy('type')->orderBy('name')->paginate(50);

        return view('admin.setlists.index', compact('setlists'));
    }

    public function create()
    {
        return view('admin.setlists.create', [
            'setlist' => new Setlist(['type' => 'regular']),
            'songs' => Song::orderBy('title')->get(['id', 'title', 'title_original']),
            'selectedSongs' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateSetlist($request);
        $setlist = Setlist::create($data);
        $this->syncSongs($setlist, $request->input('song_ids', []));

        return redirect()->route('admin.setlists.index')
            ->with('success', "Setlist '{$setlist->name}' berhasil ditambahkan.");
    }

    public function edit(Setlist $setlist)
    {
        $setlist->load('songs');

        return view('admin.setlists.edit', [
            'setlist' => $setlist,
            'songs' => Song::orderBy('title')->get(['id', 'title', 'title_original']),
            'selectedSongs' => $setlist->songs->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, Setlist $setlist)
    {
        $data = $this->validateSetlist($request);
        $setlist->update($data);
        $this->syncSongs($setlist, $request->input('song_ids', []));

        return redirect()->route('admin.setlists.index')
            ->with('success', "Setlist '{$setlist->name}' berhasil diupdate.");
    }

    public function destroy(Setlist $setlist)
    {
        $name = $setlist->name;
        $setlist->delete();

        return redirect()->route('admin.setlists.index')
            ->with('success', "Setlist '{$name}' berhasil dihapus.");
    }

    private function validateSetlist(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:regular,special'],
            'description' => ['nullable', 'string'],
        ]);
    }

    private function syncSongs(Setlist $setlist, array $ids): void
    {
        $sync = [];
        $pos = 1;
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id) {
                $sync[$id] = ['position' => $pos++];
            }
        }
        $setlist->songs()->sync($sync);
    }
}
