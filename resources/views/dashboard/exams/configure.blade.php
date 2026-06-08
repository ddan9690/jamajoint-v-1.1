@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ isEditing: false }">
    <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 flex justify-between items-center">
        <div>
            <a href="{{ route('exams.show', [$exam->id, $exam->slug]) }}" class="text-sm text-blue-600 hover:underline mb-2 block">&larr; Back to Exam</a>
            <h1 class="text-2xl font-bold text-slate-900">Configure: {{ $exam->name }}</h1>
            <p class="text-slate-500 text-sm">Define max scores and weighting for all subject papers.</p>
        </div>
        
        {{-- Edit Toggle Button --}}
        <button type="button" 
                @click="isEditing = !isEditing"
                :class="isEditing ? 'bg-amber-500 hover:bg-amber-600' : 'bg-slate-600 hover:bg-slate-700'"
                class="text-white px-4 py-2 rounded-lg transition font-semibold">
            <span x-text="isEditing ? 'Cancel Edit' : 'Edit'"></span>
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-lg border border-green-200 font-semibold text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <form action="{{ route('exams.configurations.store', [$exam->id, $exam->slug]) }}" method="POST">
            @csrf
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Paper</th>
                        <th class="px-6 py-4">Max Score</th>
                        <th class="px-6 py-4">Weight (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($exam->subject->papers as $paper)
                        <tr>
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $paper->name }}</td>
                            <td class="px-6 py-4">
                                <input type="number" name="configurations[{{ $paper->id }}][max_score]" 
                                       value="{{ isset($configs[$paper->id]) ? (int)$configs[$paper->id]->max_score : 100 }}" 
                                       class="w-24 p-2 border rounded-lg disabled:bg-slate-100 disabled:text-slate-500" 
                                       :disabled="!isEditing" required>
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" name="configurations[{{ $paper->id }}][weight]" 
                                       value="{{ isset($configs[$paper->id]) ? (int)$configs[$paper->id]->weight : 100 }}" 
                                       class="w-24 p-2 border rounded-lg disabled:bg-slate-100 disabled:text-slate-500" 
                                       :disabled="!isEditing" required>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{-- Save button only shows when in edit mode --}}
            <div class="p-6 bg-slate-50 border-t flex justify-end" x-show="isEditing" x-cloak>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection