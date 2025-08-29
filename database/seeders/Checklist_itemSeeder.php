<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Checklist_itemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('checklist_items')->insert([
            // ตัวบ่งชี้ที่ 1
            [
                'required_items' => json_encode(['1', '2', '3', '4', '5']),
                'score' => 10,
                'sequence' => 1,
                'indicator_id' => 1,
            ],
            // ตัวบ่งชี้ที่ 4
            [
                'indicator_id' => 4,
                'required_items' => json_encode(['1', '2']),
                'score' => 5,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 8
            [
                'indicator_id' => 8,
                'required_items' => json_encode(['1', '2', '3', '4', '5', '6', '7']),
                'score' => 15,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 9
            [
                'indicator_id' => 9,
                'required_items' => json_encode(['1', '2', '3', '4', '5']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 10
            [
                'indicator_id' => 10,
                'required_items' => json_encode(['1', '2', '3', '4', '5']),
                'score' => 50,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 11
            [
                'indicator_id' => 11,
                'required_items' => json_encode(['1', '2', '3', '4', '5']),
                'score' => 20,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 12
            [
                'indicator_id' => 12,
                'required_items' => json_encode(['1', '2', '3']),
                'score' => 5,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 13
            [
                'indicator_id' => 13,
                'required_items' => json_encode(['1', '2', '3']),
                'score' => 5,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 14
            [
                'indicator_id' => 14,
                'required_items' => json_encode(['1', '2', '3']),
                'score' => 5,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 15
            [
                'indicator_id' => 15,
                'required_items' => json_encode(['1', '2', '3']),
                'score' => 5,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 16
            [
                'indicator_id' => 16,
                'required_items' => json_encode(['1', '2', '3', '4']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 17
            [
                'indicator_id' => 17,
                'required_items' => json_encode(['1', '2', '3', '4']),
                'score' => 15,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 18
            [
                'indicator_id' => 18,
                'required_items' => json_encode(['1', '2', '3', '4']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 19
            [
                'indicator_id' => 19,
                'required_items' => json_encode(['1', '2', '3', '4']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 20
            [
                'indicator_id' => 20,
                'required_items' => json_encode(['1', '2', '3', '4', '5']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 21
            [
                'indicator_id' => 21,
                'required_items' => json_encode(['1', '2', '3', '4']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 22
            [
                'indicator_id' => 22,
                'required_items' => json_encode(['1', '2', '3', '4', '5']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 23
            [
                'indicator_id' => 23,
                'required_items' => json_encode(['1', '2', '3', '4', '5']),
                'score' => 15,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 24
            [
                'indicator_id' => 24,
                'required_items' => json_encode(['1', '2', '3', '4', '5']),
                'score' => 15,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 25
            [
                'indicator_id' => 25,
                'required_items' => json_encode(['1', '2', '3', '4', '5']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 26
            [
                'indicator_id' => 26,
                'required_items' => json_encode(['1', '2', '3', '4', '5']),
                'score' => 25,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 27
            [
                'indicator_id' => 27,
                'required_items' => json_encode(['1', '2', '3', '4', '5']),
                'score' => 25,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 28
            [
                'indicator_id' => 28,
                'required_items' => json_encode(['1', '2', '3', '4']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 29
            [
                'indicator_id' => 29,
                'required_items' => json_encode(['1', '2', '3']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 30
            [
                'indicator_id' => 30,
                'required_items' => json_encode(['1', '2', '3', '4', '5']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 31
            [
                'indicator_id' => 31,
                'required_items' => json_encode(['1', '2', '3']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 32
            [
                'indicator_id' => 32,
                'required_items' => json_encode(['1', '2', '3', '4']),
                'score' => 15,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 33
            [
                'indicator_id' => 33,
                'required_items' => json_encode(['1', '2', '3', '4']),
                'score' => 15,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 34
            [
                'indicator_id' => 34,
                'required_items' => json_encode(['1', '2', '3']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 35
            [
                'indicator_id' => 35,
                'required_items' => json_encode(['1', '2', '3', '4']),
                'score' => 20,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 36
            [
                'indicator_id' => 36,
                'required_items' => json_encode(['1', '2', '3', '4']),
                'score' => 20,
                'sequence' => 1,
            ],
            // ผลลัพธ์
            // ตัวบ่งชี้ที่ 1
            [
                'indicator_id' => 37,
                'required_items' => json_encode(['1', '2', '3', '4']),
                'score' => 20,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 2
            [
                'indicator_id' => 38,
                'required_items' => json_encode(['1', '2']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 11
            [
                'indicator_id' => 47,
                'required_items' => json_encode(['1', '2']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 12
            [
                'indicator_id' => 48,
                'required_items' => json_encode(['1', '2', '3', '4', '5']),
                'score' => 5,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 14
            [
                'indicator_id' => 50,
                'required_items' => json_encode(['1', '2', '3', '4']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 15
            [
                'indicator_id' => 51,
                'required_items' => json_encode(['1', '2']),
                'score' => 10,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 16
            [
                'indicator_id' => 52,
                'required_items' => json_encode(['1', '2']),
                'score' => 20,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 18
            [
                'indicator_id' => 54,
                'required_items' => json_encode(['1', '2', '3']),
                'score' => 25,
                'sequence' => 1,
            ],
            // ตัวบ่งชี้ที่ 20
            [
                'indicator_id' => 56,
                'required_items' => json_encode(['1', '2', '3']),
                'score' => 10,
                'sequence' => 1,
            ],
        ]);
    }
}
