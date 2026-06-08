@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-4 p-2 md:p-4">
    {{-- Header Section --}}
    <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-slate-200">
        <h1 class="text-xl md:text-2xl font-bold text-slate-800">{{ $exam->name }}</h1>
        <p class="text-sm text-slate-500 mt-1">
            {{ $exam->subject->name ?? 'N/A' }} | Form: {{ $exam->form->name ?? 'N/A' }}
        </p>

        <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t text-xs md:text-sm text-slate-600">
            <div><span class="text-slate-400">Year:</span> {{ $exam->academicYear->year ?? 'N/A' }}</div>
            <div><span class="text-slate-400">Term:</span> {{ $exam->term->name ?? 'N/A' }}</div>
            <div class="col-span-2"><span class="text-slate-400">School:</span> {{ $school->name }}</div>
        </div>
    </div>

    {{-- Submission Mode Alert --}}
    @if($exam->mark_submission_mode === 'admins')
        <div class="bg-indigo-50 border border-indigo-200 text-indigo-800 p-6 rounded-lg shadow-sm text-center">
            <i class='bx bx-loader-circle bx-spin text-4xl mb-3 text-indigo-600'></i>
            <h3 class="font-bold text-lg mb-2">Exam Processing</h3>
            <p class="text-sm">
                This exam is currently being processed. 
                @if($exam->status === 'finalized')
                    The exam is finalized. Results will be available shortly.
                @else
                    Please be patient; you will receive a notification once the analysis is complete and results are ready to view.
                @endif
            </p>
        </div>
    @else
        {{-- Progress Card --}}
        <div class="bg-indigo-600 text-white p-4 rounded-lg shadow-md flex justify-between items-center">
            <span class="text-sm font-medium uppercase tracking-wider">Overall Progress</span>
            <span class="text-xl font-bold">{{ $totalSubmitted }} / {{ $totalStudents }} <span class="text-indigo-200 text-sm font-normal">Submitted</span></span>
        </div>

        {{-- Streams Table --}}
        <div class="bg-white border rounded shadow-sm overflow-hidden">
            <div class="p-4 border-b">
                <h2 class="font-bold text-slate-700">Stream Status</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[300px]">
                    <thead class="bg-slate-50 text-[10px] md:text-xs uppercase text-slate-500">
                        <tr>
                            <th class="p-3 md:p-4 whitespace-nowrap">Stream</th>
                            <th class="p-3 md:p-4 text-center whitespace-nowrap">Status</th>
                            <th class="p-3 md:p-4 text-right whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-xs md:text-sm">
                        @foreach($school->streams as $stream)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 md:p-4 font-medium text-slate-700 whitespace-nowrap">{{ $stream->name }}</td>
                            <td class="p-3 md:p-4 text-center whitespace-nowrap">
                                <div class="font-bold {{ $stream->submitted_count >= $stream->students_count ? 'text-green-600' : 'text-indigo-600' }}">
                                    {{ $stream->submitted_count }}<span class="text-slate-400 font-normal">/{{ $stream->students_count }}</span>
                                </div>
                            </td>
                            <td class="p-3 md:p-4 text-right whitespace-nowrap">
                                <a href="{{ route('marks.select-paper', [
                                    'exam' => $exam->id,
                                    'examSlug' => $exam->slug,
                                    'school' => $school->id,
                                    'schoolSlug' => $school->slug,
                                    'stream' => $stream->id 
                                ]) }}" class="inline-block bg-indigo-600 text-white px-3 py-1.5 md:px-4 md:py-2 rounded font-bold text-xs md:text-sm transition hover:bg-indigo-700">
                                    Submit Marks
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection