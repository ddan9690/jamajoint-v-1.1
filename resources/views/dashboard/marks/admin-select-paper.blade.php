@extends('layouts.app')

@section('content')
<div class="px-2 md:px-4 space-y-6">
    {{-- Header --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
        <h1 class="text-xl md:text-2xl font-bold text-slate-800">{{ $exam->name }}</h1>
        <p class="text-slate-500 text-sm mt-1">
            Stream: <span class="font-bold text-slate-700">{{ $stream->name }}</span> | 
            School: <span class="font-semibold text-slate-800">{{ $school->name }}</span>
        </p>
    </div>

    {{-- Paper Selection Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($exam->subject->papers as $paper)
            <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">{{ $paper->name }}</h3>
                    <p class="text-sm text-slate-500 mt-2">
                        Click below to enter scores for this paper.
                    </p>
                </div>
                
                <div class="mt-6">
                    {{-- Updated route to marks.submit-entry --}}
                    <a href="{{ route('marks.submit-entry', [$exam->id, $exam->slug, $school->id, $school->slug, $stream->id, $paper->id]) }}" 
                       class="block w-full text-center bg-indigo-600 text-white px-4 py-2 rounded font-bold text-sm hover:bg-indigo-700 transition-colors">
                       Submit Marks
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Back Button --}}
    <div class="pt-4">
        <a href="{{ route('marks.submit-streams', [$exam->id, $exam->slug, $school->id, $school->slug]) }}" 
           class="text-indigo-600 font-bold hover:underline">
           &larr; Back to Streams
        </a>
    </div>
</div>
@endsection