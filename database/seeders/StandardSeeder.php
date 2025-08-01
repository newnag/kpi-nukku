<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('standards')->insert([
            [
                'name' => 'มาตรฐานโครงสร้าง',

            ],
            [
                'name' => 'มาตรฐานกระบวนการ',

            ],
            [
                'name' => 'มาตรฐานผลลัพธ์',

            ],
        ]);
    }
}
