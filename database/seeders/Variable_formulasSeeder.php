<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Variable_formulasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('variable_formulas')->insert([
            // ตัวบ่งชี้ที่ 2
            [
                'formula_id' => 1, // สูตรที่ 1
                'variable_id' => 1, // ตัวแปรที่ 1
            ],
            [
                'formula_id' => 1,
                'variable_id' => 2,

            ],

            [
                'formula_id' => 1,
                'variable_id' => 3,

            ],

            [
                'formula_id' => 2, // สูตรที่ 2
                'variable_id' => 4, // ตัวแปรที่
            ],
            // ตัวบ่งชี้ที่ 3
            [
                'formula_id' => 2,
                'variable_id' => 5,

            ],
            [
                'formula_id' => 2,
                'variable_id' => 6,

            ],
            [
                'formula_id' => 2,
                'variable_id' => 7,

            ],
            [
                'formula_id' => 2,
                'variable_id' => 8,

            ],
            // ตัวบ่งชี้ที่ 5
            [
                'formula_id' => 3,
                'variable_id' => 9,

            ],
            [
                'formula_id' => 3,
                'variable_id' => 10,

            ],
            [
                'formula_id' => 3,
                'variable_id' => 11,

            ],
            [
                'formula_id' => 3,
                'variable_id' => 12,

            ],
            // ตัวบ่งชี้ที่ 6
            [
                'formula_id' => 4,
                'variable_id' => 13,

            ],
            // ตัวบ่งชี้ที่ 7
            [
                'formula_id' => 5,
                'variable_id' => 14,
            ],

            // ผลลัพธ์
            [
                'formula_id' => 6, // สูตรที่ 6
                'variable_id' => 15, //
            ],
            [
                'formula_id' => 7,
                'variable_id' => 16, // ตัวแปรที่ 13
            ],
            [
                'formula_id' => 8,
                'variable_id' => 17, // ตัวแปรที่ 14
            ],
            [
                'formula_id' => 9,
                'variable_id' => 18, // ตัวแปรที่ 15
            ],
            [
                'formula_id' => 10,
                'variable_id' => 19, // ตัวแปรที่ 16
            ],
            [
                'formula_id' => 11,
                'variable_id' => 20, // ตัวแปรที่ 17
            ],
            [
                'formula_id' => 11,
                'variable_id' => 21, // ตัวแปรที่ 18
            ],
            [
                'formula_id' => 11,
                'variable_id' => 22, // ตัวแปรที่ 19
            ],
            [
                'formula_id' => 11,
                'variable_id' => 23, // ตัวแปรที่ 20
            ],
            [
                'formula_id' => 11,
                'variable_id' => 24, // ตัวแปรที่ 21
            ],
            [
                'formula_id' => 11,
                'variable_id' => 25, // ตัวแปรที่ 22
            ],
            [
                'formula_id' => 11,
                'variable_id' => 26, // ตัวแปรที่ 23
            ],
            [
                'formula_id' => 11,
                'variable_id' => 27, // ตัวแปรที่ 24
            ],

            [
                'formula_id' => 12,
                'variable_id' => 28, // ตัวแปรที่ 25
            ],
            [
                'formula_id' => 13,
                'variable_id' => 29, // ตัวแปรที่ 26
            ],
            [
                'formula_id' => 14,
                'variable_id' => 30, // ตัวแปรที่ 27
            ],
            [
                'formula_id' => 15,
                'variable_id' => 31, // ตัวแปรที่ 28
            ],
            [
                'formula_id' => 15,
                'variable_id' => 32, // ตัวแปรที่ 29
            ],
            [
                'formula_id' => 15,
                'variable_id' => 33, // ตัวแปรที่ 30
            ],
            [
                'formula_id' => 15,
                'variable_id' => 34, // ตัวแปรที่ 30
            ],
            [
                'formula_id' => 16,
                'variable_id' => 35, // ตัวแปรที่ 31
            ],
            [
                'formula_id' => 17,
                'variable_id' => 36, // ตัวแปรที่ 32
            ],

        ]);
    }
}
