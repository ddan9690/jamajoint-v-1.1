<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Exam extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'subject_id',
        'academic_year_id',
        'term_id',
        'form_id',
        'grading_system_id',
        'status',
        'visibility',
        'mark_submission_mode',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function gradingSystem(): BelongsTo
    {
        return $this->belongsTo(GradingSystem::class);
    }

    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class, 'exam_schools');
    }

    public function configurations(): HasMany
    {
        return $this->hasMany(ExamConfiguration::class);
    }

    /**
     * Relationship with ExamAdmin model
     */
    public function examAdmins(): HasMany
    {
        return $this->hasMany(ExamAdmin::class);
    }

    /**
     * Updated Papers Relationship
     * Maps to Paper using the shared subject_id from the Exam model
     */
    public function papers(): HasMany
    {
       
        return $this->hasMany(Paper::class, 'subject_id', 'subject_id');
    }
}