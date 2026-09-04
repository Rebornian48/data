<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Captain;
use App\Models\Generation;
use App\Models\Member;
use App\Models\Setlist;
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
            'activeCaptains' => Captain::with(['member.generation', 'team'])->active()->get(),
            'ageDistribution' => $this->stats->ageDistribution(),
            'birthPlaces' => $this->stats->topBirthPlaces(),
            'memberGrowth' => $this->stats->memberGrowth(),
        ]);
    }

    public function member(Member $member)
    {
        $member->load(['generation', 'singles', 'captains.team', 'teamHistory.team']);

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

    public function single(Single $single)
    {
        $single->load([
            'members' => fn ($q) => $q->with('generation')->orderBy('name'),
            'songs' => fn ($q) => $q->orderBy('external_id'),
            'couplingSongs.members' => fn ($q) => $q->with('generation'),
        ]);

        $centers = $single->members->filter(fn ($m) => $m->pivot->role === 'center')->values();
        $senbatsu = $single->members->filter(fn ($m) => $m->pivot->role !== 'center')->values();

        return view('dashboard.single', compact('single', 'centers', 'senbatsu'));
    }

    public function albums()
    {
        $albums = Album::withCount('tracks')
            ->orderBy('type')
            ->orderBy('sequence')
            ->get();

        return view('dashboard.albums', compact('albums'));
    }

    public function album(Album $album)
    {
        $album->load(['tracks' => fn ($q) => $q->with('song')->orderBy('position')]);

        return view('dashboard.album', compact('album'));
    }

    public function setlists()
    {
        $setlists = Setlist::withCount('songs')
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        return view('dashboard.setlists', compact('setlists'));
    }

    public function setlist(Setlist $setlist)
    {
        $setlist->load(['songs' => fn ($q) => $q->with('single')->orderBy('setlist_songs.position')]);

        return view('dashboard.setlist', compact('setlist'));
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
        $displayCodes = array_merge($numericCodes, ['V1','V2']);
        $labels = ['V1' => 'Virtual Gen 1', 'V2' => 'Virtual Gen 2'];

        $bucketize = function (\Illuminate\Support\Collection $byGenId) use ($gens, $displayCodes) {
            $out = [];
            foreach ($displayCodes as $code) {
                $gen = $gens->get($code);
                $out[$code] = $gen ? (int) ($byGenId[$gen->id] ?? 0) : 0;
            }

            return $out;
        };

        $current = $bucketize($activeByGen);
        $historical = $bucketize($allByGen);
        $survivors = $bucketize($survivorsByGen);
        $survivorsActive = $bucketize($survivorsStillActiveByGen);

        $rows = [];
        foreach ($displayCodes as $code) {
            $label = $labels[$code] ?? 'Generasi '.$code;
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
            'V1' => '30 Juli 2023',
            'V2' => '1 Januari 2024',
        ];

        $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        foreach ($displayCodes as $code) {
            if (!empty($formationDates[$code])) {
                continue;
            }
            $gen = $gens->get($code);
            $d = $gen?->join_date ?? $gen?->announcement_date ?? null;
            if ($d) {
                $formationDates[$code] = $d->day.' '.$bulan[(int)$d->month].' '.$d->year;
            }
        }

        $kaigaiCodes = ['Kaigai 1', 'Kaigai 2', 'Transfer'];
        $merged = ['label' => 'Kaigai dan Transfer', 'active' => 0, 'total' => 0, 'survivors' => 0, 'survivorsActive' => 0];
        $mergedFormation = [];
        foreach ($kaigaiCodes as $code) {
            $gen = $gens->get($code);
            if (! $gen) {
                continue;
            }
            $merged['active'] += (int) ($activeByGen[$gen->id] ?? 0);
            $merged['total'] += (int) ($allByGen[$gen->id] ?? 0);
            $merged['survivors'] += (int) ($survivorsByGen[$gen->id] ?? 0);
            $merged['survivorsActive'] += (int) ($survivorsStillActiveByGen[$gen->id] ?? 0);
            $d = $gen->join_date;
            if ($d) {
                $mergedFormation[] = $code.': '.$d->day.' '.$bulan[(int)$d->month].' '.$d->year;
            }
        }
        if ($merged['total'] > 0 || $mergedFormation) {
            $rows['KAIGAI_TRANSFER'] = $merged;
            $formationDates['KAIGAI_TRANSFER'] = implode(' · ', $mergedFormation);
            $totals['active'] += $merged['active'];
            $totals['all'] += $merged['total'];
            $totals['survivors'] += $merged['survivors'];
            $totals['survivorsActive'] += $merged['survivorsActive'];
        }

        $ageStats = $this->buildAgeStats($gens, $displayCodes, $labels);
        $kaigaiAgeStat = $this->buildMergedAgeStat($gens, $kaigaiCodes, 'Kaigai dan Transfer');
        if ($kaigaiAgeStat) {
            $ageStats['KAIGAI_TRANSFER'] = $kaigaiAgeStat;
        }

        return view('dashboard.statistik', compact('rows', 'totals', 'formationDates', 'ageStats'));
    }

    private function buildMergedAgeStat($gens, array $codes, string $label): ?array
    {
        $genIds = [];
        foreach ($codes as $code) {
            $gen = $gens->get($code);
            if ($gen) {
                $genIds[] = $gen->id;
            }
        }
        if (! $genIds) {
            return null;
        }

        $members = Member::whereIn('generation_id', $genIds)
            ->whereNotNull('birth_date')
            ->whereNotNull('join_date')
            ->get();
        if ($members->isEmpty()) {
            return null;
        }

        $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $fmt = fn ($d) => $d ? $d->day.' '.$bulan[(int)$d->month].' '.$d->year : '';
        $ageFmt = fn ($years) => number_format((float) $years, 2, ',', '');
        $ageNowOrGrad = fn ($m) => $ageFmt($m->birth_date->floatDiffInYears($m->graduation_date ?? now()));
        $ageNow = fn ($m) => $ageFmt($m->birth_date->floatDiffInYears(now()));

        $oldest = $members->sortByDesc(fn ($m) => $m->birth_date->timestamp * -1)->first();
        $youngest = $members->sortByDesc(fn ($m) => $m->birth_date->timestamp)->first();

        $active = $members->where('status', 'Aktif');
        $oldestActive = $active->isEmpty() ? null : $active->sortBy(fn ($m) => $m->birth_date->timestamp)->first();
        $youngestActive = $active->isEmpty() ? null : $active->sortByDesc(fn ($m) => $m->birth_date->timestamp)->first();

        $graduated = $members->filter(fn ($m) => $m->graduation_date && $m->join_date);
        $fastest = $graduated->isEmpty() ? null : $graduated->sortBy(fn ($m) => $m->join_date->diffInDays($m->graduation_date))->first();
        $latest = $graduated->isEmpty() ? null : $graduated->sortByDesc(fn ($m) => $m->graduation_date->timestamp)->first();

        $mkAge = fn ($m) => $m ? "{$m->name} ({$ageNowOrGrad($m)} tahun)" : '—';
        $mkAgeActive = fn ($m) => $m ? "{$m->name} ({$ageNow($m)} tahun)" : '—';
        $daysFmt = fn ($m) => number_format((int) $m->join_date->diffInDays($m->graduation_date), 0, ',', '.');
        $mkDate = fn ($m) => $m ? "{$m->name} ({$fmt($m->graduation_date)}. lulus dalam {$daysFmt($m)} hari)" : '—';

        return [
            'label' => $label,
            'oldest' => $mkAge($oldest),
            'youngest' => $mkAge($youngest),
            'oldestActive' => $mkAgeActive($oldestActive),
            'youngestActive' => $mkAgeActive($youngestActive),
            'fastestGrad' => $mkDate($fastest),
            'latestGrad' => $mkDate($latest),
        ];
    }

    private function buildAgeStats($gens, array $displayCodes, array $labels): array
    {
        $members = Member::with('generation')
            ->whereNotNull('birth_date')
            ->whereNotNull('join_date')
            ->get();

        $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $fmt = fn ($d) => $d ? $d->day.' '.$bulan[(int)$d->month].' '.$d->year : '';

        $ageFmt = fn ($years) => number_format((float) $years, 2, ',', '');
        $ageNowOrGrad = fn ($m) => $ageFmt($m->birth_date->floatDiffInYears($m->graduation_date ?? now()));
        $ageNow = fn ($m) => $ageFmt($m->birth_date->floatDiffInYears(now()));

        $anyMemberByGen = Member::selectRaw('generation_id, count(*) as c')
            ->groupBy('generation_id')->pluck('c', 'generation_id');

        $out = [];
        foreach ($displayCodes as $code) {
            $gen = $gens->get($code);
            if (! $gen) {
                continue;
            }
            if (($anyMemberByGen[$gen->id] ?? 0) === 0) {
                continue;
            }
            $genMembers = $members->where('generation_id', $gen->id);

            $oldest = $genMembers->isEmpty() ? null : $genMembers->sortByDesc(fn ($m) => $m->birth_date->timestamp * -1)->first();
            $youngest = $genMembers->isEmpty() ? null : $genMembers->sortByDesc(fn ($m) => $m->birth_date->timestamp)->first();

            $active = $genMembers->where('status', 'Aktif');
            $oldestActive = $active->isEmpty() ? null : $active->sortBy(fn ($m) => $m->birth_date->timestamp)->first();
            $youngestActive = $active->isEmpty() ? null : $active->sortByDesc(fn ($m) => $m->birth_date->timestamp)->first();

            $graduated = $genMembers->filter(fn ($m) => $m->graduation_date && $m->join_date);
            $fastest = $graduated->isEmpty() ? null : $graduated->sortBy(fn ($m) => $m->join_date->diffInDays($m->graduation_date))->first();
            $latest = $graduated->isEmpty() ? null : $graduated->sortByDesc(fn ($m) => $m->graduation_date->timestamp)->first();

            $mkAge = fn ($m) => $m ? "{$m->name} ({$ageNowOrGrad($m)} tahun)" : '—';
            $mkAgeActive = fn ($m) => $m ? "{$m->name} ({$ageNow($m)} tahun)" : '—';
            $daysFmt = fn ($m) => number_format((int) $m->join_date->diffInDays($m->graduation_date), 0, ',', '.');
            $mkDate = fn ($m) => $m ? "{$m->name} ({$fmt($m->graduation_date)}. lulus dalam {$daysFmt($m)} hari)" : '—';

            $out[$code] = [
                'label' => $labels[$code] ?? 'Generasi '.$code,
                'oldest' => $mkAge($oldest),
                'youngest' => $mkAge($youngest),
                'oldestActive' => $mkAgeActive($oldestActive),
                'youngestActive' => $mkAgeActive($youngestActive),
                'fastestGrad' => $mkDate($fastest),
                'latestGrad' => $mkDate($latest),
            ];
        }

        return $out;
    }

    public function captains()
    {
        $captains = Captain::with(['member.generation', 'team'])
            ->orderBy('start_date')
            ->get();

        $positions = $captains->groupBy('position')
            ->map(fn ($g) => $g->sortBy('start_date')->values())
            ->sortKeys();

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
