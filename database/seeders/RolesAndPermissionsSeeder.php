<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // ล้าง cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ====== สร้างสิทธิ์ (Permissions) ======
        $permissions = [
            'manage system',                    // ตั้งค่าระบบ
            'manage database',                  // จัดการฐานข้อมูล
            'manage indicators',                // จัดการตัวบ่งชี้
            'view all indicators',              // ดูสถานะตัวบ่งชี้ทั้งหมด
            'view own indicators',              // ดูเฉพาะตัวบ่งชี้ที่รับผิดชอบ
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ====== สร้างบทบาท (Roles) พร้อมมอบสิทธิ์ ======

        // 1. Super Admin - ได้ทุกอย่าง
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());

        // 2. System Admin - ตั้งค่าระบบ + ฐานข้อมูล
        $systemAdmin = Role::firstOrCreate(['name' => 'System Admin']);
        $systemAdmin->syncPermissions(['manage system', 'manage database']);

        // 3. QA Admin - จัดการตัวบ่งชี้ + ดูภาพรวม
        $qaAdmin = Role::firstOrCreate(['name' => 'QA Admin']);
        $qaAdmin->syncPermissions(['manage indicators', 'view all indicators']);

        // 4. Administration Admin - จัดการตัวบ่งชี้ + ดูภาพรวม
        $adminAdmin = Role::firstOrCreate(['name' => 'Administration Admin']);
        $adminAdmin->syncPermissions(['manage indicators', 'view all indicators']);

        // 5. User - จัดการเฉพาะตัวบ่งชี้ของตน
        $user = Role::firstOrCreate(['name' => 'User']);
        $user->syncPermissions(['view own indicators', 'manage indicators']);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> origin/Jui
