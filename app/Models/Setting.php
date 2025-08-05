<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'title',
        'day_notify',
    ];

    protected $casts = [
        'day_notify' => 'integer',
    ];

    public static function getSetting()
    {
        return self::first();
    }
}
