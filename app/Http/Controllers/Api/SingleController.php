<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SingleResource;
use App\Models\Single;
use Illuminate\Http\Request;

class SingleController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search'   => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
            'sort'     => ['nullable', 'string', 'in:sequence,release_date,title,-sequence,-release_date,-title'],
        ]);

        $query = Single::query();

        if (! empty($validated['search'])) {
            $term = $validated['search'];
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%");
            });
        }

        $sort = $validated['sort'] ?? 'sequence';
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');
        $query->orderBy($column, $direction);

        $perPage = (int) ($validated['per_page'] ?? 25);

        return SingleResource::collection($query->paginate($perPage)->appends($request->query()));
    }

    public function show(int $single)
    {
        $model = Single::with('members')->findOrFail($single);

        return (new SingleResource($model))->response();
    }
}
