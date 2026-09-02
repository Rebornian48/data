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
            Generation14MemberSeeder::class,
            TeamSeeder::class,
            MemberTeamSeeder::class,
            CaptainSeeder::class,
            MemberSingleSeeder::class,
            AdminUserSeeder::class,
        ]);

        $this->command->info('JKT48 database seeded successfully.');
    }
}
