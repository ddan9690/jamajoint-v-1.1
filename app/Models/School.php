<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'county_id',
        'type',
        'is_active'
    ];

    /**
     * Relationship: A school belongs to a county.
     */
    public function county()
    {
        return $this->belongsTo(County::class);
    }

    /**
     * Relationship: A school has many streams.
     */
    public function streams()
    {
        return $this->hasMany(Stream::class);
    }

    /**
     * Relationship: A school has many students.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_schools');
    }
}
