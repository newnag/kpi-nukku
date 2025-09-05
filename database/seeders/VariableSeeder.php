<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VariableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('variables')->insert([
            //ตัวบ่งชี้ที่ 2
            [
                //id-1
                'indicator_id' => 2,
                'variable_name' => 'precent_doctor',
                'label_name' => 'ร้อยละของอาจารย์ประจำที่มีคุณวุฒิปริญญาเอก',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),

            ],
            [
                //id-2
                'indicator_id' => 2,
                'variable_name' => 'precent_40',
                'label_name' => 'precent_40',
                'type' => 'static',
                'value' => 40.00,
                // 'TIMESTAMP' => now(),

            ],
            [
                //id-3
                'indicator_id' => 2,
                'variable_name' => 'socre',
                'label_name' => 'socre',
                'type' => 'static',
                'value' => 10.00,
                // 'TIMESTAMP' => now(),

            ],
            [
                //id-4
                'indicator_id' => 2,
                'variable_name' => 'score',
                'label_name' => 'score',
                'type' => 'output',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            //ตัวบ่งชี้ที่ 3
            [
                // id-5
                'indicator_id' => 3,
                'variable_name' => 'qualified_nurse',
                'label_name' => 'จำนวนอาจารย์พยาบาลประจำที่มีคุณสมบัติตามเกณฑ์',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),

            ],
            [
                // id-6
                'indicator_id' => 3,
                'variable_name' => 'total_nurse',
                'label_name' => 'จำนวนอาจารย์พยาบาลประจำทั้งหมดของสถาบันรวมทุกคุณวุฒิการศึกษา',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),

            ],
            [
                // id-7
                'indicator_id' => 3,
                'variable_name' => 'socre',
                'label_name' => 'socre',
                'type' => 'static',
                'value' => 100.00,
                // 'TIMESTAMP' => now(),

            ],
            [
                // id-8
                'indicator_id' => 3,
                'variable_name' => 'socre',
                'label_name' => 'socre',
                'type' => 'output',
                'value' => null,
                // 'TIMESTAMP' => now(),

            ],
            
            //ตัวบ่งชี้ที่ 5
            [
                // id-9
                'indicator_id' => 5,
                'variable_name' => 'precent_teacher',
                'label_name' => 'precent_teacher',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),

            ],
            [
                // id-10
                'indicator_id' => 5,
                'variable_name' => 'precent_100',
                'label_name' => 'precent_100',
                'type' => 'static',
                'value' => 100.00,
                // 'TIMESTAMP' => now(),

            ],
            [
                // id-11
                'indicator_id' => 5,
                'variable_name' => 'score',
                'label_name' => 'score',
                'type' => 'static',
                'value' => 15.00,
                // 'TIMESTAMP' => now(),

            ],
            [
                // id-12
                'indicator_id' => 5,
                'variable_name' => 'score',
                'label_name' => 'score',
                'type' => 'output',
                'value' => null,
                // 'TIMESTAMP' => now(),

            ],
            //ตัวบ่งชี้ที่ 6
            [
                // id-13
                'indicator_id' => 6,
                'variable_name' => 'input',
                'label_name' => 'input',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),

            ],
            //ตัวบ่งชี้ที่ 7
            [
                // id-14
                'indicator_id' => 7,
                'variable_name' => 'input',
                'label_name' => 'input',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),

            ],
            //ผลลัพธ์
            [
                // id-15
                'indicator_id' => 37,
                'variable_name' => 'average_score',
                'label_name' => 'average_score',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),

            ],

            [
                // id-16
                'indicator_id' => 39,
                'variable_name' => 'precent_100',
                'label_name' => 'precent_100',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),

            ],
            [
                // id-17
                'indicator_id' => 40,
                'variable_name' => 'precent_budget',
                'label_name' => 'precent_budget',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),

            ],
            [
                // id-18
                'indicator_id' => 41,
                'variable_name' => 'retention_rate',
                'label_name' => 'retention_rate',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            [
                // id-19
                'indicator_id' => 42,
                'variable_name' => 'retention_support_staff',
                'label_name' => 'retention_support_staff',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            //ตัวบ่งชี้ที่ 7
           
            [
                // id-20
                'indicator_id' => 43,
                'variable_name' => 'quality_management_level_1',
                'label_name' => 'quality_management_level_1',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            [
                // id-21
                'indicator_id' => 43,
                'variable_name' => 'quality_management_level_1_percent',
                'label_name' => 'quality_management_level_1_percent',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            [
                // id-22
                'indicator_id' => 43,
                'variable_name' => 'quality_management_level_2',
                'label_name' => 'quality_management_level_2',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            [
                // id-23
                'indicator_id' => 43,
                'variable_name' => 'quality_management_level_2_percent',
                'label_name' => 'quality_management_level_2_percent',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            [
                // id-24
                'indicator_id' => 43,
                'variable_name' => 'quality_management_level_3',
                'label_name' => 'quality_management_level_3',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            [
                // id-25
                'indicator_id' => 43,
                'variable_name' => 'quality_management_level_3_percent',
                'label_name' => 'quality_management_level_3_percent',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            [
                // id-26
                'indicator_id' => 43,
                'variable_name' => 'quality_management_level_4',
                'label_name' => 'quality_management_level_4',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            [
                // id-27
                'indicator_id' => 43,
                'variable_name' => 'quality_management_level_4_percent',
                'label_name' => 'quality_management_level_4_percent',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            [
                //id-28
                'indicator_id' => 44,
                'variable_name' => 'precent_outcome',
                'label_name' => 'precent_outcome',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            [
                //id-29
                'indicator_id' => 45,
                'variable_name' => 'precent_pass_first_attempt',
                'label_name' => 'precent_pass_first_attempt',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            [
                //id-30
                'indicator_id' => 46,
                'variable_name' => 'precent_pass_first_year',
                'label_name' => 'precent_pass_first_year',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            [
                //id-31
                'indicator_id' => 49,
                'variable_name' => 'precent_academic_work',
                'label_name' => 'precent_academic_work',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            [
                //id-32
                'indicator_id' => 49,
                'variable_name' => 'score',
                'label_name' => 'score',
                'type' => 'static',
                'value' => 70.00,
                // 'TIMESTAMP' => now(),
            ],
            [
                //id-33
                'indicator_id' => 49,
                'variable_name' => 'score',
                'label_name' => 'score',
                'type' => 'static',
                'value' => 25.00,
                // 'TIMESTAMP' => now(),
            ],
            [
                //id-34
                'indicator_id' => 49,
                'variable_name' => 'score',
                'label_name' => 'score',
                'type' => 'output',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            [
                //id-35
                'indicator_id' => 53,
                'variable_name' => 'precent_nurse_practice',
                'label_name' => 'precent_nurse_practice',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
            [
                //id-36
                'indicator_id' => 55,
                'variable_name' => 'precent_graduate_identity',
                'label_name' => 'precent_graduate_identity',
                'type' => 'input',
                'value' => null,
                // 'TIMESTAMP' => now(),
            ],
    
          




        ]);
    }
}
