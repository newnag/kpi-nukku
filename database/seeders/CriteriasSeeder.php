<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CriteriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('criterias')->insert([
            [
                'name' => 'ตัวบ่งชี้ที่ 1 การบริหารจัดการ',
                'description' => 'การบริหารจัดการ หมายถึง การวางแผน การดำเนินงาน การติดตามประเมินผล และการปรับปรุงพัฒนาการดำเนินงานของสถาบัน',
                'sequence' => 1,
                'indicator_id' => 1,
            ],
        ]);
    }
}
