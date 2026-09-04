<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlbumController extends Controller
{
    public function index()
    {
        $albums = Album::withCount('tracks')->orderBy('type')->orderBy('sequence')->paginate(30);

        return view('admin.albums.index', compact('albums'));
    }

    public function create()
    {
        return view('admin.albums.create', [
            'album' => new Album(['type' => 'album']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateAlbum($request);
        $album = Album::create($data);
        $this->syncTracks($album, $request->input('tracks', []));

        return redirect()->route('admin.albums.index')
            ->with('success', "Album '{$album->title}' berhasil ditambahkan.");
    }

    public function edit(Album $album)
    {
        $album->load(['tracks' => fn ($q) => $q->with('song')->orderBy('position')]);

        return view('admin.albums.edit', compact('album'));
    }

    public function update(Request $request, Album $album)
    {
        $data = $this->validateAlbum($request);
        $album->update($data);
        $this->syncTracks($album, $request->input('tracks', []));

        return redirect()->route('admin.albums.index')
            ->with('success', "Album '{$album->title}' berhasil diupdate.");
    }

    public function destroy(Album $album)
    {
        $title = $album->title;
        $album->delete();

        return redirect()->route('admin.albums.index')
            ->with('success', "Album '{$title}' berhasil dihapus.");
    }

    private function validateAlbum(Request $request): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'type' => ['required', 'in:album,ep'],
            'title' => ['required', 'string', 'max:255'],
            'title_jp' => ['nullable', 'string', 'max:255'],
            'sequence' => ['required', 'integer', 'min:1'],
            'release_date' => ['nullable', 'date'],
            'cover_url' => ['nullable', 'string', 'max:2048'],
        ]);
    }

    /**
     * Sync tracks dari input textarea (satu baris = satu track).
     * Format tiap baris: "Judul" atau "Judul || song_id".
     */
    private function syncTracks(Album $album, array|string $raw): void
    {
        if (is_string($raw)) {
            $lines = preg_split('/\r?\n/', trim($raw));
        } else {
            $lines = $raw;
        }
        $lines = array_values(array_filter(array_map('trim', $lines ?: [])));

        DB::transaction(function () use ($album, $lines) {
            $album->tracks()->delete();
            $songIdByTitle = Song::pluck('id', 'title')->toArray();
            foreach ($lines as $i => $line) {
                [$title, $songId] = array_pad(array_map('trim', explode('||', $line, 2)), 2, null);
                if (! $title) {
                    continue;
                }
                $songId = $songId ? (int) $songId : ($songIdByTitle[$title] ?? null);
                $album->tracks()->create([
                    'position' => $i + 1,
                    'title' => $title,
                    'song_id' => $songId,
                ]);
            }
        });
    }
}
