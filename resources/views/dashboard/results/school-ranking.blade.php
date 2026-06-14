@extends('layouts.app')

@section('title', 'School Ranking - ' . $exam->name)

@section('content')
<div class="px-2 md:px-4 space-y-6">
    {{-- Header --}}
    <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-slate-200">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-800">{{ $exam->name }}</h1>
            <p class="text-xs md:text-sm text-slate-500 mt-1">
                {{ $exam->subject->name ?? 'N/A' }} | 
                Form: {{ $exam->form->name ?? 'N/A' }} | 
                Term: {{ $exam->term->name ?? 'N/A' }} |
                Year: {{ $exam->academicYear->year ?? 'N/A' }}
            </p>
            <p class="text-sm font-bold text-slate-700 mt-2">School Ranking</p>
        </div>
    </div>

    {{-- School Ranking Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                    <tr>
                        <th class="p-3 text-center w-12">#</th>
                        <th class="p-3 text-left">School</th>
                        <th class="p-3 text-center">A</th>
                        <th class="p-3 text-center">A-</th>
                        <th class="p-3 text-center">B+</th>
                        <th class="p-3 text-center">B</th>
                        <th class="p-3 text-center">B-</th>
                        <th class="p-3 text-center">C+</th>
                        <th class="p-3 text-center">C</th>
                        <th class="p-3 text-center">C-</th>
                        <th class="p-3 text-center">D+</th>
                        <th class="p-3 text-center">D</th>
                        <th class="p-3 text-center">D-</th>
                        <th class="p-3 text-center">E</th>
                        <th class="p-3 text-center">Entry</th>
                        <th class="p-3 text-center">Mean</th>
                        <th class="p-3 text-center">M.Grade</th>
                        <th class="p-3 text-center">% Pass</th>
                        <th class="p-3 text-center">Rank</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($schoolRankings as $school)
                    <tr class="hover:bg-slate-50">
                        <td class="p-3 text-center font-bold text-slate-600">{{ $school->rank }}</td>
                        <td class="p-3 font-medium text-slate-800 whitespace-nowrap">{{ $school->school_name }}</td>
                        <td class="p-3 text-center">{{ $school->count_A }}</td>
                        <td class="p-3 text-center">{{ $school->{'count_A-'} }}</td>
                        <td class="p-3 text-center">{{ $school->{'count_B+'} }}</td>
                        <td class="p-3 text-center">{{ $school->count_B }}</td>
                        <td class="p-3 text-center">{{ $school->{'count_B-'} }}</td>
                        <td class="p-3 text-center">{{ $school->{'count_C+'} }}</td>
                        <td class="p-3 text-center">{{ $school->count_C }}</td>
                        <td class="p-3 text-center">{{ $school->{'count_C-'} }}</td>
                        <td class="p-3 text-center">{{ $school->{'count_D+'} }}</td>
                        <td class="p-3 text-center">{{ $school->count_D }}</td>
                        <td class="p-3 text-center">{{ $school->{'count_D-'} }}</td>
                        <td class="p-3 text-center">{{ $school->count_E }}</td>
                        <td class="p-3 text-center">{{ $school->total_students }}</td>
                        <td class="p-3 text-center font-bold">{{ number_format($school->mean_score, 4) }}</td>
                        <td class="p-3 text-center">
                            @php
                                $gradeClass = 'bg-slate-100 text-slate-700';
                                if ($school->mean_grade === 'A') $gradeClass = 'bg-green-100 text-green-700';
                                elseif ($school->mean_grade === 'A-') $gradeClass = 'bg-green-50 text-green-600';
                                elseif ($school->mean_grade === 'B+') $gradeClass = 'bg-blue-100 text-blue-700';
                                elseif ($school->mean_grade === 'B') $gradeClass = 'bg-blue-50 text-blue-600';
                                elseif ($school->mean_grade === 'B-') $gradeClass = 'bg-blue-50 text-blue-500';
                                elseif ($school->mean_grade === 'C+') $gradeClass = 'bg-yellow-100 text-yellow-700';
                                elseif ($school->mean_grade === 'C') $gradeClass = 'bg-yellow-50 text-yellow-600';
                                elseif ($school->mean_grade === 'C-') $gradeClass = 'bg-yellow-50 text-yellow-500';
                                elseif ($school->mean_grade === 'D+') $gradeClass = 'bg-orange-100 text-orange-700';
                                elseif ($school->mean_grade === 'D') $gradeClass = 'bg-orange-50 text-orange-600';
                                elseif ($school->mean_grade === 'D-') $gradeClass = 'bg-orange-50 text-orange-500';
                                elseif ($school->mean_grade === 'E') $gradeClass = 'bg-red-100 text-red-700';
                            @endphp
                            <span class="px-2 py-1 rounded text-xs font-bold {{ $gradeClass }}">
                                {{ $school->mean_grade }}
                            </span>
                        </td>
                        <td class="p-3 text-center">{{ number_format($school->pass_rate, 1) }}%</td>
                        <td class="p-3 text-center font-bold">{{ $school->rank }}</td>
                    </tr>
                    @endforeach
                </tbody>
                @if($overall)
                <tfoot class="bg-slate-100 border-t border-slate-200">
                    <tr class="font-bold">
                        <td class="p-3 text-center">-</td>
                        <td class="p-3 font-bold text-slate-800">OVERALL</td>
                        <td class="p-3 text-center">{{ $overall['grade_counts']['A'] ?? 0 }}</td>
                        <td class="p-3 text-center">{{ $overall['grade_counts']['A-'] ?? 0 }}</td>
                        <td class="p-3 text-center">{{ $overall['grade_counts']['B+'] ?? 0 }}</td>
                        <td class="p-3 text-center">{{ $overall['grade_counts']['B'] ?? 0 }}</td>
                        <td class="p-3 text-center">{{ $overall['grade_counts']['B-'] ?? 0 }}</td>
                        <td class="p-3 text-center">{{ $overall['grade_counts']['C+'] ?? 0 }}</td>
                        <td class="p-3 text-center">{{ $overall['grade_counts']['C'] ?? 0 }}</td>
                        <td class="p-3 text-center">{{ $overall['grade_counts']['C-'] ?? 0 }}</td>
                        <td class="p-3 text-center">{{ $overall['grade_counts']['D+'] ?? 0 }}</td>
                        <td class="p-3 text-center">{{ $overall['grade_counts']['D'] ?? 0 }}</td>
                        <td class="p-3 text-center">{{ $overall['grade_counts']['D-'] ?? 0 }}</td>
                        <td class="p-3 text-center">{{ $overall['grade_counts']['E'] ?? 0 }}</td>
                        <td class="p-3 text-center">{{ $overall['total_students'] }}</td>
                        <td class="p-3 text-center font-bold">{{ number_format($overall['overall_mean'], 4) }}</td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-1 rounded text-xs font-bold bg-slate-200 text-slate-700">
                                {{ $overall['overall_grade'] }}
                            </span>
                        </td>
                        <td class="p-3 text-center">{{ number_format($overall['pass_rate'], 1) }}%</td>
                        <td class="p-3 text-center">-</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Back Link --}}
    <div class="mt-4">
        <a href="{{ route('results.index', [$exam->id, $exam->slug]) }}" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
            <i class='bx bx-arrow-back'></i> Back to Results Dashboard
        </a>
    </div>
</div>
@endsection