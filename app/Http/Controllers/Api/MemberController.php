<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status'        => ['nullable', 'string', 'in:Aktif,Lulus,Trainee'],
            'generation_id' => ['nullable', 'integer'],
            'search'        => ['nullable', 'string', 'max:100'],
            'per_page'      => ['nullable', 'integer', 'min:1', 'max:200'],
            'sort'          => ['nullable', 'string', 'in:name,join_date,birth_date,graduation_date,-name,-join_date,-birth_date,-graduation_date'],
        ]);

        $query = Member::query()->with('generation');

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }
        if (! empty($validated['generation_id'])) {
            $query->where('generation_id', $validated['generation_id']);
        }
        if (! empty($validated['search'])) {
            $query->search($validated['search']);
        }

        $sort = $validated['sort'] ?? 'name';
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');
        $query->orderBy($column, $direction);

        $perPage = (int) ($validated['per_page'] ?? 25);

        return MemberResource::collection($query->paginate($perPage)->appends($request->query()));
    }

    public function show(int $member)
    {
        $model = Member::with(['generation', 'currentTeams.team', 'singles'])->findOrFail($member);

        return (new MemberResource($model))->response();
    }
}
