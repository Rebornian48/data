<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MvLocationResource;
use App\Models\MvLocation;
use Illuminate\Http\Request;

class MvLocationController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'category' => ['nullable', 'string', 'max:100'],
            'year' => ['nullable', 'integer'],
            'has_coords' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $query = MvLocation::query()->orderBy('release_year')->orderBy('song_title');
        if (! empty($validated['category'])) {
            $query->where('category', $validated['category']);
        }
        if (! empty($validated['year'])) {
            $query->where('release_year', $validated['year']);
        }
        if (array_key_exists('has_coords', $validated)) {
            $validated['has_coords']
                ? $query->whereNotNull('latitude')->whereNotNull('longitude')
                : $query->where(fn ($q) => $q->whereNull('latitude')->orWhereNull('longitude'));
        }

        $perPage = (int) ($validated['per_page'] ?? 50);

        return MvLocationResource::collection($query->paginate($perPage)->appends($request->query()));
    }

    public function show(MvLocation $mvLocation)
    {
        return (new MvLocationResource($mvLocation))->response();
    }
}
