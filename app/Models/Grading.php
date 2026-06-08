<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grading extends Model
{
    protected $fillable = [
        'grading_system_id',
        'grade',
        'min_score',
        'max_score',
        'points',
        'remark'
    ];

    public function gradingSystem()
    {
        return $this->belongsTo(GradingSystem::class);
    }
}