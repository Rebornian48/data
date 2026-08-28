<?php

namespace App\Services;

use App\Models\Generation;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarService
{
    public const MONTH_NAMES = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function parseInput(Request $request): array
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

    public function collectEvents(int $year, int $month): array
    {
        $events = [];
        $push = function ($date, array $entry) use (&$events) {
            $key = $date instanceof Carbon ? $date->toDateString() : $date;
            $events[$key][] = $entry;
        };

        $this->pushBirthdayEvents($push, $year, $month);
        $this->pushGenerationEvents($push, $year, $month);
        $this->pushGraduationEvents($push, $year, $month);
        $this->sortDailyEvents($events);

        return $events;
    }

    public function buildWeekGrid(Carbon $first): array
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

    private function pushBirthdayEvents(callable $push, int $year, int $month): void
    {
        foreach (Member::whereNotNull('birth_date')->get() as $m) {
            $target = $this->birthdayTarget($m->birth_date, $year, $month);
            if (! $target) {
                continue;
            }
            $push($target, [
                'type' => 'birthday',
                'label' => $m->name.' ('.($year - $m->birth_date->year).')',
                'url' => route('members.show', $m),
            ]);
        }
    }

    private function birthdayTarget(Carbon $bd, int $year, int $month): ?Carbon
    {
        if ($bd->month !== $month) {
            return null;
        }
        try {
            $target = Carbon::create($year, $month, $bd->day);
        } catch (\Exception) {
            return null;
        }

        return ($target && $target->month === $month) ? $target : null;
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
}
