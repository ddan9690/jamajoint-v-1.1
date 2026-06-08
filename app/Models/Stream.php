<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{

    protected $fillable = [
        'school_id',
        'form_id',
        'name',
        'slug',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
