<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamConfiguration;
use Illuminate\Http\Request;

class ExamConfigurationController extends Controller
{
    public function index($id, $slug)
    {
        $exam = Exam::with('subject.papers')->where('id', $id)->where('slug', $slug)->firstOrFail();
        
        $configs = ExamConfiguration::where('exam_id', $exam->id)->get()->keyBy('paper_id');

        return view('dashboard.exams.configure', compact('exam', 'configs'));
    }

    public function store(Request $request, $id, $slug)
    {
        // 1. Verify the exam exists
        $exam = Exam::where('id', $id)->where('slug', $slug)->firstOrFail();

        // 2. Validate the input
        $request->validate([
            'configurations' => 'required|array',
            'configurations.*.max_score' => 'required|numeric|min:0',
            'configurations.*.weight' => 'required|numeric|min:0',
        ]);

        // 3. Process each configuration
        foreach ($request->configurations as $paperId => $data) {
            ExamConfiguration::updateOrCreate(
                [
                    'exam_id' => $exam->id, 
                    'paper_id' => $paperId
                ],
                [
                    'max_score' => $data['max_score'], 
                    'weight' => $data['weight']
                ]
            );
        }

        // 4. Redirect back with feedback
        return back()->with('success', 'Exam configuration saved successfully.');
    }
}