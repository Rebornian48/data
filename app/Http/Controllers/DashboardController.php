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

    public function statistik()
    {
        $newEraDate = '2021-03-14';

        $gens = Generation::orderBy('id')->get()->keyBy('code');

        $activeByGen = Member::where('status', 'Aktif')
            ->selectRaw('generation_id, count(*) as c')
            ->groupBy('generation_id')->pluck('c', 'generation_id');

        $allByGen = Member::selectRaw('generation_id, count(*) as c')
            ->groupBy('generation_id')->pluck('c', 'generation_id');

        $survivorsByGen = Member::whereDate('join_date', '<=', $newEraDate)
            ->where(function ($q) use ($newEraDate) {
                $q->whereNull('graduation_date')->orWhereDate('graduation_date', '>', $newEraDate);
            })
            ->selectRaw('generation_id, count(*) as c')
            ->groupBy('generation_id')->pluck('c', 'generation_id');

        $survivorsStillActiveByGen = Member::where('status', 'Aktif')
            ->whereDate('join_date', '<=', $newEraDate)
            ->selectRaw('generation_id, count(*) as c')
            ->groupBy('generation_id')->pluck('c', 'generation_id');

        $numericCodes = ['1','2','3','4','5','6','7','8','9','10','11','12','13','14'];
        $vCodes = ['V1','V2'];

        $bucketize = function (\Illuminate\Support\Collection $byGenId) use ($gens, $numericCodes, $vCodes) {
            $out = [];
            foreach ($numericCodes as $code) {
                $gen = $gens->get($code);
                $out[$code] = $gen ? (int) ($byGenId[$gen->id] ?? 0) : 0;
            }
            $vTotal = 0;
            foreach ($vCodes as $code) {
                $gen = $gens->get($code);
                if ($gen) {
                    $vTotal += (int) ($byGenId[$gen->id] ?? 0);
                }
            }
            $out['V'] = $vTotal;

            return $out;
        };

        $current = $bucketize($activeByGen);
        $historical = $bucketize($allByGen);
        $survivors = $bucketize($survivorsByGen);
        $survivorsActive = $bucketize($survivorsStillActiveByGen);

        $rows = [];
        foreach (array_merge($numericCodes, ['V']) as $code) {
            $label = $code === 'V' ? 'JKT48V' : 'Generasi '.$code;
            $rows[$code] = [
                'label' => $label,
                'active' => $current[$code],
                'total' => $historical[$code],
                'survivors' => $survivors[$code],
                'survivorsActive' => $survivorsActive[$code],
            ];
        }

        $totals = [
            'active' => array_sum($current),
            'all' => array_sum($historical),
            'survivors' => array_sum($survivors),
            'survivorsActive' => array_sum($survivorsActive),
        ];

        $formationDates = [
            '1' => '2 November 2011',
            '2' => '3 November 2012',
            '3' => '15 Maret 2014',
            '4' => '16 Mei 2015',
            '5' => '28 Mei 2016',
            '6' => '8 April 2018',
            '7' => '29 September 2018',
            '8' => '27 April 2019',
            '9' => '1 Desember 2019',
            '10' => '27 Agustus 2020, dibubarkan 4 Desember 2020, dibentuk kembali pada 18 Desember 2021',
            '11' => '31 Oktober 2022',
            '12' => '18 November 2023',
            '13' => '31 Oktober 2024',
            'V' => '22 Agustus 2023',
        ];

        return view('dashboard.statistik', compact('rows', 'totals', 'formationDates'));
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
