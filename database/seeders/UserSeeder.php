<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'prefix' => 'นาย',
                'name' => 'สมชาย ตัวอย่าง',
                'employee_id' => 'EMP001',
                'password' => Hash::make('password123'),
                'email' => 'somchai@example.com',
                'phone' => '0800000001',
                'status' => 'true',
                'department_id' => 1,
                'remember_token' => Str::random(10),
                'role' => 'Super Admin',
            ],
            [
                'prefix' => 'นาย',
                'name' => 'สมสี ตัวอย่าง',
                'employee_id' => 'EMP002',
                'password' => Hash::make('password123'),
                'email' => 'somchi@example.com',
                'phone' => '0800000002',
                'status' => 'true',
                'department_id' => 1,
                'remember_token' => Str::random(10),
                'role' => 'QA Admin',
            ],
            [
                'prefix' => 'นาย',
                'name' => 'สมพงษ์ ตัวอย่าง',
                'employee_id' => 'EMP003',
                'password' => Hash::make('password123'),
                'email' => 'sompong@example.com',
                'phone' => '0800000003',
                'status' => 'true',
                'department_id' => 1,
                'remember_token' => Str::random(10),
                'role' => 'User',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']); // ลบ key 'role' ก่อน insert

            $user = User::create($userData);
            $user->assignRole($role); // กำหนดบทบาทให้ผู้ใช้
        }
    }
}
