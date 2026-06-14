<?php

namespace App\Http\Controllers;

use App\Models\{Exam, Subject, Form, GradingSystem, AcademicYear, Term};
use App\Models\Mark;
use App\Models\School;
use App\Jobs\ProcessExamChangesJob;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with(['subject', 'form', 'term', 'academicYear'])->latest()->get();
        return view('dashboard.exams.index', compact('exams'));
    }

    public function create()
    {
        return view('dashboard.exams.create', $this->getFormData());
    }

    public function store(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', 1)->firstOrFail();
        $activeTerm = Term::where('is_active', 1)->firstOrFail();

        $validated = $request->validate($this->rules());

        $validated['academic_year_id'] = $activeYear->id;
        $validated['term_id'] = $activeTerm->id;
        $validated['slug'] = Str::slug($request->name);

        Exam::create($validated);
        return redirect()->route('exams.index')->with('success', 'Exam created successfully.');
    }

    public function edit(Exam $exam)
    {
        return view('dashboard.exams.edit', array_merge(['exam' => $exam], $this->getFormData()));
    }

    public function update(Request $request, Exam $exam)
    {
        $activeYear = AcademicYear::where('is_active', 1)->firstOrFail();
        $activeTerm = Term::where('is_active', 1)->firstOrFail();

        $validated = $request->validate($this->rules());

        $validated['academic_year_id'] = $activeYear->id;
        $validated['term_id'] = $activeTerm->id;
        $validated['slug'] = Str::slug($request->name);

        $exam->update($validated);
        return redirect()->route('exams.index')->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('exams.index')->with('success', 'Exam deleted.');
    }

    public function show(Request $request, Exam $exam)
    {
        $user = auth()->user();
        $exam->load(['subject.papers', 'form', 'configurations.paper']);

        if ($exam->status === 'finalized') {
            $canViewResults = false;

            if ($user->can('manage-exams')) {
                $canViewResults = true;
            } elseif ($user->role === 'teacher' && $exam->visibility === 'public') {
                $canViewResults = true;
            } elseif ($user->role === 'teacher' && $user->school_id) {
                $canViewResults = true;
            }

            if ($canViewResults) {
                return redirect()->route('results.index', [
                    'exam' => $exam->id,
                    'slug' => $exam->slug
                ]);
            }

            abort(403, 'Results are not yet available for public viewing.');
        }

        if ($user->can('manage-exams')) {
            $schools = $exam->schools()
                ->with(['county'])
                ->withCount(['students' => fn($q) => $q->where('form_id', $exam->form_id)])
                ->get();

            $submissionCounts = Mark::where('exam_id', $exam->id)
                ->whereNotNull('score')
                ->select('school_id', 'paper_id', DB::raw('count(*) as count'))
                ->groupBy('school_id', 'paper_id')
                ->get()
                ->groupBy('school_id');

            $schools->each(function ($school) use ($submissionCounts) {
                $school->submission_map = $submissionCounts->get($school->id, collect())->pluck('count', 'paper_id');
            });

            $registeredCount = $schools->count();
            $allSchools = School::whereNotIn('id', $exam->schools->pluck('id'))->paginate(40);
            $examAdmins = $exam->examAdmins()->with('user')->get();

            return view('dashboard.exams.show', compact(
                'exam', 'schools', 'allSchools', 'examAdmins', 'registeredCount'
            ));
        }

        if ($exam->mark_submission_mode === 'teachers' && $user->school_id) {
            $school = School::find($user->school_id);

            $hasMarks = Mark::where('exam_id', $exam->id)
                ->where('school_id', $user->school_id)
                ->whereNotNull('score')
                ->exists();

            if ($hasMarks) {
                return redirect()->route('exams.school.view-submissions', [
                    'exam' => $exam->id, 'examSlug' => $exam->slug,
                    'school' => $school->id, 'schoolSlug' => $school->slug
                ]);
            }

            return redirect()->route('marks.submit-streams', [
                'exam' => $exam->id, 'examSlug' => $exam->slug,
                'school' => $school->id, 'schoolSlug' => $school->slug
            ]);
        }

        $school = School::with(['streams' => function ($q) use ($exam) {
            $q->where('form_id', $exam->form_id);
        }])->find($user->school_id);

        $totalStudents = 0;
        $totalSubmitted = 0;

        if ($school) {
            $school->streams->each(function ($stream) use ($exam) {
                $stream->students_count = $stream->students()->where('form_id', $exam->form_id)->count();
                $stream->submitted_count = Mark::where('exam_id', $exam->id)
                    ->whereIn('student_id', $stream->students()->pluck('id'))
                    ->whereNotNull('score')
                    ->count();
            });

            $totalStudents = $school->streams->sum('students_count');
            $totalSubmitted = $school->streams->sum('submitted_count');
        }

        return view('dashboard.exams.teacher-show', compact('exam', 'school', 'totalStudents', 'totalSubmitted'));
    }

    private function getFormData()
    {
        return [
            'subjects' => Subject::all(),
            'forms' => Form::all(),
            'gradingSystems' => GradingSystem::all(),
        ];
    }

    private function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'form_id' => 'required|exists:forms,id',
            'grading_system_id' => 'required|exists:grading_systems,id',
            'mark_submission_mode' => 'required|in:teachers,admins',
        ];
    }

    public function changeExamStatus(Exam $exam, $slug)
    {
        if ($exam->slug !== $slug) return redirect()->route('exams.show', ['exam' => $exam->id, 'slug' => $exam->slug]);

        $exam->status = ($exam->status === 'finalized') ? 'processing' : 'finalized';
        $message = ($exam->status === 'finalized') ? 'Exam published and finalized.' : 'Exam unpublished.';
        
        $exam->save();

        ProcessExamChangesJob::dispatch($exam);

        return redirect()->route('exams.show', ['exam' => $exam->id, 'slug' => $exam->slug])->with('success', $message);
    }

    public function changeVisibility(Exam $exam, $slug)
    {
        if ($exam->slug !== $slug) return redirect()->route('exams.show', ['exam' => $exam->id, 'slug' => $exam->slug]);

        if ($exam->status !== 'finalized') {
            return redirect()->route('exams.show', ['exam' => $exam->id, 'slug' => $exam->slug])->with('error', 'Only finalized exams can be toggled.');
        }

        $exam->visibility = ($exam->visibility === 'private') ? 'public' : 'private';
        $message = 'Visibility updated to ' . $exam->visibility;
        
        $exam->save();

        ProcessExamChangesJob::dispatch($exam);

        return redirect()->route('exams.show', ['exam' => $exam->id, 'slug' => $exam->slug])->with('success', $message);
    }
}