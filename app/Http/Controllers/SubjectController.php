<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubjectController extends Controller
{
    /**
     * Display a listing of the subjects.
     */
    public function index()
    {
        $subjects = Subject::latest()->get();
        return view('dashboard.subjects.index', compact('subjects'));
    }

    /**
     * Show the details of a subject and manage its papers.
     */
    public function show($id, $slug)
    {
        // Eager load papers to prevent N+1 queries
        $subject = Subject::with('papers')->where('id', $id)->where('slug', $slug)->firstOrFail();
        return view('dashboard.subjects.show', compact('subject'));
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create()
    {
        return view('dashboard.subjects.create');
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|unique:subjects,code|max:50',
            'short'         => 'nullable|string|max:10',
            'is_compulsory' => 'nullable|boolean',
        ]);

        Subject::create([
            'name'          => $request->name,
            'slug'          => Str::slug($request->name),
            'code'          => $request->code,
            'short'         => $request->short ?? substr($request->name, 0, 3),
            'is_compulsory' => $request->has('is_compulsory'),
            'is_active'     => true,
        ]);

        return response()->json(['message' => 'Subject created successfully.']);
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit($id, $slug)
    {
        $subject = Subject::where('id', $id)->where('slug', $slug)->firstOrFail();
        return view('dashboard.subjects.edit', compact('subject'));
    }

    /**
     * Update the specified subject in storage.
     */
    public function update(Request $request, $id, $slug)
    {
        $subject = Subject::where('id', $id)->where('slug', $slug)->firstOrFail();

        $request->validate([
            'name'  => 'required|string|max:255',
            'code'  => 'required|string|unique:subjects,code,' . $subject->id,
            'short' => 'nullable|string|max:10',
        ]);

        $subject->update([
            'name'          => $request->name,
            'slug'          => Str::slug($request->name),
            'code'          => $request->code,
            'short'         => $request->short,
            'is_compulsory' => $request->has('is_compulsory'),
        ]);

        return response()->json(['message' => 'Subject updated successfully.']);
    }

    /**
     * Remove the specified subject from storage.
     */
    public function destroy($id, $slug)
    {
        $subject = Subject::where('id', $id)->where('slug', $slug)->firstOrFail();
        $subject->delete();

        return response()->json(['message' => 'Subject deleted successfully.']);
    }
}