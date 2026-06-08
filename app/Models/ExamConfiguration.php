<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamConfiguration extends Model
{
    /**
     * The attributes that are mass assignable.
     * * @var array
     */
    protected $fillable = [
        'exam_id', 
        'paper_id', 
        'max_score', 
        'weight'
    ];

    /**
     * Get the exam that this configuration belongs to.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the paper associated with this configuration.
     */
    public function paper()
    {
        return $this->belongsTo(Paper::class);
    }
}