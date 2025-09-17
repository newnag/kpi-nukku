<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class VariablesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

   
        
        \DB::table('variables')->insert(array (
            0 => 
            array (
                'id' => 37,
                'variable_name' => 'input_1',
                'label_name' => 'ร้อยละของอาจารย์ประจำที่มีคุณวุฒิปริญญาเอก',
                'type' => 'input',
                'value' => 60.0,
                'indicator_id' => 2,
                'created_at' => '2025-09-15 13:25:18',
                'updated_at' => '2025-09-16 07:49:37',
            ),
            1 => 
            array (
                'id' => 38,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวหาร',
                'type' => 'defined',
                'value' => 40.0,
                'indicator_id' => 2,
                'created_at' => '2025-09-15 13:25:18',
                'updated_at' => '2025-09-15 13:25:18',
            ),
            2 => 
            array (
                'id' => 39,
                'variable_name' => 'defined_2',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 10.0,
                'indicator_id' => 2,
                'created_at' => '2025-09-15 13:25:18',
                'updated_at' => '2025-09-15 13:25:18',
            ),
            3 => 
            array (
                'id' => 40,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนอาจารย์พยาบาลประจำที่มีคุณสมบัติตามเก็ณฑ์',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 3,
                'created_at' => '2025-09-15 13:37:35',
                'updated_at' => '2025-09-15 13:37:35',
            ),
            4 => 
            array (
                'id' => 41,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนอาจารย์ประจำทั้งหมดของสถายบันรวมทุกคุณวุฒิการศึกษา',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 3,
                'created_at' => '2025-09-15 13:37:35',
                'updated_at' => '2025-09-15 13:37:35',
            ),
            5 => 
            array (
                'id' => 42,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 3,
                'created_at' => '2025-09-15 13:37:35',
                'updated_at' => '2025-09-15 13:37:35',
            ),
            6 => 
            array (
                'id' => 85,
                'variable_name' => 'input_1',
                'label_name' => 'ค่าเฉลี่ยของของการประเมินคณะผู้บริหาร',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 37,
                'created_at' => '2025-09-16 06:02:05',
                'updated_at' => '2025-09-16 06:02:05',
            ),
            7 => 
            array (
                'id' => 86,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนโครงการ/กิจกรรมที่มีผลการดำเนินงานเป็นไปตามเป้าหมายที่กำหนดไว้ในแผลกลยุทธ์/ยุทธศาสตร์',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 39,
                'created_at' => '2025-09-16 06:09:14',
                'updated_at' => '2025-09-16 06:09:14',
            ),
            8 => 
            array (
                'id' => 87,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนโครงการ/กิจกรรททั้งหมดในแต่ละปีงบประมาณ',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 39,
                'created_at' => '2025-09-16 06:09:14',
                'updated_at' => '2025-09-16 06:09:14',
            ),
            9 => 
            array (
                'id' => 88,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 39,
                'created_at' => '2025-09-16 06:09:14',
                'updated_at' => '2025-09-16 06:09:14',
            ),
            10 => 
            array (
                'id' => 89,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนค่าใช้จ่าย',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 40,
                'created_at' => '2025-09-16 06:12:27',
                'updated_at' => '2025-09-16 06:12:27',
            ),
            11 => 
            array (
                'id' => 90,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนเงินที่จัดสรรไว้ในแผนปฏิบัติการประจำปี',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 40,
                'created_at' => '2025-09-16 06:12:27',
                'updated_at' => '2025-09-16 06:12:27',
            ),
            12 => 
            array (
                'id' => 77,
                'variable_name' => 'input_1',
                'label_name' => 'FTES',
                'type' => 'input',
                'value' => 1.0,
                'indicator_id' => 6,
                'created_at' => '2025-09-16 03:40:32',
                'updated_at' => '2025-09-16 03:40:48',
            ),
            13 => 
            array (
                'id' => 91,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 40,
                'created_at' => '2025-09-16 06:12:27',
                'updated_at' => '2025-09-16 06:12:27',
            ),
            14 => 
            array (
                'id' => 92,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนอาจารย์เมื่อสิ้นปีการศึกษา',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 41,
                'created_at' => '2025-09-16 06:15:18',
                'updated_at' => '2025-09-16 06:15:18',
            ),
            15 => 
            array (
                'id' => 93,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนอาจารย์เมื่อเริ่มต้นปีการศึกษา',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 41,
                'created_at' => '2025-09-16 06:15:18',
                'updated_at' => '2025-09-16 06:15:18',
            ),
            16 => 
            array (
                'id' => 78,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนอาจารย์ประจำ',
                'type' => 'input',
                'value' => 9.0,
                'indicator_id' => 6,
                'created_at' => '2025-09-16 03:40:32',
                'updated_at' => '2025-09-16 03:41:08',
            ),
            17 => 
            array (
                'id' => 82,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนรายวิชาที่มีอาจารย์พยาบาลประจำวิชาพยาบาลวิชาชีพสอนภาคปฏิบัติ ไม่เกิน 1:8',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 7,
                'created_at' => '2025-09-16 03:49:26',
                'updated_at' => '2025-09-16 03:49:26',
            ),
            18 => 
            array (
                'id' => 83,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนรายวิชาภาคปฏิบัติที่เปิดสอนในรายปีการศึกษาที่ครบวาระการรับรองสถาบัน',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 7,
                'created_at' => '2025-09-16 03:49:26',
                'updated_at' => '2025-09-16 03:49:26',
            ),
            19 => 
            array (
                'id' => 84,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 7,
                'created_at' => '2025-09-16 03:49:26',
                'updated_at' => '2025-09-16 03:49:26',
            ),
            20 => 
            array (
                'id' => 94,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 41,
                'created_at' => '2025-09-16 06:15:18',
                'updated_at' => '2025-09-16 06:15:18',
            ),
            21 => 
            array (
                'id' => 95,
                'variable_name' => 'input_1',
                'label_name' => 'เมื่อสิ้นปีการศึกษา',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 42,
                'created_at' => '2025-09-16 06:18:44',
                'updated_at' => '2025-09-16 06:18:44',
            ),
            22 => 
            array (
                'id' => 96,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนบุคลากรสายสนับสนุน',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 42,
                'created_at' => '2025-09-16 06:18:44',
                'updated_at' => '2025-09-16 06:18:44',
            ),
            23 => 
            array (
                'id' => 97,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 42,
                'created_at' => '2025-09-16 06:18:44',
                'updated_at' => '2025-09-16 06:18:44',
            ),
            24 => 
            array (
                'id' => 98,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนนักศึกษาชั้นปีที่สุดท้ายที่มีผลลัพธ์การเรียนรู้ครบตามที่กำหนดไว้ในหลักสูตร',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 44,
                'created_at' => '2025-09-16 06:31:19',
                'updated_at' => '2025-09-16 06:31:19',
            ),
            25 => 
            array (
                'id' => 99,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนนักศึกษาชั้นปีสุดท้ายทั้งหมด',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 44,
                'created_at' => '2025-09-16 06:31:19',
                'updated_at' => '2025-09-16 06:31:19',
            ),
            26 => 
            array (
                'id' => 100,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 44,
                'created_at' => '2025-09-16 06:31:19',
                'updated_at' => '2025-09-16 06:31:19',
            ),
            27 => 
            array (
                'id' => 101,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนรวมของผู้สอบผ่านในครั้งแรก ย้อนหลัง 3 ปี',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 45,
                'created_at' => '2025-09-16 06:36:00',
                'updated_at' => '2025-09-16 06:36:00',
            ),
            28 => 
            array (
                'id' => 102,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนรวมของผู้สำเร็จการศึกษาในปีการศึกษานั้นย้อนหลัง 3 ปี',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 45,
                'created_at' => '2025-09-16 06:36:00',
                'updated_at' => '2025-09-16 06:36:00',
            ),
            29 => 
            array (
                'id' => 103,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 45,
                'created_at' => '2025-09-16 06:36:00',
                'updated_at' => '2025-09-16 06:36:00',
            ),
            30 => 
            array (
                'id' => 104,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนรวมของผู้สอบผ่านในการสอบครั้งแรกย้อนหลัง 3 ปี',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 46,
                'created_at' => '2025-09-16 06:38:36',
                'updated_at' => '2025-09-16 06:38:36',
            ),
            31 => 
            array (
                'id' => 105,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนรวมของผู้สำเร็จการศึกษาในปีการศึกษานั้นๆย้อนหลัง 3 ปี',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 46,
                'created_at' => '2025-09-16 06:38:36',
                'updated_at' => '2025-09-16 06:38:36',
            ),
            32 => 
            array (
                'id' => 106,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 46,
                'created_at' => '2025-09-16 06:38:36',
                'updated_at' => '2025-09-16 06:38:36',
            ),
            33 => 
            array (
                'id' => 107,
                'variable_name' => 'input_1',
                'label_name' => 'ร้อยละของผลงานทางวิชาการ',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 49,
                'created_at' => '2025-09-16 06:47:50',
                'updated_at' => '2025-09-16 06:47:50',
            ),
            34 => 
            array (
                'id' => 108,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวหาร',
                'type' => 'defined',
                'value' => 70.0,
                'indicator_id' => 49,
                'created_at' => '2025-09-16 06:47:50',
                'updated_at' => '2025-09-16 06:47:50',
            ),
            35 => 
            array (
                'id' => 109,
                'variable_name' => 'defined_2',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 25.0,
                'indicator_id' => 49,
                'created_at' => '2025-09-16 06:47:50',
                'updated_at' => '2025-09-16 06:47:50',
            ),
            36 => 
            array (
                'id' => 110,
                'variable_name' => 'input_1',
                'label_name' => 'สาขาหลักทางการพยาบาล',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 50,
                'created_at' => '2025-09-16 06:51:18',
                'updated_at' => '2025-09-16 06:51:18',
            ),
            37 => 
            array (
                'id' => 111,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนรวมของอาจารย์พยาบาลประจำที่ปฏิบัติการพยาบาลย้านหลัง 3 ปี',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 53,
                'created_at' => '2025-09-16 06:59:33',
                'updated_at' => '2025-09-16 06:59:33',
            ),
            38 => 
            array (
                'id' => 112,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนรวมของอาจารย์พยาบาลประจำทั้งหมดย้อนหลัง 3 ปี',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 53,
                'created_at' => '2025-09-16 06:59:33',
                'updated_at' => '2025-09-16 06:59:33',
            ),
            39 => 
            array (
                'id' => 113,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 53,
                'created_at' => '2025-09-16 06:59:33',
                'updated_at' => '2025-09-16 06:59:33',
            ),
            40 => 
            array (
                'id' => 114,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนบันฑิตที่มีคุณลักษณะที่พึงประสงค์ ครบตามที่กำหนดไว้ในหลักสูตร',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 55,
                'created_at' => '2025-09-16 07:22:18',
                'updated_at' => '2025-09-16 07:22:18',
            ),
            41 => 
            array (
                'id' => 115,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนบันฑิตทั้งหมดในปีการศึกษานั้น',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 55,
                'created_at' => '2025-09-16 07:22:18',
                'updated_at' => '2025-09-16 07:22:18',
            ),
            42 => 
            array (
                'id' => 116,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 55,
                'created_at' => '2025-09-16 07:22:18',
                'updated_at' => '2025-09-16 07:22:18',
            ),
            43 => 
            array (
                'id' => 153,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนอาจารย์ประจำทั้งหมด',
                'type' => 'input',
                'value' => 0.0,
                'indicator_id' => 5,
                'created_at' => '2025-09-16 08:11:41',
                'updated_at' => '2025-09-16 08:22:34',
            ),
            44 => 
            array (
                'id' => 154,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนอาจารย์ประจำหลักสูตร',
                'type' => 'input',
                'value' => 0.0,
                'indicator_id' => 5,
                'created_at' => '2025-09-16 08:11:41',
                'updated_at' => '2025-09-16 08:22:34',
            ),
        ));
        
        
    }
}