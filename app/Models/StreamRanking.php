<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreamRanking extends Model
{
    protected $table = 'stream_rankings';
    
    protected $fillable = [
        'exam_id',
        'stream_id',
        'stream_name',
        'school_id',
        'school_name',
        'form_id',
        'rank',
        'mean_score',
        'mean_grade',
        'total_students',
        'total_males',
        'total_females',
    ];

    protected $casts = [
        'mean_score' => 'decimal:2',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    
}