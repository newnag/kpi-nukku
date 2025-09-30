<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('categories')->delete();
        
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'ด้านองค์กรและการบริหารองค์กร',
                'max_score' => 10.0,
                'standard_id' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'ด้านบุคลากร',
                'max_score' => 20.0,
                'standard_id' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'ด้านการจัดการศึกษา',
                'max_score' => 150.0,
                'standard_id' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'ด้านการวิจัยและนวัตกรรมและผลผลิตทางวิชาการ',
                'max_score' => 5.0,
                'standard_id' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'ด้านการบริการวิชาการ/วิชาชีพแก่สังคม',
                'max_score' => 5.0,
                'standard_id' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'ด้านการทำนุบำรุงศิลปะและวัฒนธรรม',
                'max_score' => 5.0,
                'standard_id' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'ด้านนิสิตและนักศึกษา',
                'max_score' => 5.0,
                'standard_id' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'ด้านองค์กรและการบริหารองค์กร',
                'max_score' => 70.0,
                'standard_id' => 2,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'ด้านบุคลากร',
                'max_score' => 50.0,
                'standard_id' => 2,
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'ด้านการจัดการศึกษา',
                'max_score' => 70.0,
                'standard_id' => 2,
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'ด้านการวิจัยและนวัตกรรมและผลผลิตทางวิชาการ',
                'max_score' => 20.0,
                'standard_id' => 2,
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'ด้านการบริการวิชาการ/วิชาชีพแก่สังคม',
                'max_score' => 30.0,
                'standard_id' => 2,
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'ด้านการทำนุบำรุงศิลปะและวัฒนธรรม',
                'max_score' => 10.0,
                'standard_id' => 2,
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'ด้านนิสิตและนักศึกษา',
                'max_score' => 40.0,
                'standard_id' => 2,
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'ด้านองค์กรและการบริหารองค์กร',
                'max_score' => 25.0,
                'standard_id' => 3,
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'ด้านบุคลากร',
                'max_score' => 30.0,
                'standard_id' => 3,
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'ด้านการจัดการศึกษา',
                'max_score' => 75.0,
                'standard_id' => 3,
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'ด้านการวิจัยและนวัตกรรมและผลผลิตทางวิชาการ',
                'max_score' => 45.0,
                'standard_id' => 3,
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'ด้านการบริการวิชาการ/วิชาชีพแก่สังคม',
                'max_score' => 40.0,
                'standard_id' => 3,
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'ด้านการทำนุบำรุงศิลปะและวัฒนธรรม',
                'max_score' => 15.0,
                'standard_id' => 3,
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'ด้านนิสิตและนักศึกษา',
                'max_score' => 20.0,
                'standard_id' => 3,
            ),
        ));
        
        
    }
}