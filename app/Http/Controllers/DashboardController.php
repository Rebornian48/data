<?php

namespace App\Http\Controllers;

use App\Models\Captain;
use App\Models\Generation;
use App\Models\Member;
use App\Models\Single;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Main public dashboard.
     */
    public function index()
    {
        // Overall stats
        $stats = [
            'total_members' => Member::count(),
            'active_members' => Member::active()->count(),
            'graduated_members' => Member::graduated()->count(),
            'total_generations' => Generation::count(),
            'total_singles' => Single::count(),
        ];

        // Members per generation
        $membersByGeneration = Generation::withCount([
            'members',
            'members as active_count' => fn ($q) => $q->where('status', 'Aktif'),
            'members as graduated_count' => fn ($q) => $q->where('status', 'Lulus'),
        ])
        ->orderByRaw("
            CASE
                WHEN code REGEXP '^[0-9]+$' THEN CAST(code AS UNSIGNED)
                ELSE 999
            END
        ")
        ->get();

        // Top 10 members by tenure
        $longestTenure = Member::with('generation')
            ->whereNotNull('join_date')
            ->get()
            ->sortByDesc('days_in_jkt48')
            ->take(10)
            ->values();

        // Top 10 by senbatsu count
        $topSenbatsu = Member::with('generation')
            ->withCount('singles')
            ->orderByDesc('singles_count')
            ->limit(10)
            ->get();

        // Top by center count
        $topCenter = Member::with('generation')
            ->withCount(['singles as center_count' => function ($q) {
                $q->where('member_singles.role', 'center');
            }])
            ->orderByDesc('center_count')
            ->limit(10)
            ->get();

        // Currently active captains
        $activeCaptains = Captain::with('member.generation')
            ->active()
            ->get();

        // Age distribution of active members
        $ageDistribution = Member::active()
            ->whereNotNull('birth_date')
            ->get()
            ->groupBy(function ($m) {
                $age = $m->current_age;
                if ($age < 18) return 'Under 18';
                if ($age < 20) return '18-19';
                if ($age < 22) return '20-21';
                if ($age < 25) return '22-24';
                return '25+';
            })
            ->map->count();

        // Birthplace distribution (top 10)
        $birthPlaces = Member::whereNotNull('birth_place')
            ->select('birth_place', DB::raw('COUNT(*) as total'))
            ->groupBy('birth_place')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'membersByGeneration',
            'longestTenure',
            'topSenbatsu',
            'topCenter',
            'activeCaptains',
            'ageDistribution',
            'birthPlaces'
        ));
    }

    /**
     * Member detail public page.
     */
    public function member(Member $member)
    {
        $member->load(['generation', 'singles', 'captains']);
        return view('dashboard.member', compact('member'));
    }

    /**
     * Public members list with search + filter.
     */
    public function members(Request $request)
    {
        $query = Member::with('generation')
            ->search($request->input('q'));

        if ($request->filled('generation')) {
            $query->where('generation_id', $request->input('generation'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $members = $query->orderBy('name')->paginate(24)->withQueryString();
        $generations = Generation::orderBy('id')->get();

        return view('dashboard.members', compact('members', 'generations'));
    }
}
