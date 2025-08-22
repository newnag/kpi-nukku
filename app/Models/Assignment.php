<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'indicator_id',
        'collector', // <- เก็บ user_id ไว้ในคอลัมน์ชื่อ collector
    ];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    // ความสัมพันธ์ไปยังผู้ใช้ที่เป็นผู้รวบรวมข้อมูล (rename เพื่อเลี่ยงชนกับคอลัมน์ collector)
    public function collectorUser()
    {
        return $this->belongsTo(User::class, 'collector', 'id');
    }
}
