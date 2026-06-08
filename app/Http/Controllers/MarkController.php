<?php

namespace App\Http\Controllers;

use App\Models\{Exam, School, Mark, Stream, Paper};
use Illuminate\Http\Request;

class MarkController extends Controller
{
    public function showStreams(Exam $exam, $slug, School $school, $school_slug)
    {
        $exam->load(['subject', 'form', 'papers', 'academicYear', 'term']);

        $streams = $school->streams()
            ->where('form_id', $exam->form_id)
            ->with('students')
            ->get();

        foreach ($streams as $stream) {
            $studentIds = $stream->students->pluck('id');
            $stream->submission_map = $exam->papers->mapWithKeys(function ($paper) use ($exam, $studentIds) {
                $count = Mark::where('exam_id', $exam->id)
                    ->where('paper_id', $paper->id)
                    ->whereIn('student_id', $studentIds)
                    ->whereNotNull('score')
                    ->count();
                return [$paper->id => $count];
            });
            $stream->total_students = $studentIds->count();
        }

        return view('dashboard.marks.admin-streams', compact('exam', 'school', 'streams'));
    }

    public function showSubmissionStreams(Exam $exam, $examSlug, School $school, $schoolSlug)
    {
        $user = auth()->user();

        // 1. Security check: Teacher can only access their own school
        if ($user->role === 'teacher' && $user->school_id !== (int)$school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        // 2. Operational check: If admin hasn't opened submission to teachers, 
        // prevent direct access via URL guessing.
        if ($user->role === 'teacher' && $exam->mark_submission_mode !== 'teachers') {
            return redirect()->route('dashboard')->with('error', 'Mark submission for this exam is currently closed to teachers.');
        }

        // 3. Load necessary data for the view
        $exam->load(['subject', 'form', 'papers']);

        $streams = $school->streams()
            ->where('form_id', $exam->form_id)
            ->get();

        return view('dashboard.marks.submit-streams', compact('exam', 'school', 'streams'));
    }

    public function viewSubmissions(Exam $exam, $slug, School $school, $school_slug)
    {
        $exam->load(['subject', 'form', 'papers']);

        $streams = $school->streams()
            ->where('form_id', $exam->form_id)
            ->with('students')
            ->get();

        foreach ($streams as $stream) {
            $studentIds = $stream->students->pluck('id');
            $stream->submission_map = $exam->subject->papers->mapWithKeys(function ($paper) use ($exam, $studentIds) {
                $count = Mark::where('exam_id', $exam->id)
                    ->where('paper_id', $paper->id)
                    ->whereIn('student_id', $studentIds)
                    ->whereNotNull('score')
                    ->count();
                return [$paper->id => $count];
            });
            $stream->total_students = $studentIds->count();
        }

        return view('dashboard.marks.admin-view-mark-submission', compact('exam', 'school', 'streams'));
    }

    public function selectPaper(Exam $exam, $examSlug, School $school, $schoolSlug, Stream $stream)
    {
        $exam->load(['subject.papers']);

        return view('dashboard.marks.admin-select-paper', compact('exam', 'school', 'stream'));
    }

    public function showSubmissionStudents(Exam $exam, $examSlug, School $school, $schoolSlug, Stream $stream, Paper $paper)
    {
        $students = $stream->students()
            ->where('form_id', $exam->form_id)
            ->with(['mark' => function ($query) use ($exam, $paper) {
                $query->where('exam_id', $exam->id)
                    ->where('paper_id', $paper->id);
            }])
            ->orderBy('admission_number', 'asc')
            ->get();

        return view('dashboard.marks.students-mark-submit', compact('exam', 'school', 'stream', 'paper', 'students'));
    }

    public function adminShowStudents(Exam $exam, $slug, School $school, $school_slug, Stream $stream, Paper $paper)
    {
        $students = $stream->students()
            ->where('form_id', $exam->form_id)
            ->whereHas('mark', function ($query) use ($exam, $paper) {
                $query->where('exam_id', $exam->id)->where('paper_id', $paper->id);
            })
            ->with(['mark' => function ($query) use ($exam, $paper) {
                $query->where('exam_id', $exam->id)->where('paper_id', $paper->id);
            }])
            ->paginate(50);

        $studentsWithoutMarks = $stream->students()
            ->where('form_id', $exam->form_id)
            ->whereDoesntHave('mark', function ($query) use ($exam, $paper) {
                $query->where('exam_id', $exam->id)->where('paper_id', $paper->id);
            })
            ->orderBy('admission_number', 'asc')
            ->get();

        return view('dashboard.marks.admin-view-marks', compact('exam', 'school', 'stream', 'students', 'paper', 'studentsWithoutMarks'));
    }

    public function store(Request $request, Exam $exam, $slug, School $school, $school_slug, Stream $stream, Paper $paper)
    {
        $request->validate([
            'marks'   => 'required|array',
            'marks.*' => 'nullable|integer|min:0|max:100',
        ]);

        foreach ($request->marks as $studentId => $score) {
            if ($score !== null && $score !== '') {
                Mark::updateOrCreate(
                    [
                        'exam_id'    => $exam->id,
                        'student_id' => $studentId,
                        'paper_id'   => $paper->id,
                    ],
                    [
                        'school_id'  => $school->id,
                        'form_id'    => $exam->form_id,
                        'stream_id'  => $stream->id,
                        'subject_id' => $exam->subject_id,
                        'user_id'    => auth()->id(),
                        'score'      => (int) $score,
                    ]
                );
            }
        }

        // Redirect to the overview page instead of back to the form
        return redirect()->route('exams.school.view-submissions', [
            'exam' => $exam->id,
            'examSlug' => $exam->slug,
            'school' => $school->id,
            'schoolSlug' => $school->slug
        ])->with('success', 'Marks saved successfully.');
    }

    public function updateMark(Request $request, $exam, $exam_slug, $school, $school_slug, $stream, $paper, Mark $mark)
    {
        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $mark->update([
            'score' => $request->score,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mark updated successfully',
            'data' => $mark
        ]);
    }

    public function deleteMark($school, $school_slug, $stream, $paper, Mark $mark)
    {
        $mark->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mark deleted successfully'
        ]);
    }

    public function deleteAllMarks(Exam $exam, $examSlug, School $school, $schoolSlug, Stream $stream, Paper $paper)
    {
        // Perform the mass deletion
        Mark::where('exam_id', $exam->id)
            ->where('school_id', $school->id)
            ->where('stream_id', $stream->id)
            ->where('paper_id', $paper->id)
            ->delete();

        // Redirect to the school's submission overview page
        return redirect()->route('exams.school.view-submissions', [
            'exam'       => $exam->id,
            'examSlug'   => $exam->slug,
            'school'     => $school->id,
            'schoolSlug' => $school->slug
        ])->with('success', "All marks for '{$stream->name}' have been cleared.");
    }
}
