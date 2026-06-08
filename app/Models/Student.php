<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'school_id',
        'form_id',
        'stream_id',
        'name',
        'admission_number',
        'index_number',
        'index_number',
        'gender',
    ];

    /**
     * Get the school that the student belongs to.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the form that the student belongs to.
     */
    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Get the stream that the student belongs to.
     */
    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function mark()
    {
        return $this->hasOne(Mark::class, 'student_id');
    }
}
