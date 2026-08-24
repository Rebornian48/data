<?php

namespace Database\Seeders;

use App\Models\Generation;
use App\Models\Member;
use Illuminate\Database\Seeder;

class Generation14MemberSeeder extends Seeder
{
    public function run(): void
    {
        $gen14 = Generation::firstOrCreate(
            ['code' => '14'],
            ['name' => 'Generasi 14']
        );

        // [fullname, nickname, birth_place, birth_date]
        $members = [
            ['Afera Thalia Putri Eysteinn',        'Fera',    'Bogor',     '2012-10-20'],
            ['Carissa Dini Asmaranti',             'Carissa', 'Jakarta',   '2012-02-02'],
            ['Christabella Bonita Claura Chandra', 'Bella',   'Tangerang', '2011-03-02'],
            ['Fahira Putri Kirana',                'Fahira',  'Jakarta',   '2012-08-13'],
            ['Fatimah Azzahra',                    'Rara',    'Depok',     '2010-08-30'],
            ['Heidi Suyangga',                     'Heidi',   'Jakarta',   '2008-08-27'],
            ['Maegan Jovanka Andhita Putri',       'Maegan',  'Depok',     '2011-12-21'],
            ['Maxine Faye Lee',                    'Maxine',  'Jakarta',   '2011-12-02'],
            ['Putry Jazyta',                       'Jazzy',   'Bogor',     '2011-03-12'],
            ['Ralyne Van Irwan',                   'Ralyne',  'Medan',     '2011-10-15'],
            ['Sona Kalyana Purboprasetyani',       'Sona',    'Jakarta',   '2011-12-01'],
        ];

        foreach ($members as [$name, $nickname, $birthPlace, $birthDate]) {
            Member::updateOrCreate(
                ['name' => $name],
                [
                    'nickname'           => $nickname,
                    'birth_place'        => $birthPlace,
                    'birth_date'         => $birthDate,
                    'generation_id'      => $gen14->id,
                    'status'             => 'Aktif',
                    'restructure_status' => 'Trainee',
                ]
            );
        }

        $this->command->info('Seeded ' . count($members) . ' Generasi 14 members.');
    }
}
