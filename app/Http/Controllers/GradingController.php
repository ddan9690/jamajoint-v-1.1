<?php

namespace App\Http\Controllers;

use App\Models\Grading;
use App\Models\GradingSystem;
use Illuminate\Http\Request;

class GradingController extends Controller
{
    protected $gradeMap = [
        'A' => 12, 'A-' => 11, 'B+' => 10, 'B' => 9, 'B-' => 8, 
        'C+' => 7, 'C' => 6, 'C-' => 5, 'D+' => 4, 'D' => 3, 'D-' => 2, 'E' => 1
    ];

    public function index(GradingSystem $gradingSystem)
    {
        $existingGrades = $gradingSystem->gradings()->get()->keyBy('grade');
        $gradeMap = $this->gradeMap;
        
        return view('dashboard.grading-systems.grades.index', compact('gradingSystem', 'gradeMap', 'existingGrades'));
    }

    public function store(Request $request, GradingSystem $gradingSystem)
    {
        $request->validate([
            'grades' => 'required|array',
            'grades.*.min' => 'required|numeric',
            'grades.*.max' => 'required|numeric|gt:grades.*.min', // Ensures max > min
        ]);

        // Process the incoming grades
        foreach ($request->grades as $grade => $scores) {
            if (isset($scores['min']) && isset($scores['max'])) {
                $gradingSystem->gradings()->updateOrCreate(
                    ['grade' => $grade],
                    [
                        'min_score' => $scores['min'],
                        'max_score' => $scores['max'],
                        'points'    => $this->gradeMap[$grade] ?? 0
                    ]
                );
            } else {
                // If inputs are empty, remove that grade record if it exists
                $gradingSystem->gradings()->where('grade', $grade)->delete();
            }
        }

        return response()->json(['message' => 'Grading system updated successfully.']);
    }

    public function destroy(GradingSystem $gradingSystem, Grading $grading)
    {
        $grading->delete();
        return response()->json(['message' => 'Grade deleted successfully.']);
    }
}