<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamConfiguration;
use App\Models\Paper;
use App\Jobs\ProcessExamChangesJob; // Import the centralized job
use Illuminate\Http\Request;

class ExamConfigurationController extends Controller
{
    /**
     * Store or update an exam configuration for a paper (inline editing).
     */
    public function store(Request $request, Exam $exam, $slug)
    {
        // Redirect if slug doesn't match
        if ($exam->slug !== $slug) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid exam slug'
            ], 404);
        }

        // Validate request
        $request->validate([
            'paper_id' => 'required|exists:papers,id',
            'max_score' => 'required|integer|min:1|max:1000',
            'weight' => 'required|numeric|min:0|max:100'
        ]);

        // Verify that the paper belongs to the exam's subject
        $paper = Paper::find($request->paper_id);
        if (!$paper || $paper->subject_id !== $exam->subject_id) {
            return response()->json([
                'success' => false,
                'message' => 'This paper does not belong to the exam\'s subject'
            ], 422);
        }

        try {
            // Update or create configuration
            $config = ExamConfiguration::updateOrCreate(
                [
                    'exam_id' => $exam->id,
                    'paper_id' => $request->paper_id,
                ],
                [
                    'max_score' => $request->max_score,
                    'weight' => $request->weight,
                ]
            );

            // Trigger the background job to refresh result snapshots
            // We only trigger this if the exam is already finalized
            if ($exam->status === 'finalized') {
                ProcessExamChangesJob::dispatch($exam);
            }

            return response()->json([
                'success' => true,
                'message' => 'Configuration saved successfully',
                'data' => [
                    'id' => $config->id,
                    'paper_id' => $config->paper_id,
                    'max_score' => $config->max_score,
                    'weight' => (float) $config->weight,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save configuration: ' . $e->getMessage()
            ], 500);
        }
    }
}