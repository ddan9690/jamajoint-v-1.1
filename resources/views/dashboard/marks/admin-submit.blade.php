@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="markEntry({{ count($students) }})">

    {{-- Header Details --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
        <h1 class="text-2xl font-bold text-slate-800">{{ $exam->name }}</h1>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 text-sm text-slate-600">
            <div><span class="font-semibold text-slate-900">School:</span> {{ $school->name }}</div>
            <div><span class="font-semibold text-slate-900">Paper:</span> {{ $exam->papers->firstWhere('id', request()->route('paper'))->name ?? 'N/A' }}</div>
            <div><span class="font-semibold text-slate-900">Form:</span> {{ $exam->form->name }}</div>
            <div><span class="font-semibold text-slate-900">Stream:</span> {{ $stream->name }}</div>
        </div>
    </div>

    {{-- Form --}}
    <form id="marksForm" 
          action="{{ route('marks.admin.store', [
              'school' => $school->id, 
              'schoolSlug' => $school->slug, 
              'exam' => $exam->id, 
              'stream' => $stream->id,
              'paper' => request()->route('paper') 
          ]) }}" 
          method="POST">
        @csrf
        
        {{-- HIDDEN PAPER ID FIELD --}}
        <input type="hidden" name="paper_id" value="{{ request()->route('paper') }}">

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-100 text-xs uppercase text-slate-600 font-bold">
                        <tr>
                            <th class="p-4 border-b">#</th>
                            <th class="p-4 border-b">Adm No</th>
                            <th class="p-4 border-b">Student</th>
                            <th class="p-4 border-b w-40">Score</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($students as $index => $student)
                        <tr class="hover:bg-slate-50">
                            <td class="p-4 text-slate-500">{{ $index + 1 }}</td>
                            <td class="p-4 font-mono font-medium text-slate-800">{{ $student->admission_number }}</td>
                            <td class="p-4 text-slate-800">{{ $student->name }}</td>
                            <td class="p-4 relative">
                                <input 
                                    type="number" 
                                    name="marks[{{ $student->id }}]" 
                                    value="{{ old('marks.' . $student->id, $student->mark->score ?? '') }}" 
                                    x-init="checkInitial($el, {{ $index }})"
                                    @input="validate({{ $index }}, $el.value)"
                                    @keydown="restrictChars($event); handleKeydown($event, {{ $index }})"
                                    :class="errors[{{ $index }}] ? 'border-red-500 animate-shake' : 'border-slate-300'"
                                    class="w-full border rounded-md p-2 text-center focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                                    min="0" max="100"
                                >
                                <div x-show="errors[{{ $index }}]" x-cloak class="absolute z-10 w-full text-red-600 text-[10px] font-bold mt-1 text-center">
                                    Invalid (0-100)
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-6 bg-slate-50 border-t flex justify-end">
                <button type="button" 
                        @click="confirmSubmit()" 
                        :disabled="hasErrors || !hasData"
                        :class="(hasErrors || !hasData) ? 'bg-slate-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'"
                        class="text-white font-bold py-3 px-10 rounded-lg shadow-md transition">
                    Submit Marks
                </button>
            </div>
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
            values: Array(count).fill(''),
            
            get hasData() { return this.values.some(v => v !== ''); },
            get hasErrors() { return this.errors.includes(true); },

            checkInitial(el, index) {
                if (el.value !== '') this.values[index] = el.value;
            },
            restrictChars(e) {
                if (['e', 'E', '.', '-'].includes(e.key)) e.preventDefault();
            },
            validate(index, val) {
                this.values[index] = val;
                const num = parseInt(val);
                if (val !== '' && (isNaN(num) || num < 0 || num > 100)) {
                    this.errors[index] = true;
                } else {
                    this.errors[index] = false;
                }
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
            confirmSubmit() {
                Swal.fire({
                    title: 'Submit Marks?',
                    text: 'Confirm to save these entries.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    confirmButtonText: 'Yes, Submit'
                }).then((result) => {
                    if (result.isConfirmed) document.getElementById('marksForm').submit();
                });
            }
        }
    }
</script>
@endsection