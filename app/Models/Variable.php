<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    protected $fillable = [
        'label',
        'variable_name',
        'status',
        'value',
        'indicator_id',
    ];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    public function formulas()
    {
        return $this->belongsToMany(Formula::class, 'variable_formulas');
    }
}
