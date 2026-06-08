<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Form;
use App\Models\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StreamController extends Controller
{
    public function index(School $school, $slug, Form $form)
    {
        if ($school->slug !== $slug) {
            return redirect()->route('schools.forms.streams.index', [
                'school' => $school->id,
                'slug' => $school->slug,
                'form' => $form->id
            ]);
        }

        $streams = Stream::where('school_id', $school->id)
            ->where('form_id', $form->id)
            ->withCount('students')
            ->get();

        return view('dashboard.streams.index', compact('school', 'form', 'streams'));
    }

    public function store(Request $request, School $school, $slug, Form $form)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $exists = Stream::where('school_id', $school->id)
            ->where('form_id', $form->id)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'This stream already exists.'], 422);
        }

        Stream::create([
            'school_id' => $school->id,
            'form_id'   => $form->id,
            'name'      => $request->name,
            'slug'      => Str::slug($request->name),
        ]);

        return response()->json(['message' => 'Stream created successfully!']);
    }

    public function update(Request $request, School $school, $slug, Form $form, Stream $stream)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $stream->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json(['message' => 'Stream updated successfully!']);
    }

    public function destroy(School $school, $slug, Form $form, Stream $stream)
    {
        $stream->delete();
        
        // Using session flash for non-AJAX deletions
        return back()->with('success', 'Stream deleted successfully.');
    }
}