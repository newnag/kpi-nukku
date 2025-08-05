<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departments')->insert([
            ['name' => 'หน่วยจัดการงานทั่วไป', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'หน่วยทรัพยากรบุคคล', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'หน่วยยุทธศาสตร์และพัฒนาคุณภาพ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'หน่วยเทคโนโลยีเพื่อการบริหารองค์กร', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'หน่วยอาคารสถานที่และยานพาหนะ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'หอพักนักศึกษา', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'หน่วยจัดการศึกษาปริญญาตรี', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'หน่วยการต่างประเทศ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'หน่วยพัฒนานักศึกษาและศิษย์เก่าสัมพันธ์', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'หน่วยเทคโนโลยีเพื่อการศึกษา', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'หน่วยห้องปฏิบัติการพยาบาล', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'หน่วยห้องสมุด', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'หน่วยวิจัยและบริการวิชาการ (วิจัย)', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'หน่วยวิจัยและบริการวิชาการ (บริการวิชาการ)', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'หน่วยจัดการศึกษาปริญญาตรีฯ', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}