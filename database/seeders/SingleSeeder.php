<?php

namespace Database\Seeders;

use App\Models\Single;
use Illuminate\Database\Seeder;

class SingleSeeder extends Seeder
{
    public function run(): void
    {
        $singles = [
            ['code' => 'S1',  'title' => 'RIVER',                          'release_date' => '2013-05-11', 'sequence' => 1],
            ['code' => 'S2',  'title' => 'Yuuhi wo miteiru ka?',         'release_date' => '2013-07-03', 'sequence' => 2],
            ['code' => 'S3',  'title' => 'Koi Suru Fortune Cookie',      'release_date' => '2013-08-21', 'sequence' => 3],
            ['code' => 'S4',  'title' => 'Manatsu no Sounds Good!',     'release_date' => '2013-11-26', 'sequence' => 4],
            ['code' => 'S5',  'title' => 'Flying Get',                   'release_date' => '2014-03-05', 'sequence' => 5],
            ['code' => 'S6',  'title' => 'Gingham Check',               'release_date' => '2014-06-11', 'sequence' => 6],
            ['code' => 'S7',  'title' => 'Kokoro no Placard',           'release_date' => '2014-08-27', 'sequence' => 7],
            ['code' => 'S8',  'title' => 'Kaze wa Fuiteiru',            'release_date' => '2014-12-24', 'sequence' => 8],
            ['code' => 'S9',  'title' => 'Pareo wa Emerald',            'release_date' => '2015-03-27', 'sequence' => 9],
            ['code' => 'S10', 'title' => 'Kibouteki Refrain',           'release_date' => '2015-05-27', 'sequence' => 10],
            ['code' => 'S11', 'title' => 'Halloween Night',             'release_date' => '2015-08-26', 'sequence' => 11],
            ['code' => 'S12', 'title' => 'Beginner',                    'release_date' => '2016-01-01', 'sequence' => 12],
            ['code' => 'S13', 'title' => 'Mae Shika Mukanee',          'release_date' => '2016-06-01', 'sequence' => 13],
            ['code' => 'S14', 'title' => 'LOVE TRIP',                   'release_date' => '2016-09-21', 'sequence' => 14],
            ['code' => 'S15', 'title' => 'Saikou Kayo',                 'release_date' => '2016-12-21', 'sequence' => 15],
            ['code' => 'S16', 'title' => 'So Long!',                    'release_date' => '2017-03-08', 'sequence' => 16],
            ['code' => 'S17', 'title' => 'Kimi no Hohoemi wo Yume ni Miru', 'release_date' => '2017-06-07', 'sequence' => 17],
            ['code' => 'S18', 'title' => 'Kimi wa Melody',              'release_date' => '2017-12-14', 'sequence' => 18],
            ['code' => 'S19_EK', 'title' => 'Everyday, Kachuusha',    'release_date' => '2018-06-07', 'sequence' => 19],
            ['code' => 'S19_U', 'title' => 'UZA',                       'release_date' => '2018-06-07', 'sequence' => 20],
            ['code' => 'S20', 'title' => 'High Tension',                'release_date' => '2019-01-30', 'sequence' => 21],
            ['code' => 'S21', 'title' => 'Rapsodi',                     'release_date' => '2020-01-22', 'sequence' => 22],
            ['code' => 'S22', 'title' => 'Darashinai Aishikata',       'release_date' => '2021-03-16', 'sequence' => 23],
        ];

        foreach ($singles as $single) {
            Single::updateOrCreate(
                ['code' => $single['code']],
                [
                    'title' => $single['title'],
                    'release_date' => $single['release_date'],
                    'sequence' => $single['sequence'],
                ]
            );
        }

        $this->command->info('Seeded '.count($singles).' singles.');
    }
}
