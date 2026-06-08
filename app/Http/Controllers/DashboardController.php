<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $stats = [];
        $exams = collect(); // Initialize as an empty collection
        $tableTitle = 'Dashboard';

        // 1. Super Admin Logic
        if ($user->role === 'super_admin') {
            $stats = [
                ['label' => 'Total Exams', 'value' => Exam::count()],
                ['label' => 'System Users', 'value' => User::count()],
                ['label' => 'Active Schools', 'value' => School::count()],
                ['label' => 'Total Marks', 'value' => '12,450']
            ];
            $exams = Exam::latest()->take(10)->get();
            $tableTitle = 'System-Wide Activity';

            // 2. Exam Admin Logic
        } elseif ($user->role === 'exam_admin') {
            $stats = [
                ['label' => 'My Exams', 'value' => $user->managedExams()->count()],
                ['label' => 'My Schools', 'value' => School::count()],
                ['label' => 'Pending Submissions', 'value' => '12'],
                ['label' => 'Completion Rate', 'value' => '85%']
            ];
            $exams = $user->managedExams()->latest()->take(10)->get();
            $tableTitle = 'My Exam Sessions';

            // 3. Teacher Logic
        } else {
            // Use optional() to prevent crash if school is null
            $school = $user->school;

            $stats = [
                ['label' => 'Assigned Exams', 'value' => $school ? $school->exams()->count() : 0],
                ['label' => 'Pending Entries', 'value' => '0'],
                ['label' => 'Completed', 'value' => '0'],
                ['label' => 'Target', 'value' => 'N/A']
            ];

            $exams = $school ? $school->exams()->latest()->take(10)->get() : collect();
            $tableTitle = 'Current Exam Assignments';
        }

        return view('dashboard.index', compact('stats', 'exams', 'tableTitle'));
    }
}
