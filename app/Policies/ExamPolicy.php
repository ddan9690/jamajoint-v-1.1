<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExamPolicy
{
    /**
     * Determine if the user can view or manage the exam.
     */
    public function view(User $user, Exam $exam): bool
    {
        // 1. Super Admins: Access to everything
        if ($user->role === 'super_admin') {
            return true;
        }

        // 2. Exam Admins: Access if linked in the pivot table
        if ($user->role === 'exam_admin') {
            return $user->managedExams()->where('exam_id', $exam->id)->exists();
        }

        // 3. Teachers: Access if their school participates in the exam
        if ($user->role === 'teacher') {
            return $exam->schools()->where('schools.id', $user->school_id)->exists();
        }

        return false;
    }
}