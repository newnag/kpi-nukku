<?php

namespace Database\Seeders;

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
            // ตัวบ่งชี้ที่ 2
            [
                // id-1
                'indicator_id' => 2,
                'label' => 'ร้อยละของอาจารย์ประจำที่มีคุณวุฒิปริญญาเอก',
                'variable_name' => 'precent_doctor',
                'type' => 'input,',
                'value' => null,
                'TIMESTAMP' => now(),

            ],
            [
                // id-2
                'indicator_id' => 2,
                'label' => 'ร้อยละ 40',
                'variable_name' => 'precent_40',
                'type' => 'static',
                'value' => 40.00,
                'TIMESTAMP' => now(),

            ],
            [
                // id-3
                'indicator_id' => 2,
                'label' => 'คะแนน',
                'variable_name' => 'socre',
                'type' => 'static',
                'value' => 10.00,
                'TIMESTAMP' => now(),

            ],
            [
                // id-4
                'indicator_id' => 2,
                'label' => 'คะแนนที่ได้',
                'variable_name' => 'score',
                'type' => 'output',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            // ตัวบ่งชี้ที่ 3
            [
                // id-5
                'indicator_id' => 3,
                'label' => 'จำนวนอาจารย์พยาบาลประจำที่มีคุณสมบัติตามเกณฑ์',
                'variable_name' => 'qualified_nurse',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),

            ],
            [
                // id-6
                'indicator_id' => 3,
                'label' => 'จำนวนอาจารย์พยาบาลประจำทั้งหมดของสถาบันรวมทุกคุณวุฒิการศึกษา',
                'variable_name' => 'total_nurse',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),

            ],
            [
                // id-7
                'indicator_id' => 3,
                'label' => 'คะแนน',
                'variable_name' => 'socre',
                'type' => 'static',
                'value' => 100.00,
                'TIMESTAMP' => now(),

            ],
            [
                // id-8
                'indicator_id' => 3,
                'label' => 'คะแนนที่ได้',
                'variable_name' => 'socre',
                'type' => 'output',
                'value' => null,
                'TIMESTAMP' => now(),

            ],

            // ตัวบ่งชี้ที่ 5
            [
                // id-9
                'indicator_id' => 5,
                'label' => 'ร้อยละของอาจารย์ประจำหลักสูตร',
                'variable_name' => 'precent_teacher',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),

            ],
            [
                // id-10
                'indicator_id' => 5,
                'label' => 'ตัวแปร',
                'variable_name' => 'precent_100',
                'type' => 'static',
                'value' => 100.00,
                'TIMESTAMP' => now(),

            ],
            [
                // id-11
                'indicator_id' => 5,
                'label' => 'ตัวแปร',
                'variable_name' => 'score',
                'type' => 'static',
                'value' => 15.00,
                'TIMESTAMP' => now(),

            ],
            [
                // id-12
                'indicator_id' => 5,
                'label' => 'คะแนนที่ได้',
                'variable_name' => 'score',
                'type' => 'output',
                'value' => null,
                'TIMESTAMP' => now(),

            ],
            // ตัวบ่งชี้ที่ 6
            [
                // id-13
                'indicator_id' => 6,
                'label' => 'อัตราส่วนของอาจารย์ประจำต่อจำนวนนักศึกษา',
                'variable_name' => 'input',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),

            ],
            // ตัวบ่งชี้ที่ 7
            [
                // id-14
                'indicator_id' => 7,
                'label' => 'ร้อยละของรายวิชาที่มีอาจารย์พยาบาลประจำต่อนักศึกษาไม่เกิน 1:8',
                'variable_name' => 'input',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),

            ],
            // ผลลัพธ์
            [
                // id-15
                'indicator_id' => 37,
                'label' => 'คะแนนเฉลี่ยของการประเมินคณะผู้บริหาร',
                'variable_name' => 'average_score',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),

            ],

            [
                // id-16
                'indicator_id' => 39,
                'label' => 'ร้อยละของการดำเนินการที่บรรลุเป้าหมายตามแผนกลยุทธ์/ยุทธศาสตร์',
                'variable_name' => 'precent_100',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),

            ],
            [
                // id-17
                'indicator_id' => 40,
                'label' => 'ร้อยละของการใช้จ่ายงบประมาณตามแผนจัดสรรงบประมาณ',
                'variable_name' => 'precent_budget',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),

            ],
            [
                // id-18
                'indicator_id' => 41,
                'label' => 'อัตราการคงอยู่ของอาจารย์',
                'variable_name' => 'retention_rate',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            [
                // id-19
                'indicator_id' => 42,
                'label' => 'อัตราการคงอยู่ของบุคลากรสายสนับสนุน',
                'variable_name' => 'retention_support_staff',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            // ตัวบ่งชี้ที่ 7

            [
                // id-20
                'indicator_id' => 43,
                'label' => 'คุณภาพการจัดการเรียนการสอน  ระดับที่ 1',
                'variable_name' => 'quality_management_level_1',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            [
                // id-21
                'indicator_id' => 43,
                'label' => 'คุณภาพการจัดการเรียนการสอน ระดับที่ 1 คิดเป็นร้อยละ',
                'variable_name' => 'quality_management_level_1_percent',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            [
                // id-22
                'indicator_id' => 43,
                'label' => 'คุณภาพการจัดการเรียนการสอน ระดับที่ 2',
                'variable_name' => 'quality_management_level_2',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            [
                // id-23
                'indicator_id' => 43,
                'label' => 'คุณภาพการจัดการเรียนการสอน ระดับที่ 2 คิดเป็นร้อยละ',
                'variable_name' => 'quality_management_level_2_percent',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            [
                // id-24
                'indicator_id' => 43,
                'label' => 'คุณภาพการจัดการเรียนการสอน ระดับที่ 3',
                'variable_name' => 'quality_management_level_3',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            [
                // id-25
                'indicator_id' => 43,
                'label' => 'คุณภาพการจัดการเรียนการสอน ระดับที่ 3 คิดเป็นร้อยละ',
                'variable_name' => 'quality_management_level_3_percent',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            [
                // id-26
                'indicator_id' => 43,
                'label' => 'คุณภาพการจัดการเรียนการสอน ระดับที่ 4',
                'variable_name' => 'quality_management_level_4',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            [
                // id-27
                'indicator_id' => 43,
                'label' => 'คุณภาพการจัดการเรียนการสอน ระดับที่ 4 คิดเป็นร้อยละ',
                'variable_name' => 'quality_management_level_4_percent',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            [
                // id-28
                'indicator_id' => 44,
                'label' => 'ร้อยละนักศึกษาชั้นปีสุดท้ายที่มีผลลัพธ์การเรียนรู้ครบตามที่กำหนด',
                'variable_name' => 'precent_outcome',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            [
                // id-29
                'indicator_id' => 45,
                'label' => 'ร้อยละของผู้สอบผ่านในครั้งแรก',
                'variable_name' => 'precent_pass_first_attempt',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            [
                // id-30
                'indicator_id' => 46,
                'label' => 'ร้อยละของผู้สอบผ่านในปีแรก',
                'variable_name' => 'precent_pass_first_year',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            [
                // id-31
                'indicator_id' => 49,
                'label' => 'ร้อยละของผลงานทางวิชาการ',
                'variable_name' => 'precent_academic_work',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            [
                // id-32
                'indicator_id' => 49,
                'label' => 'ตัวแปร',
                'variable_name' => 'score',
                'type' => 'static',
                'value' => 70.00,
                'TIMESTAMP' => now(),
            ],
            [
                // id-33
                'indicator_id' => 49,
                'label' => 'ตัวแปร',
                'variable_name' => 'score',
                'type' => 'static',
                'value' => 25.00,
                'TIMESTAMP' => now(),
            ],
            [
                // id-34
                'indicator_id' => 49,
                'label' => 'คะแนนที่ได้',
                'variable_name' => 'score',
                'type' => 'output',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            [
                // id-35
                'indicator_id' => 53,
                'label' => 'ร้อยละของอาจารย์พยาบาลประจำที่ปฏิบัติการพยาบาลย้อนหลัง3ปี',
                'variable_name' => 'precent_nurse_practice',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),
            ],
            [
                // id-36
                'indicator_id' => 55,
                'label' => 'ร้อยละของบัณฑิตที่มีอัตลักษณ์/คุณลักษณะพิเศษตามที่สถาบันกำหนดต่อจำนวนบัณฑิตทั้งหมด',
                'variable_name' => 'precent_graduate_identity',
                'type' => 'input',
                'value' => null,
                'TIMESTAMP' => now(),
            ],

        ]);
    }
}
