<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'title',
        'day_notify',
        'notify_date',
        'message',
    ];

    public $timestamps = true; // ใช้ timestamps ถ้า migration มี $table->timestamps()

    protected $casts = [
        'day_notify'   => 'integer',
        'notify_date'  => 'date',
    ];

    public static function getSetting()
    {
        return self::first();
    }
}
