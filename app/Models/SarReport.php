<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SarReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'standard_id',
        'indicator_id',
        'criteria_id',
        'section1',
        'section2',
        'section4',
        'performance_result',
        'performance_report',
        'self_score',
        'comment',
        'submitted_at',
        'created_by',
        'updated_by',
    ];

    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    public function criteria()
    {
        return $this->belongsTo(Criteria::class);
    }

    public function evidences()
    {
        return $this->belongsToMany(Evidence::class, 'sar_report_evidence');
    }
}
