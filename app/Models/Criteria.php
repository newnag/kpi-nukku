<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'sequence',
        'indicator_id',
        'status',
    ];

    protected $table = 'criterias'; // matches migration

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }
}
