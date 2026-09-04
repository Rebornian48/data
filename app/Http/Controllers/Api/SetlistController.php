<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SetlistResource;
use App\Models\Setlist;
use Illuminate\Http\Request;

class SetlistController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'type' => ['nullable', 'string', 'in:regular,special'],
            'search' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Setlist::withCount('songs')->orderBy('type')->orderBy('name');
        if (! empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }
        if (! empty($validated['search'])) {
            $query->where('name', 'like', "%{$validated['search']}%");
        }

        $perPage = (int) ($validated['per_page'] ?? 25);

        return SetlistResource::collection($query->paginate($perPage)->appends($request->query()));
    }

    public function show(Setlist $setlist)
    {
        $setlist->load(['songs' => fn ($q) => $q->with('single:id,code,title')]);

        return (new SetlistResource($setlist))->response();
    }
}
