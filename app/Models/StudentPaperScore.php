<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPaperScore extends Model
{
    protected $table = 'student_paper_scores';
    
    protected $fillable = [
        'exam_id',
        'student_id',
        'paper_id',
        'paper_name',
        'max_score',
        'weight',
        'score',
        'grade',
        'points',
        'is_submitted',
    ];

    protected $casts = [
        'max_score' => 'integer',
        'weight' => 'decimal:2',
        'score' => 'integer',
        'points' => 'integer',
        'is_submitted' => 'boolean',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function paper(): BelongsTo
    {
        return $this->belongsTo(Paper::class);
    }
}