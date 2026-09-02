<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            ['code' => 'J',       'name' => 'Tim J',       'color' => '#3b82f6', 'formed_at' => '2013-11-03', 'disbanded_at' => '2021-03-13'],
            ['code' => 'KIII',    'name' => 'Tim KIII',    'color' => '#8b5cf6', 'formed_at' => '2013-11-03', 'disbanded_at' => '2021-03-13'],
            ['code' => 'T',       'name' => 'Tim T',       'color' => '#10b981', 'formed_at' => '2015-08-01', 'disbanded_at' => '2021-03-13'],
            ['code' => 'Love',    'name' => 'Team Love',   'color' => '#ec4899', 'formed_at' => '2026-04-01', 'disbanded_at' => null, 'notes' => 'Diumumkan 2025-12-20 (The First Snow - JKT48 14th Anniversary Concert), resmi terbentuk 2026-04-01.'],
            ['code' => 'Dream',   'name' => 'Team Dream',  'color' => '#06b6d4', 'formed_at' => '2026-04-01', 'disbanded_at' => null, 'notes' => 'Diumumkan 2025-12-20 (The First Snow - JKT48 14th Anniversary Concert), resmi terbentuk 2026-04-01.'],
            ['code' => 'Passion', 'name' => 'Team Passion', 'color' => '#f59e0b', 'formed_at' => '2026-04-01', 'disbanded_at' => null, 'notes' => 'Diumumkan 2025-12-20 (The First Snow - JKT48 14th Anniversary Concert), resmi terbentuk 2026-04-01.'],
        ];

        foreach ($teams as $data) {
            Team::updateOrCreate(['code' => $data['code']], $data);
        }

        $this->command->info('Seeded '.count($teams).' teams.');
    }
}
