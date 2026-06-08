@extends('layouts.app')

@section('content')
<div class="px-2 md:px-4 space-y-6" x-data="markEntry({{ count($students) }})">

    {{-- Header --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
        <h1 class="text-xl md:text-2xl font-bold text-slate-800">{{ $exam->name }}</h1>
        <p class="text-slate-500 text-sm mt-1">
            Paper: <span class="font-bold text-slate-700">{{ $paper->name }}</span> | 
            Stream: <span class="font-bold text-slate-700">{{ $stream->name }}</span> | 
            School: <span class="font-semibold text-slate-800">{{ $school->name }}</span>
        </p>
    </div>

    {{-- Submission Form --}}
    <form id="marksForm" 
          action="{{ route('marks.admin-store', [$exam->id, $exam->slug, $school->id, $school->slug, $stream->id, $paper->id]) }}" 
          method="POST">
        @csrf
        
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                    <tr>
                        <th class="p-4">#</th>
                        <th class="p-4">Adm No</th>
                        <th class="p-4">Student</th>
                        <th class="p-4 w-40">Score</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($students as $index => $student)
                    <tr class="hover:bg-slate-50">
                        <td class="p-4 text-slate-500">{{ $index + 1 }}</td>
                        <td class="p-4 font-mono font-medium text-slate-800">{{ $student->admission_number }}</td>
                        <td class="p-4 font-medium text-slate-800">{{ $student->name }}</td>
                        <td class="p-4 relative">
                            <input type="number" 
                                   name="marks[{{ $student->id }}]" 
                                   value="{{ old('marks.' . $student->id, $student->mark->score ?? '') }}" 
                                   x-init="checkInitial($el, {{ $index }})"
                                   @input="validate({{ $index }}, $el.value)"
                                   @keydown="restrictChars($event); handleKeydown($event, {{ $index }})"
                                   :class="errors[{{ $index }}] ? 'border-red-500 animate-shake' : 'border-slate-300'"
                                   class="w-full border rounded-md p-2 text-center focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                                   min="0" max="100" step="1">
                            <div x-show="errors[{{ $index }}]" x-cloak class="absolute z-10 w-full text-red-600 text-[10px] font-bold mt-1 text-center">
                                Invalid (0-100)
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer Actions --}}
        <div class="flex justify-between items-center pt-4">
            <a href="{{ route('marks.select-paper', [$exam->id, $exam->slug, $school->id, $school->slug, $stream->id]) }}" 
               class="text-indigo-600 font-bold hover:underline">
               &larr; Back to Papers
            </a>
            <button type="button" 
                    @click="confirmSubmit()" 
                    :disabled="hasErrors"
                    :class="hasErrors ? 'bg-slate-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'"
                    class="text-white px-6 py-2 rounded font-bold transition-colors">
                Save Marks
            </button>
        </div>
    </form>
</div>

<style>
    @keyframes shake { 0%, 100% {transform: translateX(0);} 25% {transform: translateX(-5px);} 75% {transform: translateX(5px);} }
    .animate-shake { animation: shake 0.2s ease-in-out 0s 2; }
</style>

<script>
    function markEntry(count) {
        return {
            errors: Array(count).fill(false),
            checkInitial(el, index) {
                // Initialize if value already exists
            },
            restrictChars(e) {
                if (['e', 'E', '.', '-'].includes(e.key)) e.preventDefault();
            },
            validate(index, val) {
                const num = parseInt(val);
                this.errors[index] = (val !== '' && (isNaN(num) || num < 0 || num > 100));
            },
            handleKeydown(e, index) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (!this.errors[index]) {
                        const inputs = Array.from(document.querySelectorAll('input[type="number"]'));
                        if (inputs[index + 1]) {
                            inputs[index + 1].focus();
                            inputs[index + 1].select();
                        }
                    }
                }
            },
            get hasErrors() { return this.errors.includes(true); },
            confirmSubmit() {
                Swal.fire({
                    title: 'Submit Marks?',
                    text: 'Confirm to save these entries.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    confirmButtonText: 'Yes, Save'
                }).then((result) => {
                    if (result.isConfirmed) document.getElementById('marksForm').submit();
                });
            }
        }
    }
</script>
@endsection