<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\School;
use Illuminate\Http\Request;

class ExamSchoolController extends Controller
{
    /**
     * Store a bulk of schools for an exam.
     */
    public function storeBulk(Request $request, Exam $exam)
    {
        $request->validate([
            'school_ids' => 'required|array',
            'school_ids.*' => 'exists:schools,id',
        ]);

        // Attaches only those not already present in the pivot table
        $exam->schools()->syncWithoutDetaching($request->school_ids);

        return back()->with('success', 'Selected schools registered for the exam successfully.');
    }

    /**
     * Remove a school from exam participation.
     */
    public function destroy(Exam $exam, School $school)
    {
        // Detach the specific school from the exam
        $exam->schools()->detach($school->id);

        return back()->with('success', 'School removed from participation successfully.');
    }
}