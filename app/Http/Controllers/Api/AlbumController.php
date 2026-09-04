<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlbumResource;
use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'type' => ['nullable', 'string', 'in:album,ep'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Album::withCount('tracks')->orderBy('type')->orderBy('sequence');
        if (! empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        $perPage = (int) ($validated['per_page'] ?? 25);

        return AlbumResource::collection($query->paginate($perPage)->appends($request->query()));
    }

    public function show(Album $album)
    {
        $album->load(['tracks' => fn ($q) => $q->with('song:id,title_original,origin_group,preview_url')->orderBy('position')]);

        return (new AlbumResource($album))->response();
    }
}
