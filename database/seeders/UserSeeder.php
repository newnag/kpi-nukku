<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'prefix' => 'นาย',
                'name' => 'สมชาย ตัวอย่าง',
                'employee_id' => 'EMP001',
                'password' => Hash::make('password123'),
                'email' => 'somchai@example.com',
                'phone' => '0800000001',
                'status' => 'true',
                'department_id' => 1,
                'position_id' => 1,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
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
                'position_id' => 1,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
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
                'position_id' => 1,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
