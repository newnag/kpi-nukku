<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                // 'id' => 2,
                'name' => 'System Admin',
                'password' => '$2y$12$DIpJl8PXcr9YydLoficaCuBKXDY2C5DasyoWWZjWv8O0wBXIeXi.i',
                'phone' => '0800000002',
                'status' => true,
                'email' => 'system@example.com',
                // 'email_verified_at' => null,
                // 'remember_token' => 'BzPgjxi12y',
                // 'created_at' => '2025-09-15 12:52:11',
                // 'updated_at' => '2025-09-15 12:52:11',
                'department_id' => 1,
            ],
            [
                // 'id' => 4,
                'name' => 'Administration Admin',
                'password' => '$2y$12$Q9eJbM6/bG2BhZGCukQ7rOKCrDhXL1ysbyHzR9LuiopgA3YDju/F2',
                'phone' => '0800000004',
                'status' => true,
                'email' => 'admin@example.com',
                // 'email_verified_at' => null,
                // 'remember_token' => 'yBA6aAZ230',
                // 'created_at' => '2025-09-15 12:52:11',
                // 'updated_at' => '2025-09-15 12:52:11',
                'department_id' => 3,
            ],
            [
                // 'id' => 3,
                'name' => 'QA Admin',
                'password' => '$2y$12$39n/FwYxx1I0io32gSMBlOuwkKDXw1.6FZw7ncj.SnUomQe73jqPm',
                'phone' => '0800000003',
                'status' => true,
                'email' => 'qa@example.com',
                // 'email_verified_at' => null,
                // 'remember_token' => 'eNP1e2LyssplcO68LlD0bfMdZH6NWEWWwEiA5ANWXntXdW6OPYyPZ1297xHE',
                // 'created_at' => '2025-09-15 12:52:11',
                // 'updated_at' => '2025-09-15 12:52:11',
                'department_id' => 2,
            ],
            [
                // 'id' => 1,
                'name' => 'Super Admin',
                'password' => '$2y$12$ZHMUmt8XnB9mjaIVBIohhutFIYCRm/8B2rsICOEPE6oBofB0Tznz6',
                'phone' => '0800000001',
                'status' => true,
                'email' => 'super@example.com',
                // 'email_verified_at' => null,
                // 'remember_token' => 'udQ3M615WQRBvoW2EC9wlkP5mHsyM8eEF0rRoeGAxQpeaTt3xpUcqZeww2Wo',
                // 'created_at' => '2025-09-15 12:52:11',
                // 'updated_at' => '2025-09-15 12:52:11',
                'department_id' => 1,
            ],
            [
                // 'id' => 5,
                'name' => 'User',
                'password' => '$2y$12$LIVUpFK8uh3Gwfl.FdN4.OGqh6.M6D4J0VQnrgFXU8vLK6KO3fKom',
                'phone' => '0800000005',
                'status' => true,
                'email' => 'user@example.com',
                // 'email_verified_at' => null,
                // 'remember_token' => '4jEfnU5Mb4Z1dfIK0qoHlAkPj4pj3PajZkmNrNoJ6bmJdM45Ug4fywp28tgR',
                // 'created_at' => '2025-09-15 12:52:11',
                // 'updated_at' => '2025-09-15 12:52:11',
                'department_id' => 4,
            ],
        ]);

        // Assign roles using Spatie Permission
        $map = [
            'super@example.com' => 'super_admin',
            'system@example.com' => 'system_admin',
            'qa@example.com' => 'qa_admin',
            'admin@example.com' => 'administration_admin',
            'user@example.com' => 'user',
        ];
        foreach ($map as $email => $role) {
            $u = User::where('email', $email)->first();
            if ($u) { $u->assignRole($role); }
        }
    }
}

