@extends('layouts.app')

@section('content')
<div class="px-2 md:px-4 space-y-6">
    {{-- Header --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
        <h1 class="text-xl md:text-2xl font-bold text-slate-800">{{ $exam->name }}</h1>
        <p class="text-sm text-slate-500 mt-1">
            {{ $school->name }} | Submission Statistics
        </p>
    </div>

    {{-- Submission Statistics Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b bg-slate-50">
            <h3 class="font-bold text-slate-700">Stream Submission Overview</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-slate-50 text-slate-400 uppercase text-xs">
                    <tr>
                        <th class="p-3">Stream</th>
                        @foreach($exam->subject->papers as $paper)
                            <th class="p-3 text-center">{{ $paper->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($streams as $stream)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 font-bold text-slate-800">{{ $stream->name }}</td>
                            @foreach($exam->subject->papers as $paper)
                                @php $submitted = $stream->submission_map->get($paper->id, 0); @endphp
                                <td class="p-3 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="{{ $submitted > 0 ? 'text-green-600 font-bold' : 'text-slate-300' }}">
                                            {{ $submitted }}/{{ $stream->total_students }}
                                        </span>
                                        
                                        @if($submitted > 0)
                                            {{-- View existing submissions --}}
                                            <a href="{{ route('marks.admin-show-students', [$exam->id, $exam->slug, $school->id, $school->slug, $stream->id, $paper->id]) }}" 
                                               class="text-[10px] text-indigo-600 hover:text-indigo-800 font-bold mt-1">
                                               View Marks
                                            </a>
                                        @else
                                            {{-- Trigger new submission entry --}}
                                            <a href="{{ route('marks.submit-entry', [$exam->id, $exam->slug, $school->id, $school->slug, $stream->id, $paper->id]) }}" 
                                               class="text-[10px] text-emerald-600 hover:text-emerald-800 font-bold mt-1">
                                               Submit Marks
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Navigation Footer --}}
    <div class="flex justify-start">
        <a href="{{ route('exams.show', [$exam->id, $exam->slug]) }}" 
           class="inline-flex items-center text-slate-500 hover:text-indigo-600 text-sm font-bold transition">
            &larr; Back to Participating Schools
        </a>
    </div>
</div>
@endsection