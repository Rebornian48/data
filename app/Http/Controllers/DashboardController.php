<?php

namespace App\Http\Controllers;

use App\Models\Captain;
use App\Models\Generation;
use App\Models\Member;
use App\Models\Single;
use App\Services\CalendarService;
use App\Services\DashboardStatsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardStatsService $stats,
        private readonly CalendarService $calendar,
    ) {}

    public function index()
    {
        return view('dashboard.index', [
            'stats' => $this->stats->overallStats(),
            'membersByGeneration' => $this->stats->membersByGeneration(),
            'longestTenure' => $this->stats->longestTenure(),
            'topSenbatsu' => $this->stats->topSenbatsu(),
            'topCenter' => $this->stats->topCenter(),
            'activeCaptains' => Captain::with('member.generation')->active()->get(),
            'ageDistribution' => $this->stats->ageDistribution(),
            'birthPlaces' => $this->stats->topBirthPlaces(),
            'memberGrowth' => $this->stats->memberGrowth(),
        ]);
    }

    public function member(Member $member)
    {
        $member->load(['generation', 'singles', 'captains']);

        return view('dashboard.member', compact('member'));
    }

    public function members(Request $request)
    {
        $query = Member::with('generation')->search($request->input('q'));

        if ($request->filled('generation')) {
            $query->where('generation_id', $request->input('generation'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return view('dashboard.members', [
            'members' => $query->orderBy('name')->paginate(24)->withQueryString(),
            'generations' => Generation::orderBy('id')->get(),
        ]);
    }

    public function singles()
    {
        $singles = Single::withCount('members')->orderBy('sequence')->get();

        return view('dashboard.singles', compact('singles'));
    }

    public function restrukturisasi()
    {
        $members = Member::with('generation')
            ->whereBetween('graduation_date', ['2021-03-11', '2021-03-14'])
            ->orderBy('name')
            ->get();

        $generations = $members->groupBy('generation_id')->map(function ($group) {
            $gen = $group->first()->generation;
            $gen->members_count = $group->count();

            return $gen;
        })->sortBy(fn ($gen) => is_numeric($gen->code) ? (int) $gen->code : 999)->values();

        return view('dashboard.restrukturisasi', compact('generations', 'members'));
    }

    public function calendar(Request $request)
    {
        [$year, $month] = $this->calendar->parseInput($request);
        $first = Carbon::create($year, $month, 1)->startOfDay();

        return view('dashboard.calendar', [
            'year' => $year,
            'month' => $month,
            'monthName' => CalendarService::MONTH_NAMES[$month],
            'monthNames' => CalendarService::MONTH_NAMES,
            'weeks' => $this->calendar->buildWeekGrid($first),
            'events' => $this->calendar->collectEvents($year, $month),
            'prev' => $first->copy()->subMonth(),
            'next' => $first->copy()->addMonth(),
            'today' => now()->toDateString(),
        ]);
    }

    public function captains()
    {
        $captains = Captain::with('member.generation')
            ->orderBy('position')
            ->orderBy('start_date')
            ->get();

        $positions = $captains->groupBy('position')->map(fn ($g) => $g->sortBy('start_date')->values());

        $timelineData = $captains->map(fn ($c) => [
            'position' => $c->position,
            'member' => $c->member->name,
            'start' => $c->start_date->format('Y-m-d'),
            'end' => $c->end_date ? $c->end_date->format('Y-m-d') : now()->format('Y-m-d'),
            'active' => $c->end_date === null,
        ])->sortBy('start')->values();

        return view('dashboard.captains', compact('captains', 'positions', 'timelineData'));
    }
}
