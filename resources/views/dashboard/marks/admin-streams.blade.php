@extends('layouts.app')

@section('content')
<div class="px-2 md:px-4 space-y-6">
    {{-- Header --}}
    <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-slate-200">
        <h1 class="text-xl md:text-2xl font-bold text-slate-800">{{ $exam->name }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ $school->name }} | Form: {{ $exam->form->name }}</p>
    </div>

    {{-- Streams Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                    <tr>
                        <th class="p-3 whitespace-nowrap">Stream</th>
                        @foreach($exam->papers as $paper)
                            <th class="p-3 text-center whitespace-nowrap">{{ $paper->name }}</th>
                        @endforeach
                        <th class="p-3 text-right whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($streams as $stream)
                        <tr>
                            <td class="p-3 font-bold text-slate-800 whitespace-nowrap">{{ $stream->name }}</td>
                            
                            @foreach($exam->papers as $paper)
                                @php 
                                    $submitted = $stream->submission_map->get($paper->id, 0);
                                @endphp
                                <td class="p-3 text-center text-xs whitespace-nowrap">
                                    <span class="{{ $submitted > 0 ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                                        {{ $submitted }}/{{ $stream->total_students }}
                                    </span>
                                </td>
                            @endforeach

                            <td class="p-3 text-right whitespace-nowrap">
                                @php $hasAny = $stream->submission_map->sum() > 0; @endphp
                                
                                {{-- 
                                   Logic: 
                                   If marks exist ($hasAny), we jump straight to View (Paper 1).
                                   If no marks exist, we go to Select Paper.
                                --}}
                                <a href="{{ $hasAny 
                                    ? route('marks.admin.show-students', [
                                        'school' => $school->id, 'schoolSlug' => $school->slug,
                                        'exam' => $exam->id, 'examSlug' => $exam->slug,
                                        'subject' => $exam->subject_id, 'subjectSlug' => $exam->subject->slug ?? 'n-a',
                                        'form' => $exam->form_id, 'stream' => $stream->id, 
                                        'paper' => $exam->papers->first()->id ?? 0
                                      ]) 
                                    : route('marks.admin.select-paper', [
                                        'school' => $school->id, 'schoolSlug' => $school->slug,
                                        'exam' => $exam->id, 'examSlug' => $exam->slug,
                                        'stream' => $stream->id
                                      ]) 
                                }}" class="px-3 py-1 rounded text-xs font-bold text-white {{ $hasAny ? 'bg-green-600' : 'bg-indigo-600' }}">
                                    {{ $hasAny ? 'View' : 'Submit' }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection