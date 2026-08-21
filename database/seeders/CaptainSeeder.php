<?php

namespace Database\Seeders;

use App\Models\Captain;
use App\Models\Member;
use Illuminate\Database\Seeder;

class CaptainSeeder extends Seeder
{
    public function run(): void
    {
        $captains = [
            // Kapten JKT48
            ['name' => 'Melody Nurramdhani Laksani', 'position' => 'Kapten JKT48', 'start_date' => '2013-12-21', 'end_date' => '2018-03-31'],
            ['name' => 'Shania Junianatha', 'position' => 'Kapten JKT48', 'start_date' => '2018-04-01', 'end_date' => '2019-04-27'],
            ['name' => 'Beby Chaesara Anadila', 'position' => 'Kapten JKT48', 'start_date' => '2019-04-27', 'end_date' => '2021-02-21'],
            ['name' => 'Shani Indira Natio', 'position' => 'Kapten JKT48', 'start_date' => '2021-12-18', 'end_date' => '2024-05-11'],
            ['name' => 'Shania Gracia', 'position' => 'Kapten JKT48', 'start_date' => '2024-05-11', 'end_date' => '2026-08-21'],

            // Wakil Kapten JKT48
            ['name' => 'Gabriela Margareth Warouw', 'position' => 'Wakil Kapten JKT48', 'start_date' => '2019-12-22', 'end_date' => '2021-02-21'],
            ['name' => 'Jinan Safa Safira', 'position' => 'Wakil Kapten JKT48', 'start_date' => '2021-12-18', 'end_date' => '2023-03-18'],

            // Kapten Tim J
            ['name' => 'Devi Kinal Putri', 'position' => 'Kapten Tim J', 'start_date' => '2012-12-23', 'end_date' => '2015-07-31'],
            ['name' => 'Shania Junianatha', 'position' => 'Kapten Tim J', 'start_date' => '2015-08-01', 'end_date' => '2018-03-31'],
            ['name' => 'Priscillia Sari Dewi', 'position' => 'Kapten Tim J', 'start_date' => '2018-04-01', 'end_date' => '2018-06-08'],
            ['name' => 'Gabriela Margareth Warouw', 'position' => 'Kapten Tim J', 'start_date' => '2018-07-01', 'end_date' => '2020-06-06'],
            ['name' => 'Frieska Anastasia Laksani', 'position' => 'Kapten Tim J', 'start_date' => '2020-06-06', 'end_date' => '2021-02-20'],

            // Kapten Tim KIII
            ['name' => 'Shinta Naomi', 'position' => 'Kapten Tim KIII', 'start_date' => '2013-07-03', 'end_date' => '2015-08-01'],
            ['name' => 'Devi Kinal Putri', 'position' => 'Kapten Tim KIII', 'start_date' => '2015-08-01', 'end_date' => '2016-11-30'],
            ['name' => 'Ratu Vienny Fitrilya', 'position' => 'Kapten Tim KIII', 'start_date' => '2016-12-01', 'end_date' => '2017-10-12'],
            ['name' => 'Viviyona Apriani', 'position' => 'Kapten Tim KIII', 'start_date' => '2018-02-01', 'end_date' => '2019-12-21'],
            ['name' => 'Ratu Vienny Fitrilya', 'position' => 'Kapten Tim KIII', 'start_date' => '2019-12-22', 'end_date' => '2020-02-23'],
            ['name' => 'Shania Gracia', 'position' => 'Kapten Tim KIII', 'start_date' => '2020-06-06', 'end_date' => '2021-03-13'],

            // Kapten Tim T
            ['name' => 'Haruka Nakagawa', 'position' => 'Kapten Tim T', 'start_date' => '2015-08-01', 'end_date' => '2016-11-30'],
            ['name' => 'Melody Nurramdhani Laksani', 'position' => 'Kapten Tim T', 'start_date' => '2016-12-01', 'end_date' => '2018-03-31'],
            ['name' => 'Ayana Shahab', 'position' => 'Kapten Tim T', 'start_date' => '2018-04-01', 'end_date' => '2019-12-08'],
            ['name' => 'Tan Zhi Hui Celine', 'position' => 'Kapten Tim T', 'start_date' => '2020-08-22', 'end_date' => '2021-03-12'],
        ];

        $count = 0;
        foreach ($captains as $data) {
            $member = Member::where('name', $data['name'])->first();
            if (!$member) {
                $this->command->warn("Member not found: {$data['name']}");
                continue;
            }

            Captain::updateOrCreate(
                [
                    'member_id' => $member->id,
                    'position' => $data['position'],
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
