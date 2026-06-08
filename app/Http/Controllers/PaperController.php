<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Paper;
use Illuminate\Http\Request;

class PaperController extends Controller
{
    /**
     * Store a new paper for the specified subject.
     */
    public function store(Request $request, Subject $subject)
    {
        // Validate that the input is numeric
        $request->validate([
            'paper_number' => 'required|numeric|min:1',
        ]);

        $formattedName = 'Paper ' . $request->paper_number;

        // Check if a paper with this name already belongs to this subject
        if ($subject->papers()->where('name', $formattedName)->exists()) {
            return back()->with('error', "{$formattedName} already exists for this subject.");
        }

        $subject->papers()->create([
            'name' => $formattedName,
        ]);

        return back()->with('success', 'Paper added successfully.');
    }

    /**
     * Remove the specified paper from storage.
     */
    public function destroy(Paper $paper)
    {
        $paper->delete();

        return back()->with('success', 'Paper deleted successfully.');
    }
}
