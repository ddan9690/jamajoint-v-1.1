<?php

namespace App\Http\Controllers;

use App\Models\County;
use App\Models\Form;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SchoolController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        $query = School::with('county');

        if (Auth::user()->role !== 'super_admin') {
            $query->where('id', Auth::user()->school_id);
        }

        $schools = $query->orderBy('name', 'asc')->get();

        return view('dashboard.schools.index', compact('schools'));
    }

    public function create()
    {
        $this->authorize('manage-system');

        $counties = County::all();

        return view('dashboard.schools.create', compact('counties'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-system');

        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:schools,name',
            'county_id' => 'required|exists:counties,id',
            'type'      => 'required|in:mixed,girls,boys',
        ]);

        // 🔥 SLUG LOGIC MOVED HERE
        $validated['slug'] = Str::slug($validated['name']);

        School::create($validated);

        return redirect()->route('schools.index')
            ->with('success', 'School created successfully!');
    }

    public function show(School $school, $slug)
{
    if ($school->slug !== $slug) {
        return redirect()->route('schools.show', [
            'school' => $school->id,
            'slug' => $school->slug
        ]);
    }

    $forms = Form::all();

    $forms->each(function ($form) use ($school) {
        $form->total_students = Student::whereHas('stream', function ($query) use ($school, $form) {
            $query->where('school_id', $school->id)
                  ->where('form_id', $form->id);
        })->count();
    });

   
    $teachers = User::where('school_id', $school->id)
                                ->where('role', 'teacher')
                                ->orderBy('name', 'asc')
                                ->get();

    return view('dashboard.schools.show', compact('school', 'forms', 'teachers'));
}

    public function edit(School $school, $slug)
    {
        if ($school->slug !== $slug) {
            return redirect()->route('schools.edit', [
                'school' => $school->id,
                'slug' => $school->slug
            ]);
        }

        $this->authorize('update', $school);

        $counties = County::all();

        return view('dashboard.schools.edit', compact('school', 'counties'));
    }

    public function update(Request $request, School $school, $slug)
    {
        if ($school->slug !== $slug) {
            return redirect()->route('schools.edit', [
                'school' => $school->id,
                'slug' => $school->slug
            ]);
        }

        $this->authorize('update', $school);

        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:schools,name,' . $school->id,
            'county_id' => 'required|exists:counties,id',
            'type'      => 'required|in:mixed,girls,boys',
            'is_active' => 'nullable',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // 🔥 SLUG NOW HANDLED HERE
        $validated['slug'] = Str::slug($validated['name']);

        $school->update($validated);

        return redirect()->route('schools.show', [
            'school' => $school->id,
            'slug' => $school->slug
        ])->with('success', 'School updated successfully!');
    }

    public function destroy(School $school, $slug)
    {
        if ($school->slug !== $slug) {
            return redirect()->route('schools.show', [
                'school' => $school->id,
                'slug' => $school->slug
            ]);
        }

        $this->authorize('manage-system');

        $school->delete();

        return redirect()->route('schools.index')
            ->with('success', 'School removed from system.');
    }
}