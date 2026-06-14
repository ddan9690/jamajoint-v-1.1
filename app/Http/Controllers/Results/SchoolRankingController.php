<?php

namespace App\Http\Controllers\Results;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Services\Results\ExamResultSummaryService;
use Illuminate\Http\Request;

class SchoolRankingController extends Controller
{
    protected $examResultSummaryService;
    
    public function __construct(ExamResultSummaryService $examResultSummaryService)
    {
        $this->examResultSummaryService = $examResultSummaryService;
    }
    
    /**
     * Display school ranking page
     */
    public function index(Exam $exam, $slug)
    {
        // Redirect if slug doesn't match
        if ($exam->slug !== $slug) {
            return redirect()->route('results.school-ranking', [
                'exam' => $exam->id,
                'slug' => $exam->slug
            ]);
        }

        $user = auth()->user();
        
        // Check if user can view results
        if (!$this->canViewResults($user, $exam)) {
            abort(403, 'You do not have permission to view these results.');
        }

        // Check if exam is finalized
        if ($exam->status !== 'finalized') {
            abort(403, 'Results are not yet available. Exam is still in progress.');
        }

        // Load exam with relationships
        $exam->load(['subject', 'form', 'academicYear', 'term']);

        // Get school rankings from service
        $schoolRankings = $this->examResultSummaryService->getSchoolRankings($exam);
        
        // If no rankings exist, process them
        if ($schoolRankings->isEmpty()) {
            $this->examResultSummaryService->processExamResults($exam);
            $schoolRankings = $this->examResultSummaryService->getSchoolRankings($exam);
        }

        // Calculate overall totals from the same service
        $overall = $this->examResultSummaryService->getOverallTotals($exam);

        return view('dashboard.results.school-ranking', compact('exam', 'schoolRankings', 'overall'));
    }
    
    /**
     * Check if user can view results
     */
    private function canViewResults($user, $exam)
    {
        if ($user->can('manage-exams')) {
            return true;
        }
        
        if ($user->role === 'teacher') {
            return $exam->visibility === 'public';
        }
        
        return false;
    }
}