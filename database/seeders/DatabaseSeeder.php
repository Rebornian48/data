<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GenerationSeeder::class,
            SingleSeeder::class,
            MemberSeeder::class,
            CaptainSeeder::class,
        ]);

        $this->command->info('JKT48 database seeded successfully.');
    }
}