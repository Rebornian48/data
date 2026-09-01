<?php

namespace App\Services\Notifications;

use App\Models\Member;
use App\Services\CalendarService;
use Carbon\Carbon;

class CommandRouter
{
    public function __construct(private readonly CalendarService $calendar) {}

    /**
     * Dispatch text command. Returns plain-text reply (or null when command unknown / not addressed).
     * $platform: 'telegram' | 'discord' — mostly for formatting toggles.
     */
    public function handle(string $text, string $platform = 'telegram'): ?string
    {
        $text = trim($text);
        if ($text === '' || $text[0] !== '/') {
            return null;
        }

        $parts = preg_split('/\s+/', $text, 2);
        $cmd = strtolower(ltrim($parts[0], '/'));
        $cmd = explode('@', $cmd)[0];
        $args = $parts[1] ?? '';

        return match ($cmd) {
            'start', 'help' => $this->help(),
            'ultah', 'birthday' => $this->birthdaysToday(),
            'lulus', 'graduation' => $this->graduationsMonth(),
            'member', 'cari' => $this->findMember($args),
            'kalender', 'jadwal' => $this->calendarThisMonth(),
            default => null,
        };
    }

    private function help(): string
    {
        return "Command tersedia:\n"
            ."/ultah — daftar member ulang tahun hari ini\n"
            ."/lulus — daftar kelulusan bulan ini\n"
            ."/member <nama> — cari member\n"
            ."/jadwal — event bulan ini\n"
            ."/help — bantuan";
    }

    private function birthdaysToday(): string
    {
        $today = now();
        $members = Member::whereNotNull('birth_date')
            ->whereRaw('MONTH(birth_date) = ?', [$today->month])
            ->whereRaw('DAY(birth_date) = ?', [$today->day])
            ->where('status', 'Aktif')
            ->orderBy('name')
            ->get();

        if ($members->isEmpty()) {
            return '🎂 Tidak ada member yang berulang tahun hari ini.';
        }

        $lines = ['🎂 Ulang tahun hari ini:'];
        foreach ($members as $m) {
            $age = $today->year - $m->birth_date->year;
            $lines[] = "• {$m->name} ({$age} thn)";
        }

        return implode("\n", $lines);
    }

    private function graduationsMonth(): string
    {
        $now = now();
        $members = Member::whereNotNull('graduation_date')
            ->whereYear('graduation_date', $now->year)
            ->whereMonth('graduation_date', $now->month)
            ->orderBy('graduation_date')
            ->get();

        if ($members->isEmpty()) {
            return '🎓 Tidak ada kelulusan bulan ini.';
        }

        $lines = ['🎓 Kelulusan bulan '.CalendarService::MONTH_NAMES[$now->month].':'];
        foreach ($members as $m) {
            $lines[] = '• '.$m->graduation_date->format('d M').' — '.$m->name;
        }

        return implode("\n", $lines);
    }

    private function findMember(string $q): string
    {
        $q = trim($q);
        if ($q === '') {
            return 'Gunakan: /member <nama>';
        }
        $members = Member::search($q)->with('generation')->limit(5)->get();
        if ($members->isEmpty()) {
            return "Tidak ditemukan member dengan kata kunci: {$q}";
        }
        $lines = ["Hasil pencarian \"{$q}\":"];
        foreach ($members as $m) {
            $gen = $m->generation?->code ?? '-';
            $url = route('members.show', $m);
            $lines[] = "• {$m->name} [{$gen}] — {$m->status}\n  {$url}";
        }

        return implode("\n", $lines);
    }

    private function calendarThisMonth(): string
    {
        $now = now();
        $events = $this->calendar->collectEvents($now->year, $now->month);
        if (! $events) {
            return 'Tidak ada event bulan ini.';
        }
        ksort($events);
        $lines = ['📅 Event bulan '.CalendarService::MONTH_NAMES[$now->month].':'];
        foreach ($events as $date => $items) {
            $d = Carbon::parse($date)->format('d M');
            foreach ($items as $ev) {
                $emoji = match ($ev['type']) {
                    'birthday' => '🎂', 'graduate' => '🎓', 'gen' => '⭐', default => '•',
                };
                $lines[] = "{$d} {$emoji} {$ev['label']}";
            }
        }

        return implode("\n", $lines);
    }
}
