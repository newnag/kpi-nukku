<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'sequence',
        'indicator_id',
    ];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    public function evidences()
    {
        return $this->hasMany(Evidence::class, 'criteria_id');
    }
}
