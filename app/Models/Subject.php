<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'slug', 'short', 'code', 'is_compulsory', 'is_active'];

    public function papers()
{
    return $this->hasMany(Paper::class);
}
}
