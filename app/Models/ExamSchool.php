<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamSchool extends Model
{
    protected $fillable = ['exam_id', 'school_id'];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}