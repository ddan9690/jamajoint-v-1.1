@extends('layouts.app')

@section('content')
<div class="space-y-8">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($stats as $stat)
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <div class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">{{ $stat['label'] }}</div>
                <div class="text-2xl md:text-4xl font-black mt-2 text-slate-800">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Main Activity Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800">{{ $tableTitle }}</h2>
            @if(in_array(auth()->user()->role, ['super_admin', 'exam_admin']))
                <a href="{{ route('exams.create') }}" class="text-sm bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">+ New Exam</a>
            @endif
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px]">
                    <tr>
                        <th class="px-6 py-4 text-left">Exam Name</th>
                        <th class="px-6 py-4 text-left">Subject</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-left">Submission Mode</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($exams as $exam)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-700">{{ $exam->name }}</td>
                            <td class="px-6 py-4">{{ $exam->subject->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase 
                                    {{ $exam->status === 'finalized' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $exam->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 capitalize">{{ $exam->mark_submission_mode }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('exams.show', [$exam->id, $exam->slug]) }}" class="text-indigo-600 hover:text-indigo-900 font-bold">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 italic">No exams available for your account.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection