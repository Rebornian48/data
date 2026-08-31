<?php

namespace Database\Seeders;

use App\Models\Captain;
use App\Models\Member;
use App\Models\Team;
use Illuminate\Database\Seeder;

class CaptainSeeder extends Seeder
{
    public function run(): void
    {
        $teams = Team::pluck('id', 'code')->all();

        $captains = [
            // Kapten JKT48
            ['name' => 'Melody Nurramdhani Laksani', 'role' => 'Kapten', 'team' => null, 'start_date' => '2013-12-21', 'end_date' => '2018-03-31'],
            ['name' => 'Shania Junianatha',          'role' => 'Kapten', 'team' => null, 'start_date' => '2018-04-01', 'end_date' => '2019-04-27'],
            ['name' => 'Beby Chaesara Anadila',      'role' => 'Kapten', 'team' => null, 'start_date' => '2019-04-27', 'end_date' => '2021-02-21'],
            ['name' => 'Shani Indira Natio',         'role' => 'Kapten', 'team' => null, 'start_date' => '2021-12-18', 'end_date' => '2024-05-11'],
            ['name' => 'Shania Gracia',              'role' => 'Kapten', 'team' => null, 'start_date' => '2024-05-11', 'end_date' => '2026-08-21'],

            // Wakil Kapten JKT48
            ['name' => 'Gabriela Margareth Warouw', 'role' => 'Wakil Kapten', 'team' => null, 'start_date' => '2019-12-22', 'end_date' => '2021-02-21'],
            ['name' => 'Jinan Safa Safira',         'role' => 'Wakil Kapten', 'team' => null, 'start_date' => '2021-12-18', 'end_date' => '2023-03-18'],

            // Kapten Tim J
            ['name' => 'Devi Kinal Putri',          'role' => 'Kapten', 'team' => 'J', 'start_date' => '2012-12-23', 'end_date' => '2015-07-31'],
            ['name' => 'Shania Junianatha',         'role' => 'Kapten', 'team' => 'J', 'start_date' => '2015-08-01', 'end_date' => '2018-03-31'],
            ['name' => 'Priscillia Sari Dewi',      'role' => 'Kapten', 'team' => 'J', 'start_date' => '2018-04-01', 'end_date' => '2018-06-08'],
            ['name' => 'Gabriela Margareth Warouw', 'role' => 'Kapten', 'team' => 'J', 'start_date' => '2018-07-01', 'end_date' => '2020-06-06'],
            ['name' => 'Frieska Anastasia Laksani', 'role' => 'Kapten', 'team' => 'J', 'start_date' => '2020-06-06', 'end_date' => '2021-02-20'],

            // Kapten Tim KIII
            ['name' => 'Shinta Naomi',              'role' => 'Kapten', 'team' => 'KIII', 'start_date' => '2013-07-03', 'end_date' => '2015-08-01'],
            ['name' => 'Devi Kinal Putri',          'role' => 'Kapten', 'team' => 'KIII', 'start_date' => '2015-08-01', 'end_date' => '2016-11-30'],
            ['name' => 'Ratu Vienny Fitrilya',      'role' => 'Kapten', 'team' => 'KIII', 'start_date' => '2016-12-01', 'end_date' => '2017-10-12'],
            ['name' => 'Viviyona Apriani',          'role' => 'Kapten', 'team' => 'KIII', 'start_date' => '2018-02-01', 'end_date' => '2019-12-21'],
            ['name' => 'Ratu Vienny Fitrilya',      'role' => 'Kapten', 'team' => 'KIII', 'start_date' => '2019-12-22', 'end_date' => '2020-02-23'],
            ['name' => 'Shania Gracia',             'role' => 'Kapten', 'team' => 'KIII', 'start_date' => '2020-06-06', 'end_date' => '2021-03-13'],

            // Kapten Tim T
            ['name' => 'Haruka Nakagawa',           'role' => 'Kapten', 'team' => 'T', 'start_date' => '2015-08-01', 'end_date' => '2016-11-30'],
            ['name' => 'Melody Nurramdhani Laksani','role' => 'Kapten', 'team' => 'T', 'start_date' => '2016-12-01', 'end_date' => '2018-03-31'],
            ['name' => 'Ayana Shahab',              'role' => 'Kapten', 'team' => 'T', 'start_date' => '2018-04-01', 'end_date' => '2019-12-08'],
            ['name' => 'Tan Zhi Hui Celine',        'role' => 'Kapten', 'team' => 'T', 'start_date' => '2020-08-22', 'end_date' => '2021-03-12'],
        ];

        $count = 0;
        foreach ($captains as $data) {
            $member = Member::where('name', $data['name'])->first();
            if (! $member) {
                $this->command->warn("Member not found: {$data['name']}");

                continue;
            }

            $teamId = $data['team'] ? ($teams[$data['team']] ?? null) : null;

            Captain::updateOrCreate(
                [
                    'member_id' => $member->id,
                    'role' => $data['role'],
                    'team_id' => $teamId,
                    'start_date' => $data['start_date'],
                ],
                [
                    'end_date' => $data['end_date'],
                ]
            );
            $count++;
        }

        $this->command->info("Seeded {$count} captain records.");
    }
}
