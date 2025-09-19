<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use PhpParser\PrettyPrinter\Standard;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
 
    public function run(): void
{
    // Clear cached roles/permissions and reset tables for clean seeding
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    // Truncate core tables to avoid duplicate key errors on reseed (PostgreSQL)
    DB::statement('TRUNCATE TABLE 
        model_has_permissions,
        model_has_roles,
        role_has_permissions,
        permissions,
        roles,
        users,
        departments,
        categories,
        standards,
        indicators,
        assignments,
        criterias,
        evidence,
        variables,
        variable_formulas,
        settings
        RESTART IDENTITY CASCADE');

    $this->call([
        DepartmentsTableSeeder::class,
        RolesTableSeeder::class,
        PermissionsTableSeeder::class,
        UsersTableSeeder::class,
        StandardsTableSeeder::class,
        CategoriesTableSeeder::class,
        IndicatorsTableSeeder::class,
        AssignmentsTableSeeder::class,
        CriteriasTableSeeder::class,
        EvidenceTableSeeder::class,
        FormulasTableSeeder::class,
        VariablesTableSeeder::class,
        VariableFormulasTableSeeder::class,
        SettingsTableSeeder::class,
    ]);
    }
}
