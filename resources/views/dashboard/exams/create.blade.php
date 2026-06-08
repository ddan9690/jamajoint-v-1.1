@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Create New Exam</h1>
    </div>

    <form action="{{ route('exams.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Exam Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Spark English Joint Exam" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Subject</label>
                <select name="subject_id" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none" required>
                    @foreach($subjects as $s) <option value="{{$s->id}}">{{$s->name}}</option> @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Form</label>
                <select name="form_id" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none" required>
                    @foreach($forms as $f) <option value="{{$f->id}}">{{$f->name}}</option> @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Grading System</label>
                <select name="grading_system_id" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none" required>
                    @foreach($gradingSystems as $g) <option value="{{$g->id}}">{{$g->name}}</option> @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Mark Submission Mode</label>
                <select name="mark_submission_mode" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none" required>
                    <option value="admins" selected>Exam Admins</option>
                    <option value="teachers">Participating School Teachers</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('exams.index') }}" class="px-4 py-2 text-slate-600 hover:text-slate-800">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Save Exam</button>
        </div>
    </form>
</div>
@endsection