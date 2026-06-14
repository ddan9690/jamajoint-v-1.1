<?php

namespace App\Http\Controllers;

use App\Models\{Exam, School, ExamResultSummary};
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    /**
     * Show the main results index page (STRICT MODE)
     */
    public function index(Exam $exam, $slug)
    {
        // 🔁 Ensure correct slug
        if ($exam->slug !== $slug) {
            return redirect()->route('results.index', [
                'exam' => $exam->id,
                'slug' => $exam->slug
            ]);
        }

        $user = auth()->user();

      

        // 🔐 Access control
        if (!$this->canViewResults($user, $exam)) {
            abort(403, 'You do not have permission to view these results.');
        }

        /**
         * 🚨 STRICT RULE:
         * If we are here, exam MUST be finalized
         */
        if ($exam->status !== 'finalized') {
            abort(403, 'Exam is not finalized.');
        }

        // 📦 Load only required metadata
        $exam->load(['subject', 'form', 'academicYear', 'term']);

        /**
         * 🧠 SNAPSHOT FETCH (REQUIRED)
         */
        $summary = ExamResultSummary::where('exam_id', $exam->id)->first();

       

        /**
         * 🚨 HARD FAIL (NO FALLBACK)
         * Because system guarantees snapshot exists
         */
        if (!$summary) {
            abort(500, 'Result snapshot missing. Please contact administrator.');
        }

        /**
         * 📊 DASHBOARD SNAPSHOT DATA
         */
        $overviewData = [
            'participating_schools' => $summary->total_schools,
            'total_students'        => $summary->total_students,
            'eligible_students'     => $summary->eligible_students,
            'mean_score'            => $summary->mean_score,
            'mean_grade'            => $summary->mean_grade,
            'pass_percentage'       => $summary->pass_percentage,
        ];

        /**
         * 🏫 Optional teacher context
         */
        $school = null;

        if ($user->role === 'teacher' && $user->school_id) {
            $school = School::find($user->school_id);
        }

        return view('dashboard.results.index', compact(
            'exam',
            'overviewData',
            'school',
            'summary'
        ));
    }

    /**
     * 🔐 Access control
     */
    private function canViewResults($user, $exam)
    {
        if ($user->can('manage-exams')) {
            return true;
        }

        if ($user->role === 'teacher') {
            if ($exam->visibility === 'public') {
                return true;
            }

            return $exam->schools()
                ->where('schools.id', $user->school_id)
                ->exists();
        }

        return false;
    }
}