<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentRanking extends Model
{
    protected $table = 'student_rankings';
    
    protected $fillable = [
        'exam_id',
        'student_id',
        'admission_number',
        'student_name',
        'gender',
        'school_id',
        'school_name',
        'stream_id',
        'stream_name',
        'form_id',
        'weighted_average',
        'grade',
        'points',
        'overall_rank',
        'school_rank',
        'has_full_marks',
    ];

    protected $casts = [
        'weighted_average' => 'decimal:2',
        'points' => 'integer',
        'overall_rank' => 'integer',
        'school_rank' => 'integer',
        'has_full_marks' => 'boolean',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Scope for top N students overall
     */
    public function scopeTopOverall($query, $limit = 50)
    {
        return $query->orderBy('overall_rank', 'asc')->limit($limit);
    }

    /**
     * Scope for top N boys
     */
    public function scopeTopBoys($query, $limit = 20)
    {
        return $query->where('gender', 'M')->orderBy('overall_rank', 'asc')->limit($limit);
    }

    /**
     * Scope for top N girls
     */
    public function scopeTopGirls($query, $limit = 20)
    {
        return $query->where('gender', 'F')->orderBy('overall_rank', 'asc')->limit($limit);
    }

    /**
     * Scope for students in a specific school
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId)->orderBy('school_rank', 'asc');
    }
}