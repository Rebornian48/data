<?php

namespace App\Services;

use App\Models\Generation;
use App\Models\Member;
use App\Models\Single;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    public function overallStats(): array
    {
        return [
            'total_members' => Member::count(),
            'active_members' => Member::active()->count(),
            'graduated_members' => Member::graduated()->count(),
            'total_generations' => Generation::count(),
            'total_singles' => Single::count(),
        ];
    }

    public function membersByGeneration()
    {
        return Generation::withCount([
            'members',
            'members as active_count' => fn ($q) => $q->where('status', 'Aktif'),
            'members as graduated_count' => fn ($q) => $q->where('status', 'Lulus'),
        ])
            ->orderByRaw("CASE WHEN code REGEXP '^[0-9]+$' THEN CAST(code AS UNSIGNED) ELSE 999 END")
            ->get();
    }

    public function longestTenure()
    {
        return Member::with('generation')
            ->whereNotNull('join_date')
            ->get()
            ->sortByDesc('days_in_jkt48')
            ->take(10)
            ->values();
    }

    public function topSenbatsu()
    {
        return Member::with('generation')
            ->withCount('singles')
            ->orderByDesc('singles_count')
            ->limit(10)
            ->get();
    }

    public function topCenter()
    {
        return Member::with('generation')
            ->withCount(['singles as center_count' => fn ($q) => $q->where('member_singles.role', 'center')])
            ->orderByDesc('center_count')
            ->limit(10)
            ->get();
    }

    public function ageDistribution()
    {
        return Member::active()
            ->whereNotNull('birth_date')
            ->get()
            ->groupBy(fn ($m) => $this->ageBucket($m->current_age))
            ->map->count();
    }

    public function topBirthPlaces()
    {
        return Member::whereNotNull('birth_place')
            ->select('birth_place', DB::raw('COUNT(*) as total'))
            ->groupBy('birth_place')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    public function memberGrowth(): array
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

    private function buildGrowthEvents(): array
    {
        $rows = Member::whereNotNull('join_date')
            ->select('status', 'join_date', 'graduation_date', 'cancelled_date')->get();
        $events = [];
        foreach ($rows as $m) {
            $events[] = ['date' => $m->join_date->toDateString(), 'type' => 'join'];
            if ($m->status === 'Aktif') {
                continue;
            }
            $leave = $m->graduation_date;
            if ($m->cancelled_date && (! $leave || $m->cancelled_date->lt($leave))) {
                $leave = $m->cancelled_date;
            }
            if ($leave) {
                $events[] = ['date' => $leave->toDateString(), 'type' => 'leave'];
            }
        }

        return $events;
    }
}
