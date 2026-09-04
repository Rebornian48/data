<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SongResource;
use App\Models\Song;
use Illuminate\Http\Request;

class SongController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'single_id' => ['nullable', 'integer'],
            'origin_group' => ['nullable', 'string', 'max:32'],
            'released' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
            'sort' => ['nullable', 'string', 'in:external_id,title,debut_date,-external_id,-title,-debut_date'],
        ]);

        $query = Song::query()->with('single:id,code,title');

        if (! empty($validated['search'])) {
            $term = $validated['search'];
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('title_original', 'like', "%{$term}%");
            });
        }
        if (isset($validated['single_id'])) {
            $query->where('single_id', $validated['single_id']);
        }
        if (! empty($validated['origin_group'])) {
            $query->where('origin_group', $validated['origin_group']);
        }
        if (array_key_exists('released', $validated)) {
            $query->where('released', $validated['released']);
        }

        $sort = $validated['sort'] ?? 'external_id';
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $query->orderBy(ltrim($sort, '-'), $direction);

        $perPage = (int) ($validated['per_page'] ?? 25);

        return SongResource::collection($query->paginate($perPage)->appends($request->query()));
    }

    public function show(Song $song)
    {
        $song->load('single:id,code,title');

        return (new SongResource($song))->response();
    }
}
