<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PhpParser\PrettyPrinter\Standard;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
  public function run(): void
    {
        $this->call([
            PositionSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            PasswordResetTokenSeeder::class,
            StandardSeeder::class,
            CategorieSeeder::class,
            IndicatorsSeeder::class,
            CriteriasSeeder::class,
            EvidenceSeeder::class,
        ]);
    }
}
