<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'active_only' => ['nullable', 'boolean'],
            'per_page'    => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Team::query()->withCount('currentMembers');

        if (! empty($validated['active_only'])) {
            $query->whereNull('disbanded_at');
        }

        $query->orderBy('code');

        $perPage = (int) ($validated['per_page'] ?? 50);

        return TeamResource::collection($query->paginate($perPage)->appends($request->query()));
    }

    public function show(int $team)
    {
        $model = Team::withCount('currentMembers')
            ->with(['currentMembers', 'captains'])
            ->findOrFail($team);

        return (new TeamResource($model))->response();
    }
}
