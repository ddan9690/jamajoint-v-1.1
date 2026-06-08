<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingSystem extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'is_default'
    ];

    public function gradings()
    {
        return $this->hasMany(Grading::class);
    }
}