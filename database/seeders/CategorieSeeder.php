<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'standard_id' => 1,
                'name' => 'ด้านองค์กรและการบริหารองค์กร',
                'max_score' => 10.00,
            ],
            [
                'standard_id' => 1,
                'name' => 'ด้านบุคลากร',
                'max_score' => 20.00,
            ],
            [
                'standard_id' => 1,
                'name' => 'ด้านการจัดการศึกษา',
                'max_score' => 150.00,
            ],
            [
                'standard_id' => 1,
                'name' => 'ด้านการวิจัยและนวัตกรรมและผลผลิตทางวิชาการ',
                'max_score' => 5.00,
            ],
            [
                'standard_id' => 1,
                'name' => 'ด้านการบริการวิชาการ/วิชาชีพแก่สังคม',
                'max_score' => 5.00,
            ],
            [
                'standard_id' => 1,
                'name' => 'ด้านการทำนุบำรุงศิลปะและวัฒนธรรม',
                'max_score' => 5.00,
            ],
            [
                // id-7
                'standard_id' => 1,
                'name' => 'ด้านนิสิตและนักศึกษา',
                'max_score' => 5.00,
            ],
            [
                // id-8
                'standard_id' => 2,
                'name' => 'ด้านองค์กรและการบริหารองค์กร',
                'max_score' => 70.00,
            ],
            [
                // id-9
                'standard_id' => 2,
                'name' => 'ด้านบุคลากร',
                'max_score' => 50.00,
            ],
            [
                // id-10
                'standard_id' => 2,
                'name' => 'ด้านการจัดการศึกษา',
                'max_score' => 70.00,
            ],
            [
                // id-11
                'standard_id' => 2,
                'name' => 'ด้านการวิจัยและนวัตกรรมและผลผลิตทางวิชาการ',
                'max_score' => 20.00,
            ],
            [
                // id-12
                'standard_id' => 2,
                'name' => 'ด้านการบริการวิชาการ/วิชาชีพแก่สังคม',
                'max_score' => 30.00,
            ],
            [
                // id-13
                'standard_id' => 2,
                'name' => 'ด้านการทำนุบำรุงศิลปะและวัฒนธรรม',
                'max_score' => 10.00,
            ],
            [
                // id-14
                'standard_id' => 2,
                'name' => 'ด้านนิสิตและนักศึกษา',
                'max_score' => 40.00,
            ],
            [
                // id-15
                'standard_id' => 3,
                'name' => 'ด้านองค์กรและการบริหารองค์กร',
                'max_score' => 25.00,
            ],
            [
                // id-16
                'standard_id' => 3,
                'name' => 'ด้านบุคลากร',
                'max_score' => 30.00,
            ],
            [
                // id-17
                'standard_id' => 3,
                'name' => 'ด้านการจัดการศึกษา',
                'max_score' => 75.00,
            ],
            [
                // id-18
                'standard_id' => 3,
                'name' => 'ด้านการวิจัยและนวัตกรรมและผลผลิตทางวิชาการ',
                'max_score' => 45.00,
            ],
            [
                // id-19
                'standard_id' => 3,
                'name' => 'ด้านการบริการวิชาการ/วิชาชีพแก่สังคม',
                'max_score' => 40.00,
            ],
            [
                // id-20
                'standard_id' => 3,
                'name' => 'ด้านการทำนุบำรุงศิลปะและวัฒนธรรม',
                'max_score' => 15.00,
            ],
            [
                // id-21
                'standard_id' => 3,
                'name' => 'ด้านนิสิตและนักศึกษา',
                'max_score' => 20.00,
            ],
        ]);
    }
}
