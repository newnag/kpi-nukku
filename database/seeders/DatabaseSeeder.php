<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear cached roles/permissions
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
            checklist_items,
            formulas,
            variables,
            variable_formulas,
            settings
            RESTART IDENTITY CASCADE');

        // ✅ Parent -> Child
        $this->call([
            DepartmentsTableSeeder::class,
            RolesTableSeeder::class,
            PermissionsTableSeeder::class,
            UsersTableSeeder::class,
            StandardsTableSeeder::class,
            CategoriesTableSeeder::class,
            IndicatorsTableSeeder::class,       // ✅ ต้องมาก่อน criterias
            CriteriasTableSeeder::class,        // ✅ มาทีหลัง indicators
            AssignmentsTableSeeder::class,
            EvidenceTableSeeder::class,
            FormulasTableSeeder::class,         // ✅ ต้องมาก่อน variable_formulas
            VariablesTableSeeder::class,        // ✅ ต้องมาก่อน variable_formulas
            VariableFormulasTableSeeder::class, // ✅ มาทีหลัง
            ChecklistItemsTableSeeder::class,
            SettingsTableSeeder::class,
        ]);
        $this->call(FormulasTableSeeder::class);
    }
}
