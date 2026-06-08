<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'county_id',
        'school_id',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean', // Cast to boolean for easier checks
        ];
    }

    // --- Relationships ---

    public function school()
    {
        // Ensure 'school_id' is the foreign key in your users table
        return $this->belongsTo(School::class, 'school_id');
    }

    public function county()
    {

        return $this->belongsTo(County::class);
    }

    public function managedExams()
    {
        return $this->belongsToMany(Exam::class, 'exam_admins', 'user_id', 'exam_id');
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
}
