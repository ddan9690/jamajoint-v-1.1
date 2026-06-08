@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Detailed Heading Section --}}
    <div class="bg-indigo-50 border border-indigo-100 p-6 rounded-lg">
        <h1 class="text-2xl font-bold text-indigo-900 mb-4">Mark Submission</h1>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="block text-indigo-400 font-semibold uppercase text-[10px]">Exam</span>
                <span class="font-medium text-slate-700">{{ $exam->name }}</span>
            </div>
            <div>
                <span class="block text-indigo-400 font-semibold uppercase text-[10px]">School</span>
                <span class="font-medium text-slate-700">{{ auth()->user()->school->name ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="block text-indigo-400 font-semibold uppercase text-[10px]">Subject</span>
                <span class="font-medium text-slate-700">{{ $exam->subject->name ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="block text-indigo-400 font-semibold uppercase text-[10px]">Form / Paper</span>
                <span class="font-medium text-slate-700">Form {{ $exam->form->name ?? 'N/A' }} / {{ $paper->name }}</span>
            </div>
        </div>
    </div>

    {{-- Marks Form --}}
    <div class="bg-white p-8 rounded-lg shadow-sm border border-slate-200">
        <form action="{{ route('marks.papers.store', ['exam' => $exam->id, 'paper' => $paper->id]) }}" method="POST">
            @csrf
            
            <input type="hidden" name="subject_id" value="{{ $exam->subject_id }}">
            <input type="hidden" name="school_id" value="{{ auth()->user()->school_id }}">
            <input type="hidden" name="form_id" value="{{ $exam->form_id }}">
            <input type="hidden" name="stream_id" value="{{ request('stream_id') ?? $students->first()->stream_id }}">

            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b">
                        <th class="p-3">Student Name</th>
                        <th class="p-3 text-right">Score (0-100)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr class="border-b hover:bg-slate-50 transition">
                        <td class="p-3 font-medium text-slate-700">{{ $student->name }}</td>
                        <td class="p-3 text-right">
                            <input type="number" name="marks[{{ $student->id }}]" 
                                   value="{{ $student->marks->where('paper_id', $paper->id)->first()->score ?? '' }}"
                                   class="w-20 border border-slate-300 rounded p-1 text-center focus:ring-2 focus:ring-indigo-500 outline-none" 
                                   required min="0" max="100">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-indigo-700 shadow-md transition">Save Marks</button>
            </div>
        </form>
    </div>
</div>
@endsection