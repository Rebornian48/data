<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class GraduationUpdateSeeder extends Seeder
{
    public function run(): void
    {
        // [firstPart, lastPart, restructure_status, announce_date, announce_event, graduation_date]
        $data = [
            ['Shani',    'Natio',   'JKT48',      '2023-07-02', 'Summer Fest 2023',                       '2024-05-05'],
            ['Azizi',    'Asadel',  'JKT48',      '2024-04-07', 'ANAK KING',                              '2024-08-25'],
            ['Reva',     'Fidela',  'JKT48',      '2024-06-06', 'Aturan Anti Cinta',                      '2024-10-12'],
            ['Indira',   'Seruni',  'JKT48',      '2025-04-26', 'Aturan Anti Cinta',                      '2025-09-12'],
            ['Shania',   'Gracia',  'JKT48',      '2025-07-26', 'JKT48 Special Concert FULL HOUSE',       '2025-12-27'],
            ['Amanda',   'Sukma',   'Team Dream', '2025-10-21', 'Sambil Menggandeng Erat Tanganku',       '2026-03-29'],
            ['Chelsea',  'Davina',  'Team Dream', '2026-04-01', 'Pertaruhan Cinta',                       '2026-05-30'],
            ['Cathleen', 'Nixie',   'Team Love',  '2026-04-19', 'Cara Meminum Ramune',                    '2026-08-15'],
            ['Alya',     'Amanda',  'Team Love',  '2026-05-17', 'Cara Meminum Ramune',                    '2026-09-06'],
        ];

        $updated = 0;
        $missing = [];

        foreach ($data as [$first, $last, $team, $announceDate, $announceEvent, $gradDate]) {
            $member = Member::where('name', 'like', "%{$first}%")
                ->where('name', 'like', "%{$last}%")
                ->first();

            if (! $member) {
                $missing[] = "{$first} {$last}";
                continue;
            }

            $member->fill([
                'restructure_status'        => $team,
                'graduation_announce_date'  => $announceDate,
                'graduation_announce_event' => $announceEvent,
                'graduation_date'           => $gradDate,
            ])->save(); // status auto-flip via Member saving hook

            $updated++;
        }

        $this->command->info("Updated graduation data for {$updated} member(s).");
        if ($missing) {
            $this->command->warn('Not found: ' . implode(', ', $missing));
        }
    }
}
