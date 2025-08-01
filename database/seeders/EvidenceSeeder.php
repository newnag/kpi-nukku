<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class EvidenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('evidences')->insert([
            [
                'name' => 'หลักฐานตัวอย่าง 1',
                'path' => 'uploads/evidence1.pdf',
                'type' => 'pdf',
                'detail' => 'รายละเอียดของหลักฐานตัวอย่าง 1',
                'criteria_id' => 1,
                'user_id' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'หลักฐานตัวอย่าง 2',
                'path' => 'uploads/evidence2.jpg',
                'type' => 'image',
                'detail' => 'รายละเอียดของหลักฐานตัวอย่าง 2',
                'criteria_id' => 2,
                'user_id' => 1,
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'หลักฐานตัวอย่าง 3',
                'path' => 'uploads/evidence3.docx',
                'type' => 'docx',
                'detail' => 'รายละเอียดของหลักฐานตัวอย่าง 3',
                'criteria_id' => 3,
                'user_id' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'หลักฐานตัวอย่าง 4',
                'path' => 'uploads/evidence4.pdf',
                'type' => 'pdf',
                'detail' => 'รายละเอียดของหลักฐานตัวอย่าง 4',
                'criteria_id' => 4,
                'user_id' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'หลักฐานตัวอย่าง 5',
                'path' => 'uploads/evidence5.png',
                'type' => 'image',
                'detail' => 'รายละเอียดของหลักฐานตัวอย่าง 5',
                'criteria_id' => 5,
                'user_id' => 2,
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'หลักฐานตัวอย่าง 6',
                'path' => 'uploads/evidence6.xlsx',
                'type' => 'excel',
                'detail' => 'รายละเอียดของหลักฐานตัวอย่าง 6',
                'criteria_id' => 1,
                'user_id' => 3,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'หลักฐานตัวอย่าง 7',
                'path' => 'uploads/evidence7.docx',
                'type' => 'docx',
                'detail' => 'รายละเอียดของหลักฐานตัวอย่าง 7',
                'criteria_id' => 2,
                'user_id' => 3,
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'หลักฐานตัวอย่าง 8',
                'path' => 'uploads/evidence8.pdf',
                'type' => 'pdf',
                'detail' => 'รายละเอียดของหลักฐานตัวอย่าง 8',
                'criteria_id' => 3,
                'user_id' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'หลักฐานตัวอย่าง 9',
                'path' => 'uploads/evidence9.jpg',
                'type' => 'image',
                'detail' => 'รายละเอียดของหลักฐานตัวอย่าง 9',
                'criteria_id' => 4,
                'user_id' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'หลักฐานตัวอย่าง 10',
                'path' => 'uploads/evidence10.pdf',
                'type' => 'pdf',
                'detail' => 'รายละเอียดของหลักฐานตัวอย่าง 10',
                'criteria_id' => 5,
                'user_id' => 1,
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'หลักฐานตัวอย่าง 11',
                'path' => 'uploads/evidence11.docx',
                'type' => 'docx',
                'detail' => 'รายละเอียดของหลักฐานตัวอย่าง 11',
                'criteria_id' => 2,
                'user_id' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'หลักฐานตัวอย่าง 12',
                'path' => 'uploads/evidence12.pptx',
                'type' => 'pptx',
                'detail' => 'รายละเอียดของหลักฐานตัวอย่าง 12',
                'criteria_id' => 1,
                'user_id' => 3,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'หลักฐานตัวอย่าง 13',
                'path' => 'uploads/evidence13.pdf',
                'type' => 'pdf',
                'detail' => 'รายละเอียดของหลักฐานตัวอย่าง 13',
                'criteria_id' => 3,
                'user_id' => 2,
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
