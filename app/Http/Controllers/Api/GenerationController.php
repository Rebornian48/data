<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GenerationResource;
use App\Models\Generation;
use Illuminate\Http\Request;

class GenerationController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort'     => ['nullable', 'string', 'in:join_date,announcement_date,name,-join_date,-announcement_date,-name'],
        ]);

        $query = Generation::query()
            ->withCount(['members', 'activeMembers', 'graduatedMembers']);

        $sort = $validated['sort'] ?? 'join_date';
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');
        $query->orderBy($column, $direction);

        $perPage = (int) ($validated['per_page'] ?? 25);

        return GenerationResource::collection($query->paginate($perPage)->appends($request->query()));
    }

    public function show(int $generation)
    {
        $model = Generation::withCount(['members', 'activeMembers', 'graduatedMembers'])
            ->with(['members' => fn ($q) => $q->orderBy('name')])
            ->findOrFail($generation);

        return (new GenerationResource($model))->response();
    }
}
