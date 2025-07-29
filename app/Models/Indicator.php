<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Indicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'year',
        'code',
        'description',
        'condition',
        'annotation',
        'deadline',
        'status',
        'comment',
        'score_acc',
        'max_score',
        'type',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'deadline' => 'datetime:Y-m-d',
        'score_acc' => 'decimal:5,2',
        'max_score' => 'decimal:5,2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'type');
    }

    public function criterias()
    {
        return $this->hasMany(Criteria::class);
    }

    public function variables()
    {
        return $this->hasMany(Variable::class);
    }

    public function formulas()
    {
        return $this->hasMany(Formula::class);
    }

    public function checklistItems()
    {
        return $this->hasMany(Checklist_item::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function evidences()
    {
        return $this->hasManyThrough(Evidence::class, Criteria::class);
    }

}
