<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CouplingSongResource;
use App\Models\CouplingSong;
use Illuminate\Http\Request;

class CouplingSongController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'single_id' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = CouplingSong::with(['single:id,code,title', 'members'])
            ->orderBy('single_id')
            ->orderBy('id');

        if (! empty($validated['single_id'])) {
            $query->where('single_id', $validated['single_id']);
        }

        $perPage = (int) ($validated['per_page'] ?? 25);

        return CouplingSongResource::collection($query->paginate($perPage)->appends($request->query()));
    }

    public function show(CouplingSong $couplingSong)
    {
        $couplingSong->load(['single:id,code,title', 'members']);

        return (new CouplingSongResource($couplingSong))->response();
    }
}
