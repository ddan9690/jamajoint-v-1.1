<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolRanking extends Model
{
    protected $table = 'school_rankings';
    
    protected $fillable = [
        'exam_id',
        'school_id',
        'school_name',
        'county_id',
        'county_name',
        'school_type',
        'rank',
        'mean_score',
        'mean_grade',
        'total_students',
        'total_males',
        'total_females',
        'pass_rate',
        'grade_distribution',
    ];

    protected $casts = [
        'grade_distribution' => 'array',
        'mean_score' => 'decimal:2',
        'pass_rate' => 'decimal:2',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }
}