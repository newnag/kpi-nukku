<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('positions')->insert([
            ['name' => 'รองคณบดีฝ่ายบริหารและยุทธศาสตร์', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ผู้อำนวยการกองบริหารคณะ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'รองคณบดีฝ่ายการศึกษาและบริการวิชาการ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ผู้ช่วยคณบดีฝ่ายพัฒนานักศึกษาและศิษย์เก่าสัมพันธ์', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ผู้ช่วยคณบดีฝ่ายเทคโนโลยีและประกันคุณภาพ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'รองคณบดีฝ่ายวิจัย นวัตกรรมและการต่างประเทศ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ผู้ช่วยคณบดีฝ่ายบริการวิชาการและกิจการพิเศษ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ผู้รับผิดชอบหลักสูตร พยบ.', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
