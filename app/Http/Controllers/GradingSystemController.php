<?php

namespace App\Http\Controllers;

use App\Models\GradingSystem;
use Illuminate\Http\Request;

class GradingSystemController extends Controller
{
    /**
     * Display a listing of the grading systems.
     */
    public function index()
    {
        $gradingSystems = GradingSystem::all();
        return view('dashboard.grading-systems.index', compact('gradingSystems'));
    }

    /**
     * Store a newly created grading system.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        GradingSystem::create([
            'name' => $request->name,
            'is_active' => true,
            'is_default' => false,
        ]);

        return response()->json(['message' => 'Grading system created successfully.']);
    }

    /**
     * Update the specified grading system.
     */
    public function update(Request $request, GradingSystem $gradingSystem)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $gradingSystem->update([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Grading system updated successfully.']);
    }

    /**
     * Remove the specified grading system.
     */
    public function destroy(GradingSystem $gradingSystem)
    {
        if ($gradingSystem->is_default) {
            return response()->json(['message' => 'Cannot delete the default system.'], 422);
        }

        $gradingSystem->delete();

        return response()->json(['message' => 'Grading system deleted successfully.']);
    }
}