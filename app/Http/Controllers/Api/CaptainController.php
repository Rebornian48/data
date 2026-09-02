<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CaptainResource;
use App\Models\Captain;
use Illuminate\Http\Request;

class CaptainController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'active_only' => ['nullable', 'boolean'],
            'team_id'     => ['nullable', 'integer'],
            'role'        => ['nullable', 'string', 'max:60'],
            'per_page'    => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $query = Captain::query()->with(['member', 'team']);

        if (! empty($validated['active_only'])) {
            $query->active();
        }
        if (array_key_exists('team_id', $validated) && $validated['team_id'] !== null) {
            $query->where('team_id', $validated['team_id']);
        }
        if (! empty($validated['role'])) {
            $query->where('role', $validated['role']);
        }

        $query->orderByDesc('start_date');

        $perPage = (int) ($validated['per_page'] ?? 50);

        return CaptainResource::collection($query->paginate($perPage)->appends($request->query()));
    }
}
