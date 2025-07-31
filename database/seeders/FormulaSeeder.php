<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormulaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('formulas')->insert([
            [
                //id-1
                'indicator_id' => 2,
                'condition' => 'ร้อยละของอาจารย์ประจำที่มีคุณวุฒิปริญญาเอก / 40*10',
                'TIMESTAMP' => now(),
            ],
            [
                //id-2
                'indicator_id' => 3,
                'condition' => 'จำนวนอาจารย์พยาบาลประจำที่มีคุณสมบัติตามเก็ณฑ์/จำนวนอาจารย์ประจำทั้งหมดของสถายบันรวมทุกคุณวุฒิการศึกษา*100',
                'TIMESTAMP' => now(),
            ],
            [
                //id-3
                'indicator_id' => 5,
                'condition' => 'ร้อยละของอาจารย์ประจำหลักสูตร/100*15',
                'TIMESTAMP' => now(),
            ],
            [
                //id-4
                'indicator_id' => 6,
                'condition' => 'if input == 1:6 คะแนน 15 ,if input== 1:7 คะแนน 10, if input == 1:8 คะแนน 5, if input == 1:9 คะแนน 0',
                'TIMESTAMP' => now(),
            ],
            [
                //id-5
                'indicator_id' => 7,
                'condition' => "if input น้อยกว่าร้อยละ 80 ของรายวิชาทั้งหมด (0 คะแนน)\n" .
                    "if input ร้อยละ 80 – 89 ของรายวิชาทั้งหมด (5 คะแนน)\n" .
                    "if input ร้อยละ 90 – 99 ของรายวิชาทั้งหมด (10 คะแนน)\n" .
                    "if input ร้อยละ 100 ของรายวิชาทั้งหมด (15 คะแนน)",
                'TIMESTAMP' => now(),
            ],
            //ผลลัพธ์
            [
                //id-6
                'indicator_id' => 37,
                'condition' => 'input ค่าเฉลี่ยของของการประเมินคณะผู้บริหาร',
                'TIMESTAMP' => now(),
            ],
          
            [
                //id-7
                'indicator_id' => 39,
                 'condition' => " if input ต่ำกว่า ร้อยละ 61 (0)\n" . 
                   "if input ร้อยละ 61 – 70 (1)\n" . 
                   "if input ร้อยละ 71 – 80 (2)\n" .
                   "if input ร้อยละ 81 – 90 (3)\n" .
                   "if input ร้อยละ 91 – 100 (5)",
                'TIMESTAMP' => now(),
            ],
            [
                //id-8
                'indicator_id' => 40,
                'condition' => "if input ต่ำกว่า ร้อยละ 61 (0)\n" . 
                    "if input ร้อยละ 61 – 70 (1)\n" . 
                    "if input ร้อยละ 71 – 80 (2)\n" .
                    "if input ร้อยละ 81 – 90 (3)\n" .
                    "if input ร้อยละ 91 – 100 (5)",
                'TIMESTAMP' => now(),
            ],
            [
                //id-9
                'indicator_id' => 41,
                'condition' => "if input น้อยกว่าร้อยละ 95 (0)\n" . 
                    "if input ร้อยละ 95 – 96.9 (3)\n" . 
                    "if input => 97 (5)\n",
                'TIMESTAMP' => now(),
            ],
            [
                //id-10
                'indicator_id' => 42,
                'condition' => "if input < 85 (0)\n" . 
                    "if input ร้อยละ 85 – 89.9 (3)\n" . 
                    "if input => 90 (5)\n",
                'TIMESTAMP' => now(),
            ],
            [
                //id-11
                'indicator_id' => 43,
                'condition' => "input คุณภาพการจัดการเรียนการสอน 
                     ระดับที่ 1 จำนวน.......คน คิดเป็นร้อยละ.........
                     ระดับที่ 2 จำนวน.......คน คิดเป็นร้อยละ.........
                     ระดับที่ 3 จำนวน.......คน คิดเป็นร้อยละ.........
                     ระดับที่ 4 จำนวน.......คน คิดเป็นร้อยละ.........",
                'TIMESTAMP' => now(),
            ],
            [
                //id-12
                'indicator_id' => 44,
                'condition' => "if input < 95 (0)\n" . 
                    "if input ร้อยละ 95 – 99(5)\n" . 
                    "if input => 100 (10)\n",
                'TIMESTAMP' => now(),
            ],
            [
                //id-13
                'indicator_id' => 45,
                 'condition' => "if input ต่ำกว่า ร้อยละ 60 (0)\n" . 
                    "if input ร้อยละ 60 ขี้นไป(10)\n" . 
                    "if input ร้อยละ 70 ขี้นไป (20)\n" .
                    "if input ร้อยละ 80 ขี้นไป (30)\n" .
                    "if input ร้อยละ 90 ขี้นไป (40)",
                'TIMESTAMP' => now(),
            ],
            [
                //id-14
                'indicator_id' => 46,
                 'condition' => "if input ต่ำกว่า ร้อยละ 80 (0)\n" . 
                    "if input ร้อยละ 80 ขี้นไป(2)\n" . 
                    "if input ร้อยละ 85 ขี้นไป (4)\n" .
                    "if input ร้อยละ 90 ขี้นไป (6)\n" .
                    "if input ร้อยละ 95 ขี้นไป (8)\n".
                    "if input ร้อยละ 100 (10)",
                'TIMESTAMP' => now(),
            ],
            [
                //id-15
                'indicator_id' => 49,
                'condition' => " ร้อยละของผลงานทางวิชาการ/70*25",
                'TIMESTAMP' => now(),
            ],
            [
                //id-16
                'indicator_id' => 53,
                'condition' => "if input < 60 (0)\n" . 
                    "if input ร้อยละ 60 – 69 (5)\n" . 
                    "if input ร้อยละ 70 – 79 (10)\n" .
                    "if input ร้อยละ 80 – 89 (15)\n" .
                    "if input ร้อยละ 90=> (20)\n",
                'TIMESTAMP' => now(),
            ],
            [
                //id-17
                'indicator_id' => 55,
                'condition' => "if input < 3.51(0)\n" . 
                    "if input => 3.51 (5)\n" . 
                    "if input =>10 (10)\n",
                'TIMESTAMP' => now(),
            ],
            
        

        ]);
    }
}
