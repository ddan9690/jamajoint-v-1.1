<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Form;
use App\Models\Stream;
use App\Models\Student;
use App\Imports\StudentsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index($schoolId, $slug, $formId, $streamId)
    {
        $school = School::findOrFail($schoolId);
        $form = Form::findOrFail($formId);
        $stream = Stream::findOrFail($streamId);

        $students = Student::where('stream_id', $streamId)->get();

        return view('dashboard.students.index', compact('school', 'form', 'stream', 'students'));
    }

    public function store(Request $request, $schoolId, $slug, $formId, $streamId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'admission_number' => 'required|string|max:255',
            'index_number' => 'nullable|string|max:255',
            'gender' => 'required|in:M,F',
        ]);

        Student::create([
            'school_id' => $schoolId,
            'form_id' => $formId,
            'stream_id' => $streamId,
            'name' => $request->name,
            'admission_number' => $request->admission_number,
            'index_number' => $request->index_number,
            'gender' => $request->gender,
        ]);

        return response()->json(['message' => 'Student added successfully.']);
    }

    public function update(Request $request, $schoolId, $slug, $formId, $streamId, $studentId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'admission_number' => 'required|string|max:255',
            'index_number' => 'nullable|string|max:255',
            'gender' => 'required|in:M,F',
        ]);

        $student = Student::where('id', $studentId)
            ->where('stream_id', $streamId)
            ->firstOrFail();

        $student->update([
            'name' => $request->name,
            'admission_number' => $request->admission_number,
            'index_number' => $request->index_number,
            'gender' => $request->gender,
        ]);

        return response()->json(['message' => 'Student updated successfully.']);
    }

    public function destroy($schoolId, $slug, $formId, $streamId, $studentId)
    {
        $student = Student::where('id', $studentId)
            ->where('stream_id', $streamId)
            ->firstOrFail();

        $student->delete();

        return response()->json(['message' => 'Student deleted successfully.']);
    }

    public function showImportForm($schoolId, $slug, $formId)
    {
        $school = School::findOrFail($schoolId);
        $form = Form::findOrFail($formId);

        return view('dashboard.students.import', compact('school', 'form'));
    }

    public function processImport(Request $request, $schoolId, $slug, $formId)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ], [
            'file.mimes' => 'The file must be a valid Excel spreadsheet (.xlsx or .xls).',
        ]);

        try {
            Excel::import(new StudentsImport($schoolId, $formId), $request->file('file'));
            
            return response()->json([
                'success' => true, 
                'message' => 'Students and streams imported successfully!'
            ]);
            
        } catch (\InvalidArgumentException $e) {
            // Catches structural validation errors from StudentsImport
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 422);
            
        } catch (\Exception $e) {
            // General catch-all for other errors
            return response()->json([
                'success' => false, 
                'message' => 'An unexpected error occurred during import: ' . $e->getMessage()
            ], 422);
        }
    }
}