<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // Get some indicator and user IDs
        $indicatorIds = DB::table('indicators')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        // Example: assign first 3 indicators to first 3 users
        $numIndicators = count($indicatorIds);

        for ($i = 0; $i < count($indicatorIds)+1; $i++) {
            DB::table('assignments')->insert([
                'indicator_id' => $indicatorIds[$i] ?? 1,
                'collector' => random_int(1, count($userIds)) // Randomly assign a user
            ]);
        }
    }
}