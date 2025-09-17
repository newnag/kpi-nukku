<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StandardsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

       
        
        \DB::table('standards')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'มาตรฐานโครงสร้าง',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'มาตรฐานกระบวนการ',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'มาตรฐานผลลัพธ์',
            ),
        ));
        
        
    }
}