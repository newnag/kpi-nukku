<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Standard extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'name',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function indicators()
    {
        return $this->hasManyThrough(Indicator::class, Category::class, 'standard_id', 'type');
    }
}
