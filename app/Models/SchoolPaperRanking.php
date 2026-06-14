<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolPaperRanking extends Model
{
    protected $table = 'school_paper_rankings';
    
    protected $fillable = [
        'exam_id',
        'paper_id',
        'paper_name',
        'school_id',
        'school_name',
        'rank',
        'mean_score',
        'mean_grade',
        'total_students',
        'submitted_count',
    ];

    protected $casts = [
        'mean_score' => 'decimal:2',
        'rank' => 'integer',
        'total_students' => 'integer',
        'submitted_count' => 'integer',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function paper(): BelongsTo
    {
        return $this->belongsTo(Paper::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}