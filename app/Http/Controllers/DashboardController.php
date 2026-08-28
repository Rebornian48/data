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
    private const MONTH_NAMES = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function index()
    {
        return view('dashboard.index', [
            'stats' => $this->overallStats(),
            'membersByGeneration' => $this->membersByGeneration(),
            'longestTenure' => $this->longestTenure(),
            'topSenbatsu' => $this->topSenbatsu(),
            'topCenter' => $this->topCenter(),
            'activeCaptains' => Captain::with('member.generation')->active()->get(),
            'ageDistribution' => $this->ageDistribution(),
            'birthPlaces' => $this->topBirthPlaces(),
            'memberGrowth' => $this->memberGrowth(),
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
        [$year, $month] = $this->parseCalendarInput($request);
        $first = Carbon::create($year, $month, 1)->startOfDay();

        $events = $this->collectCalendarEvents($year, $month);
        $this->sortDailyEvents($events);

        return view('dashboard.calendar', [
            'year' => $year,
            'month' => $month,
            'monthName' => self::MONTH_NAMES[$month],
            'monthNames' => self::MONTH_NAMES,
            'weeks' => $this->buildWeekGrid($first),
            'events' => $events,
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

    // --- index helpers ---------------------------------------------------

    private function overallStats(): array
    {
        return [
            'total_members' => Member::count(),
            'active_members' => Member::active()->count(),
            'graduated_members' => Member::graduated()->count(),
            'total_generations' => Generation::count(),
            'total_singles' => Single::count(),
        ];
    }

    private function membersByGeneration()
    {
        return Generation::withCount([
            'members',
            'members as active_count' => fn ($q) => $q->where('status', 'Aktif'),
            'members as graduated_count' => fn ($q) => $q->where('status', 'Lulus'),
        ])
            ->orderByRaw("CASE WHEN code REGEXP '^[0-9]+$' THEN CAST(code AS UNSIGNED) ELSE 999 END")
            ->get();
    }

    private function longestTenure()
    {
        return Member::with('generation')
            ->whereNotNull('join_date')
            ->get()
            ->sortByDesc('days_in_jkt48')
            ->take(10)
            ->values();
    }

    private function topSenbatsu()
    {
        return Member::with('generation')
            ->withCount('singles')
            ->orderByDesc('singles_count')
            ->limit(10)
            ->get();
    }

    private function topCenter()
    {
        return Member::with('generation')
            ->withCount(['singles as center_count' => fn ($q) => $q->where('member_singles.role', 'center')])
            ->orderByDesc('center_count')
            ->limit(10)
            ->get();
    }

    private function ageDistribution()
    {
        return Member::active()
            ->whereNotNull('birth_date')
            ->get()
            ->groupBy(fn ($m) => $this->ageBucket($m->current_age))
            ->map->count();
    }

    private function ageBucket(?float $age): string
    {
        return match (true) {
            $age === null => '?',
            $age < 18 => 'Under 18',
            $age < 20 => '18-19',
            $age < 22 => '20-21',
            $age < 25 => '22-24',
            default => '25+',
        };
    }

    private function topBirthPlaces()
    {
        return Member::whereNotNull('birth_place')
            ->select('birth_place', DB::raw('COUNT(*) as total'))
            ->groupBy('birth_place')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    private function memberGrowth(): array
    {
        $events = $this->buildGrowthEvents();
        usort($events, fn ($a, $b) => $a['date'] <=> $b['date'] ?: $a['type'] <=> $b['type']);

        $active = 0;
        $growth = [];
        $lastDate = null;
        foreach ($events as $e) {
            if ($lastDate && $lastDate !== $e['date']) {
                $growth[] = ['date' => $lastDate, 'total' => $active];
            }
            $active += $e['type'] === 'join' ? 1 : -1;
            $lastDate = $e['date'];
        }
        if ($lastDate) {
            $growth[] = ['date' => $lastDate, 'total' => $active];
        }

        return $growth;
    }

    private function buildGrowthEvents(): array
    {
        $rows = Member::whereNotNull('join_date')->select('join_date', 'graduation_date', 'cancelled_date')->get();
        $events = [];
        foreach ($rows as $m) {
            $events[] = ['date' => $m->join_date->toDateString(), 'type' => 'join'];
            if ($m->graduation_date) {
                $events[] = ['date' => $m->graduation_date->toDateString(), 'type' => 'leave'];
            }
            if ($m->cancelled_date) {
                $events[] = ['date' => $m->cancelled_date->toDateString(), 'type' => 'leave'];
            }
        }

        return $events;
    }

    // --- calendar helpers ------------------------------------------------

    private function parseCalendarInput(Request $request): array
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

        return [$year, $month];
    }

    private function collectCalendarEvents(int $year, int $month): array
    {
        $events = [];
        $push = function ($date, array $entry) use (&$events) {
            $key = $date instanceof Carbon ? $date->toDateString() : $date;
            $events[$key][] = $entry;
        };

        $this->pushBirthdayEvents($push, $year, $month);
        $this->pushGenerationEvents($push, $year, $month);
        $this->pushGraduationEvents($push, $year, $month);

        return $events;
    }

    private function pushBirthdayEvents(callable $push, int $year, int $month): void
    {
        $members = Member::whereNotNull('birth_date')->get();
        foreach ($members as $m) {
            $bd = $m->birth_date;
            if ($bd->month !== $month) {
                continue;
            }
            try {
                $target = Carbon::create($year, $month, $bd->day);
            } catch (\Exception) {
                continue;
            }
            if (! $target || $target->month !== $month) {
                continue;
            }

            $join = $m->effective_join_date;
            $grad = $m->graduation_date;
            $wasActive = $join && $join->lte($target) && (! $grad || $grad->gte($target));
            if (! $wasActive && $m->status !== 'Aktif') {
                continue;
            }

            $push($target, [
                'type' => 'birthday',
                'label' => $m->name.' ('.($year - $bd->year).')',
                'url' => route('members.show', $m),
            ]);
        }
    }

    private function pushGenerationEvents(callable $push, int $year, int $month): void
    {
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
    }

    private function pushGraduationEvents(callable $push, int $year, int $month): void
    {
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
    }

    private function sortDailyEvents(array &$events): void
    {
        $order = ['gen' => 0, 'graduate' => 1, 'birthday' => 2];
        foreach (array_keys($events) as $k) {
            usort($events[$k], fn ($a, $b) => $order[$a['type']] <=> $order[$b['type']]);
        }
    }

    private function buildWeekGrid(Carbon $first): array
    {
        $cells = array_fill(0, $first->dayOfWeek, null);
        for ($d = 1; $d <= $first->daysInMonth; $d++) {
            $cells[] = $d;
        }
        $pad = (7 - count($cells) % 7) % 7;
        for ($i = 0; $i < $pad; $i++) {
            $cells[] = null;
        }

        return array_chunk($cells, 7);
    }
}
