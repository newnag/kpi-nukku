<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'indicator_id',
        'collector',
    ];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collector');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'collector');
    }
}
