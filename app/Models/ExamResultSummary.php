<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResultSummary extends Model
{
    protected $fillable = [
        'exam_id',
        'total_schools',
        'total_students',
        'eligible_students',
        'mean_score',
        'mean_grade',
        'pass_percentage',
        'top_student_id',
        'top_school_id',
        'highest_score',
        'lowest_score',
        'status',
        'processed_at',
    ];

    /**
     * The exam this summary belongs to
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Top performing student (optional relationship)
     */
    public function topStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'top_student_id');
    }

    /**
     * Top performing school (optional relationship)
     */
    public function topSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'top_school_id');
    }

    /**
     * Helper: check if summary is ready for UI
     */
    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    /**
     * Helper: check if still processing
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Helper: check if archived
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }
}