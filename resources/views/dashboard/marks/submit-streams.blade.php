@extends('layouts.app')

@section('content')
<div class="px-2 md:px-4 space-y-6">
    {{-- Header --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
        <h1 class="text-xl md:text-2xl font-bold text-slate-800">{{ $exam->name }}</h1>
        <p class="text-slate-500 text-sm mt-1">
            Submit marks for: <span class="font-semibold text-slate-800">{{ $school->name }}</span>
        </p>
    </div>

    {{-- Streams List Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center">
            <span class="font-bold text-slate-700 uppercase text-xs">Select a Stream to begin data entry</span>
            
            {{-- Status Indicator --}}
            @if($exam->mark_submission_mode === 'admins')
                <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase">
                    Read-Only Mode
                </span>
            @else
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase">
                    Submission Open
                </span>
            @endif
        </div>
        
        <table class="w-full text-left text-sm border-collapse">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                <tr>
                    <th class="p-4">#</th>
                    <th class="p-4">Stream Name</th>
                    <th class="p-4 text-center">Total Students</th>
                    <th class="p-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($streams as $index => $stream)
                <tr class="hover:bg-slate-50">
                    <td class="p-4">{{ $index + 1 }}</td>
                    <td class="p-4 font-medium text-slate-800">{{ $stream->name }}</td>
                    <td class="p-4 text-center">{{ $stream->students->count() }}</td>
                    <td class="p-4 text-right">
                        @if($exam->mark_submission_mode === 'teachers' || auth()->user()->can('manage-exams'))
                            <a href="{{ route('marks.select-paper', [$exam->id, $exam->slug, $school->id, $school->slug, $stream->id]) }}" 
                               class="bg-indigo-600 text-white px-4 py-2 rounded text-xs font-bold hover:bg-indigo-700 transition-colors">
                                Submit Marks
                            </a>
                        @else
                            <button disabled class="bg-slate-200 text-slate-500 px-4 py-2 rounded text-xs font-bold cursor-not-allowed">
                                Locked
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection