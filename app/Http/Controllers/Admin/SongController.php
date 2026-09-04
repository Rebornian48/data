<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Single;
use App\Models\Song;
use Illuminate\Http\Request;

class SongController extends Controller
{
    private const GROUPS = ['AKB48', 'SKE48', 'NMB48', 'HKT48', 'NGT48', 'Original'];

    public function index(Request $request)
    {
        $query = Song::with('single:id,code,title');
        if ($term = $request->input('q')) {
            $query->where(fn ($q) => $q->where('title', 'like', "%{$term}%")
                ->orWhere('title_original', 'like', "%{$term}%"));
        }
        if ($request->filled('single_id')) {
            $query->where('single_id', $request->input('single_id'));
        }
        if ($request->filled('origin_group')) {
            $query->where('origin_group', $request->input('origin_group'));
        }

        $songs = $query->orderBy('external_id')->paginate(30)->withQueryString();

        return view('admin.songs.index', [
            'songs' => $songs,
            'singles' => Single::orderBy('sequence')->get(['id', 'code', 'title']),
            'groups' => self::GROUPS,
        ]);
    }

    public function create()
    {
        return view('admin.songs.create', [
            'song' => new Song,
            'singles' => Single::orderBy('sequence')->get(['id', 'code', 'title']),
            'groups' => self::GROUPS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateSong($request);
        $song = Song::create($data);

        return redirect()->route('admin.songs.index')
            ->with('success', "Lagu '{$song->title}' berhasil ditambahkan.");
    }

    public function edit(Song $song)
    {
        return view('admin.songs.edit', [
            'song' => $song,
            'singles' => Single::orderBy('sequence')->get(['id', 'code', 'title']),
            'groups' => self::GROUPS,
        ]);
    }

    public function update(Request $request, Song $song)
    {
        $data = $this->validateSong($request);
        $song->update($data);

        return redirect()->route('admin.songs.index')
            ->with('success', "Lagu '{$song->title}' berhasil diupdate.");
    }

    public function destroy(Song $song)
    {
        $title = $song->title;
        $song->delete();

        return redirect()->route('admin.songs.index')
            ->with('success', "Lagu '{$title}' berhasil dihapus.");
    }

    private function validateSong(Request $request): array
    {
        $data = $request->validate([
            'external_id' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:255'],
            'title_original' => ['nullable', 'string', 'max:255'],
            'origin_group' => ['nullable', 'string', 'max:32'],
            'single_id' => ['nullable', 'integer', 'exists:singles,id'],
            'single_ref_raw' => ['nullable', 'string', 'max:255'],
            'other_compilations' => ['nullable', 'string', 'max:255'],
            'setlist' => ['nullable', 'string', 'max:255'],
            'special_setlist' => ['nullable', 'string', 'max:255'],
            'debut_date' => ['nullable', 'date'],
            'debut_at' => ['nullable', 'string', 'max:255'],
            'released' => ['nullable', 'boolean'],
            'preview_url' => ['nullable', 'string', 'max:2048'],
            'mv_title' => ['nullable', 'string', 'max:255'],
        ]);
        $data['released'] = (bool) ($data['released'] ?? false);

        return $data;
    }
}
