<?php

namespace App\Jobs;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\ExamConfiguration;
use App\Models\Grading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessExamResultSummary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $examId;

    /**
     * Create job instance
     */
    public function __construct(int $examId)
    {
        $this->examId = $examId;
    }

    /**
     * Execute processing
     */
    public function handle(): void
    {
        DB::transaction(function () {

            $exam = Exam::findOrFail($this->examId);

            $gradingSystemId = $exam->grading_system_id;

            $configCount = ExamConfiguration::where('exam_id', $exam->id)
                ->distinct('paper_id')
                ->count('paper_id');

            /**
             * STEP 1: Load ALL marks once
             */
            $marks = Mark::where('exam_id', $exam->id)
                ->get()
                ->groupBy('student_id');

            /**
             * STEP 2: Load grading system
             */
            $grading = Grading::where('grading_system_id', $gradingSystemId)
                ->get();

            /**
             * Storage containers (fast in-memory processing)
             */
            $studentResults = [];
            $gradeDistribution = [];

            $eligibleStudents = 0;

            /**
             * STEP 3: Process each student ONCE
             */
            foreach ($marks as $studentId => $studentMarks) {

                if ($studentMarks->count() < $configCount) {
                    continue; // not eligible
                }

                $eligibleStudents++;

                $totalScore = 0;

                // Calculate weighted subject score per paper
                foreach ($studentMarks as $mark) {

                    $config = ExamConfiguration::where('exam_id', $exam->id)
                        ->where('paper_id', $mark->paper_id)
                        ->first();

                    if (!$config) continue;

                    $weighted = ($mark->score / $config->max_score) * $config->weight;

                    $totalScore += $weighted;
                }

                /**
                 * STEP 4: Assign grade
                 */
                $gradeRow = $grading->first(function ($g) use ($totalScore) {
                    return $totalScore >= $g->min_score && $totalScore <= $g->max_score;
                });

                $grade = $gradeRow->grade ?? null;
                $points = $gradeRow->points ?? 0;

                /**
                 * STEP 5: Store student result (in memory first)
                 */
                $studentResults[$studentId] = [
                    'exam_id' => $exam->id,
                    'student_id' => $studentId,
                    'total_score' => round($totalScore, 4),
                    'grade' => $grade,
                    'points' => $points,
                ];

                /**
                 * STEP 6: Build grade distribution
                 */
                if (!isset($gradeDistribution[$grade])) {
                    $gradeDistribution[$grade] = [
                        'count' => 0,
                        'points' => $points,
                    ];
                }

                $gradeDistribution[$grade]['count']++;
            }

            /**
             * STEP 7: Persist student results
             */
            DB::table('student_exam_results')->insert(array_values($studentResults));

            /**
             * STEP 8: Persist grade distribution
             */
            foreach ($gradeDistribution as $grade => $data) {
                DB::table('grade_distribution')->insert([
                    'exam_id' => $exam->id,
                    'grade' => $grade,
                    'count' => $data['count'],
                    'points' => $data['points'],
                ]);
            }

            /**
             * STEP 9: Compute exam summary
             */
            $weightedSum = 0;

            foreach ($gradeDistribution as $data) {
                $weightedSum += $data['count'] * $data['points'];
            }

            $meanScore = $eligibleStudents > 0
                ? round($weightedSum / $eligibleStudents, 4)
                : 0;

            $meanGrade = $grading->first(function ($g) use ($meanScore) {
                return $meanScore >= $g->min_score && $meanScore <= $g->max_score;
            });

            $passCount = collect($studentResults)
                ->filter(fn ($r) => $r['points'] >= 7)
                ->count();

            $passPercentage = $eligibleStudents > 0
                ? round(($passCount / $eligibleStudents) * 100, 2)
                : 0;

            /**
             * STEP 10: Save exam summary snapshot
             */
            DB::table('exam_summaries')->updateOrInsert(
                ['exam_id' => $exam->id],
                [
                    'total_schools' => DB::table('exam_schools')
                        ->where('exam_id', $exam->id)
                        ->count(),

                    'total_students' => count($marks),
                    'eligible_students' => $eligibleStudents,

                    'mean_score' => $meanScore,
                    'mean_grade' => $meanGrade->grade ?? null,

                    'pass_percentage' => $passPercentage,

                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        });
    }
}