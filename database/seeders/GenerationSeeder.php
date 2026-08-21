<?php

namespace Database\Seeders;

use App\Models\Generation;
use Illuminate\Database\Seeder;

class GenerationSeeder extends Seeder
{
    public function run(): void
    {
        $generations = [
            ['code' => '1',        'name' => 'Generasi 1',          'join_date' => '2011-11-02'],
            ['code' => '2',        'name' => 'Generasi 2',          'join_date' => '2012-11-03'],
            ['code' => '3',        'name' => 'Generasi 3',          'join_date' => '2014-03-15'],
            ['code' => '4',        'name' => 'Generasi 4',          'join_date' => '2015-05-16'],
            ['code' => '5',        'name' => 'Generasi 5',          'join_date' => '2016-05-28'],
            ['code' => '6',        'name' => 'Generasi 6',          'join_date' => '2018-04-08'],
            ['code' => '7',        'name' => 'Generasi 7',          'join_date' => '2018-09-29'],
            ['code' => '8',        'name' => 'Generasi 8',          'join_date' => '2019-04-27'],
            ['code' => '9',        'name' => 'Generasi 9',          'join_date' => '2019-12-01'],
            ['code' => '10',       'name' => 'Generasi 10',         'join_date' => '2020-08-27'],
            ['code' => '11',       'name' => 'Generasi 11',         'join_date' => '2022-10-31'],
            ['code' => '12',       'name' => 'Generasi 12',         'join_date' => '2023-11-18'],
            ['code' => '13',       'name' => 'Generasi 13',         'join_date' => '2024-10-31'],
            ['code' => 'Kaigai 1', 'name' => 'Kaigai Generasi 1',   'join_date' => '2012-11-01'],
            ['code' => 'Kaigai 2', 'name' => 'Kaigai Generasi 2',   'join_date' => '2014-04-24'],
            ['code' => 'Transfer', 'name' => 'Transfer',            'join_date' => '2018-09-17'],
            ['code' => 'V1',       'name' => 'Vocal Generasi 1',    'join_date' => '2023-07-30'],
            ['code' => 'V2',       'name' => 'Vocal Generasi 2',    'join_date' => '2024-01-01'],
        ];

        foreach ($generations as $gen) {
            Generation::updateOrCreate(
                ['code' => $gen['code']],
                ['name' => $gen['name'], 'join_date' => $gen['join_date']]
            );
        }

        $this->command->info('Seeded ' . count($generations) . ' generations.');
    }
}