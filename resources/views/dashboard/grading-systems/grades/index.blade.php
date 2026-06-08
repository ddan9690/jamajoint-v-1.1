@extends('layouts.app')

@section('content')
<div class="space-y-4 md:space-y-6" x-data="gradeManager()">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row items-center justify-between bg-white p-4 md:p-6 rounded-lg shadow-sm border border-slate-200 gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 tracking-tight">{{ $gradingSystem->name }}</h1>
            <a href="{{ route('grading-systems.index') }}" class="text-blue-600 text-sm hover:underline">← Back to Index</a>
        </div>
        <button @click="isLocked = !isLocked" 
                :class="isLocked ? 'bg-slate-600' : 'bg-amber-500'"
                class="text-white px-4 py-2 rounded-lg font-bold text-sm transition shadow-sm w-full sm:w-auto">
            <span x-text="isLocked ? 'Unlock to Edit' : 'Lock Editing'"></span>
        </button>
    </div>

    {{-- WARNING MESSAGE --}}
    <div x-show="hasOverlap()" x-cloak
         class="flex items-center p-3 bg-red-50 text-red-700 text-xs font-bold border border-red-200 rounded-lg">
        <i class='bx bx-error-circle mr-2 text-lg'></i>
        ⚠️ Overlap detected! Please check your score ranges.
    </div>

    {{-- TABLE --}}
    <form id="gradingForm" class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        <table class="w-full text-xs md:text-sm text-left table-fixed">
            <thead class="bg-slate-50 border-b border-slate-200 uppercase text-[9px] md:text-[10px] text-slate-500 tracking-wider">
                <tr>
                    <th class="w-1/6 px-2 py-3">Grade</th>
                    <th class="w-1/6 px-2 py-3">Pts</th>
                    <th class="w-1/3 px-2 py-3">Min</th>
                    <th class="w-1/3 px-2 py-3">Max</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($gradeMap as $grade => $points)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-2 py-3 font-bold text-slate-900 truncate">{{ $grade }}</td>
                    <td class="px-2 py-3 text-slate-500 truncate">{{ $points }}</td>
                    <td class="px-1 py-2">
                        <input type="number" name="grades[{{ $grade }}][min]" 
                               :disabled="isLocked"
                               value="{{ $existingGrades[$grade]->min_score ?? '' }}" 
                               class="w-full p-1.5 border border-slate-300 rounded disabled:bg-slate-50 disabled:border-slate-100 focus:ring-2 focus:ring-blue-500 text-xs md:text-sm" 
                               placeholder="Min">
                    </td>
                    <td class="px-1 py-2">
                        <input type="number" name="grades[{{ $grade }}][max]" 
                               :disabled="isLocked"
                               value="{{ $existingGrades[$grade]->max_score ?? '' }}" 
                               class="w-full p-1.5 border border-slate-300 rounded disabled:bg-slate-50 disabled:border-slate-100 focus:ring-2 focus:ring-blue-500 text-xs md:text-sm" 
                               placeholder="Max">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-4 md:p-6 bg-slate-50 border-t flex justify-end">
            <button type="button" @click="saveGrading()" 
                    :disabled="isLocked || hasOverlap()"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold text-sm hover:bg-blue-700 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function gradeManager() {
    return {
        isLocked: true,
        hasOverlap() {
            let inputs = document.querySelectorAll('#gradingForm input[type="number"]');
            let ranges = [];
            for (let i = 0; i < inputs.length; i += 2) {
                let minVal = inputs[i].value;
                let maxVal = inputs[i+1].value;
                if (minVal !== '' && maxVal !== '') {
                    let min = parseInt(minVal);
                    let max = parseInt(maxVal);
                    if (min >= max) return true;
                    ranges.push({ min, max });
                }
            }
            for (let i = 0; i < ranges.length; i++) {
                for (let j = i + 1; j < ranges.length; j++) {
                    if (ranges[i].min <= ranges[j].max && ranges[i].max >= ranges[j].min) return true;
                }
            }
            return false;
        },
        saveGrading() {
            let formData = $('#gradingForm').serializeArray();
            let data = { grades: {}, _token: $('meta[name="csrf-token"]').attr('content') };
            formData.forEach(field => {
                let match = field.name.match(/grades\[(.*)\]\[(.*)\]/);
                if(match) {
                    if(!data.grades[match[1]]) data.grades[match[1]] = {};
                    data.grades[match[1]][match[2]] = field.value;
                }
            });

            $.ajax({
                url: "{{ route('grading-systems.grades.store', $gradingSystem->id) }}",
                type: 'POST',
                data: data,
                success: (res) => { 
                    toastr.success(res.message); 
                    setTimeout(() => location.reload(), 1000); 
                },
                error: (xhr) => toastr.error(xhr.responseJSON?.message || 'Error occurred.')
            });
        }
    }
}
</script>
@endpush