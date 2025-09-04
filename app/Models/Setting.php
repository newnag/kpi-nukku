<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'title',
        'notify_date1',
        'notify_date2',
        'message',
    ];

    public $timestamps = false; // ใช้ timestamps ถ้า migration มี $table->timestamps()

    protected $casts = [
        'notify_date1' => 'date',
        'notify_date2' => 'date',
    ];

    public static function getSetting()
    {
        return self::first();
    }
}
