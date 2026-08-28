<?php

namespace App\Http\Controllers;

use App\Models\Captain;
use App\Models\Generation;
use App\Models\Member;
use App\Models\Single;
use Carbon\Carbon;
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
                if ($age < 18) {
                    return 'Under 18';
                }
                if ($age < 20) {
                    return '18-19';
                }
                if ($age < 22) {
                    return '20-21';
                }
                if ($age < 25) {
                    return '22-24';
                }

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

        $allMembers = Member::whereNotNull('join_date')->select('join_date', 'graduation_date', 'cancelled_date')->get();
        $events = [];
        foreach ($allMembers as $m) {
            $events[] = ['date' => $m->join_date->toDateString(), 'type' => 'join'];
            if ($m->graduation_date) {
                $events[] = ['date' => $m->graduation_date->toDateString(), 'type' => 'leave'];
            }
            if ($m->cancelled_date) {
                $events[] = ['date' => $m->cancelled_date->toDateString(), 'type' => 'leave'];
            }
        }
        usort($events, fn ($a, $b) => $a['date'] <=> $b['date'] ?: $a['type'] <=> $b['type']);

        $active = 0;
        $memberGrowth = [];
        $lastDate = null;
        foreach ($events as $e) {
            if ($lastDate && $lastDate !== $e['date']) {
                $memberGrowth[] = ['date' => $lastDate, 'total' => $active];
            }
            $active += $e['type'] === 'join' ? 1 : -1;
            $lastDate = $e['date'];
        }
        if ($lastDate) {
            $memberGrowth[] = ['date' => $lastDate, 'total' => $active];
        }

        return view('dashboard.index', compact(
            'stats',
            'membersByGeneration',
            'longestTenure',
            'topSenbatsu',
            'topCenter',
            'activeCaptains',
            'ageDistribution',
            'birthPlaces',
            'memberGrowth'
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

    public function singles()
    {
        $singles = Single::withCount('members')->orderBy('sequence')->get();

        return view('dashboard.singles', compact('singles'));
    }

    public function restrukturisasi()
    {
        $start = '2021-03-11';
        $end = '2021-03-14';

        $members = Member::with('generation')
            ->whereBetween('graduation_date', [$start, $end])
            ->orderBy('name')
            ->get();

        $generations = $members->groupBy('generation_id')->map(function ($group) {
            $gen = $group->first()->generation;
            $gen->members_count = $group->count();

            return $gen;
        })->sortBy(function ($gen) {
            return is_numeric($gen->code) ? (int) $gen->code : 999;
        })->values();

        return view('dashboard.restrukturisasi', compact('generations', 'members'));
    }

    public function calendar(Request $request)
    {
        $now = now();
        $year = (int) $request->input('y', $now->year);
        $month = (int) $request->input('m', $now->month);
        if ($month < 1 || $month > 12) {
            $month = $now->month;
        }
        if ($year < 1900 || $year > 2100) {
            $year = $now->year;
        }

        $first = Carbon::create($year, $month, 1)->startOfDay();
        $last = $first->copy()->endOfMonth();

        // Bucket keyed by 'YYYY-MM-DD'
        $events = [];
        $push = function ($date, $entry) use (&$events) {
            $key = $date instanceof Carbon ? $date->toDateString() : $date;
            $events[$key][] = $entry;
        };

        // 1) Birthdays of members who were active on that date (or currently active)
        $members = Member::whereNotNull('birth_date')->get();
        foreach ($members as $m) {
            $bd = $m->birth_date;
            if ($bd->month !== $month) {
                continue;
            }

            try {
                $target = Carbon::create($year, $month, $bd->day);
            } catch (\Exception $e) {
                continue;
            }
            if (! $target || $target->month !== $month) {
                continue;
            }

            $join = $m->effective_join_date;
            $grad = $m->graduation_date;
            $wasActiveOn = $join && $join->lte($target) && (! $grad || $grad->gte($target));
            $stillActive = $m->status === 'Aktif';
            if (! $wasActiveOn && ! $stillActive) {
                continue;
            }

            $push($target, [
                'type' => 'birthday',
                'label' => $m->name.' ('.($year - $bd->year).')',
                'url' => route('members.show', $m),
            ]);
        }

        // 2) Generations that joined in this month/year
        $gens = Generation::whereNotNull('join_date')
            ->whereYear('join_date', $year)
            ->whereMonth('join_date', $month)
            ->get();
        foreach ($gens as $g) {
            $push($g->join_date, [
                'type' => 'gen',
                'label' => $g->code.' masuk'.($g->name ? ' ('.$g->name.')' : ''),
                'url' => null,
            ]);
        }

        // 3) Members who graduated in this month/year
        $grads = Member::whereNotNull('graduation_date')
            ->whereYear('graduation_date', $year)
            ->whereMonth('graduation_date', $month)
            ->get();
        foreach ($grads as $m) {
            $push($m->graduation_date, [
                'type' => 'graduate',
                'label' => $m->name.' lulus',
                'url' => route('members.show', $m),
            ]);
        }

        // Sort each day so gens > graduates > birthdays reads first
        $order = ['gen' => 0, 'graduate' => 1, 'birthday' => 2];
        foreach (array_keys($events) as $k) {
            usort($events[$k], fn ($a, $b) => $order[$a['type']] <=> $order[$b['type']]);
        }

        // Build weeks grid (Sun-first)
        $startDow = $first->dayOfWeek; // 0 = Sunday
        $daysInMonth = $first->daysInMonth;
        $cells = [];
        for ($i = 0; $i < $startDow; $i++) {
            $cells[] = null;
        }
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $cells[] = $d;
        }
        $pad = (7 - count($cells) % 7) % 7;
        for ($i = 0; $i < $pad; $i++) {
            $cells[] = null;
        }
        $weeks = array_chunk($cells, 7);

        $prev = $first->copy()->subMonth();
        $next = $first->copy()->addMonth();

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return view('dashboard.calendar', [
            'year' => $year,
            'month' => $month,
            'monthName' => $monthNames[$month],
            'monthNames' => $monthNames,
            'weeks' => $weeks,
            'events' => $events,
            'prev' => $prev,
            'next' => $next,
            'today' => $now->toDateString(),
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
