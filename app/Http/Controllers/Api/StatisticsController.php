<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Generation;
use App\Models\Member;
use App\Models\Single;
use App\Models\Team;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    public function index(): JsonResponse
    {
        $totalMembers     = Member::count();
        $activeMembers    = Member::where('status', 'Aktif')->count();
        $graduatedMembers = Member::where('status', 'Lulus')->count();
        $traineeMembers   = Member::where('status', 'Trainee')->count();

        $perGeneration = Generation::withCount(['members', 'activeMembers', 'graduatedMembers'])
            ->orderBy('join_date')
            ->get()
            ->map(fn ($g) => [
                'id'                => $g->id,
                'code'              => $g->code,
                'name'              => $g->name,
                'join_date'         => optional($g->join_date)?->toDateString(),
                'members_count'     => (int) $g->members_count,
                'active_count'      => (int) $g->active_members_count,
                'graduated_count'   => (int) $g->graduated_members_count,
            ]);

        return response()->json([
            'data' => [
                'totals' => [
                    'members'   => $totalMembers,
                    'active'    => $activeMembers,
                    'graduated' => $graduatedMembers,
                    'trainee'   => $traineeMembers,
                    'singles'   => Single::count(),
                    'teams'     => Team::count(),
                    'generations' => Generation::count(),
                ],
                'per_generation' => $perGeneration,
            ],
        ]);
    }
}
