<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    // Get all streams for a specific form within a specific school
public function streamsForSchool($schoolId) {
    return $this->hasMany(Stream::class)->where('school_id', $schoolId);
}
}
