<?php

namespace App\Services\Results;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\ExamSchool;
use App\Models\ExamConfiguration;
use App\Models\Grading;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExamResultSummaryService
{
    /**
     * Get full summary data for the Exam Result Index page.
     * This is READ-ONLY (uses precomputed snapshot later).
     *
     * For now: computes from marks (we will later move to snapshots).
     */
    public function getExamSummary(Exam $exam): array
    {
        return [
            'exam' => $exam,
            'total_schools' => $this->getTotalSchools($exam),
            'total_students' => $this->getTotalEligibleStudents($exam),
            'mean_score' => $this->getMeanScore($exam),
            'mean_grade' => $this->getMeanGrade($exam),
            'pass_percentage' => $this->getPassPercentage($exam),
        ];
    }

    /**
     * Total participating schools (from exam_schools pivot)
     */
    public function getTotalSchools(Exam $exam): int
    {
        return ExamSchool::where('exam_id', $exam->id)->count();
    }

    /**
     * Eligible students = students who have marks for ALL required papers
     */
    public function getTotalEligibleStudents(Exam $exam): int
    {
        $requiredPapers = ExamConfiguration::where('exam_id', $exam->id)
            ->distinct('paper_id')
            ->count('paper_id');

        return Mark::where('exam_id', $exam->id)
            ->select('student_id')
            ->groupBy('student_id')
            ->havingRaw('COUNT(DISTINCT paper_id) = ?', [$requiredPapers])
            ->count();
    }

    /**
     * Mean score based on grade distribution weighted by points
     */
    public function getMeanScore(Exam $exam): float
    {
        $gradingSystemId = $exam->grading_system_id;

        $eligibleStudents = $this->getEligibleStudentsCollection($exam);

        $grades = Grading::where('grading_system_id', $gradingSystemId)
            ->get();

        // Map grade ranges for fast lookup
        $gradeMap = $grades->map(function ($grade) {
            return [
                'grade' => $grade->grade,
                'min' => $grade->min_score,
                'max' => $grade->max_score,
                'points' => $grade->points,
            ];
        });

        $totalStudents = $eligibleStudents->count();

        if ($totalStudents === 0) {
            return 0.0000;
        }

        // Count students per grade
        $gradeCounts = $eligibleStudents->groupBy('grade')->map->count();

        $weightedSum = 0;

        foreach ($gradeCounts as $grade => $count) {
            $points = $gradeMap->firstWhere('grade', $grade)['points'] ?? 0;
            $weightedSum += $count * $points;
        }

        return round($weightedSum / $totalStudents, 4);
    }

    /**
     * Determine mean grade from mean score
     */
    public function getMeanGrade(Exam $exam): ?string
    {
        $meanScore = $this->getMeanScore($exam);

        $grade = Grading::where('grading_system_id', $exam->grading_system_id)
            ->where('min_score', '<=', $meanScore)
            ->where('max_score', '>=', $meanScore)
            ->first();

        return $grade->grade ?? null;
    }

    /**
     * Pass percentage (C+ and above)
     */
    public function getPassPercentage(Exam $exam): float
    {
        $eligible = $this->getTotalEligibleStudents($exam);

        if ($eligible === 0) {
            return 0.0;
        }

        $passGrades = Grading::where('grading_system_id', $exam->grading_system_id)
            ->where('points', '>=', 7) // C+ threshold
            ->pluck('grade');

        $passCount = Mark::where('exam_id', $exam->id)
            ->select('student_id')
            ->groupBy('student_id')
            ->havingRaw('COUNT(DISTINCT paper_id) = ?', [
                ExamConfiguration::where('exam_id', $exam->id)
                    ->distinct('paper_id')
                    ->count('paper_id')
            ])
            ->whereIn('student_id', function ($q) use ($exam, $passGrades) {
                // This is simplified — final version will use precomputed student results
                $q->select('student_id')
                    ->from('student_exam_subject_results')
                    ->where('exam_id', $exam->id)
                    ->whereIn('grade', $passGrades);
            })
            ->count();

        return round(($passCount / $eligible) * 100, 2);
    }

    /**
     * Get eligible students with computed grades (temporary helper)
     * In final system this will come from snapshot table.
     */
    private function getEligibleStudentsCollection(Exam $exam): Collection
    {
        $requiredPapers = ExamConfiguration::where('exam_id', $exam->id)
            ->distinct('paper_id')
            ->count('paper_id');

        return Mark::where('exam_id', $exam->id)
            ->select('student_id')
            ->selectRaw('COUNT(DISTINCT paper_id) as papers_done')
            ->selectRaw('"" as grade') // placeholder for future snapshot
            ->groupBy('student_id')
            ->havingRaw('COUNT(DISTINCT paper_id) = ?', [$requiredPapers])
            ->get();
    }
}