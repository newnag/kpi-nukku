<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // ดึง indicators และ users ที่มีอยู่
        $indicators = DB::table('indicators')->pluck('id');
        $users      = DB::table('users')->pluck('id');

        if ($indicators->isEmpty() || $users->isEmpty()) {
            $this->command->warn('⚠️ ไม่มี indicators หรือ users ในฐานข้อมูล จึงยังไม่สามารถสร้าง assignments ได้');
            return;
        }

        // สุ่มแมป indicators กับ users
        foreach ($indicators as $indicatorId) {
            DB::table('assignments')->insert([
                'indicator_id' => $indicatorId,
                'collector'    => $users->random(),  // สุ่ม user เป็นคนเก็บ
            ]);
        }

    }
}
