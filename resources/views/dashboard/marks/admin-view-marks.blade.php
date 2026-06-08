@extends('layouts.app')

@section('content')
    <div class="px-2 md:px-4 space-y-6" x-data="marksTable()">

        {{-- Header --}}
        <div class="bg-white p-4 rounded-lg shadow-sm border border-slate-200">
            <h1 class="text-lg font-bold text-slate-800">{{ $exam->name }} - {{ $paper->name }}</h1>
            <p class="text-xs text-slate-500">{{ $school->name }} | Stream: {{ $stream->name }}</p>
        </div>

        {{-- Delete All Action --}}
        <div class="bg-red-50 border border-red-100 p-4 rounded-lg flex items-center justify-between shadow-sm">
            <p class="text-xs text-red-700">
                <i class="bx bx-error-circle mr-1"></i>
                Need to restart? 
                <a href="#" @click.prevent="confirmDeleteAll()" class="font-bold underline hover:text-red-900">
                    Click here
                </a> to delete all marks entered for this stream.
            </p>
            
            <form id="deleteAllForm" 
                  action="{{ route('marks.delete-all', [$exam->id, $exam->slug, $school->id, $school->slug, $stream->id, $paper->id]) }}" 
                  method="POST" 
                  class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>

        {{-- Toolbar --}}
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-3">
            <input type="text" x-model="search" placeholder="Search by name or ADM..."
                class="w-full border border-slate-300 px-3 py-2 rounded-md text-sm focus:ring-1 focus:ring-indigo-500 outline-none">
        </div>

        {{-- Main Table --}}
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse whitespace-nowrap">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] md:text-xs">
                        <tr>
                            <th class="px-3 py-2">#</th>
                            <th class="px-3 py-2 cursor-pointer hover:text-indigo-600" @click="toggleSort('adm')">ADM ↕</th>
                            <th class="px-3 py-2">Name</th>
                            <th class="px-3 py-2 text-right cursor-pointer hover:text-indigo-600" @click="toggleSort('score')">Score ↕</th>
                            <th class="px-3 py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(student, index) in filteredStudents" :key="student.id">
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2 text-[10px] text-slate-400" x-text="index + 1"></td>
                                <td class="px-3 py-2 font-bold text-slate-700 text-xs" x-text="student.admission_number"></td>
                                <td class="px-3 py-2 text-slate-800 text-xs" x-text="student.name"></td>
                                <td class="px-3 py-2 text-right">
                                    <div x-show="!student.isEditing" class="font-bold text-indigo-600" x-text="student.mark?.score ?? '-'"></div>
                                    <div x-show="student.isEditing" class="flex justify-end gap-1">
                                        <input type="number" x-model="student.tempScore" 
                                            @keydown.enter.prevent="saveScore(student)"
                                            class="w-16 border rounded p-1 text-center text-xs"
                                            :class="isInvalid(student.tempScore) ? 'border-red-500' : 'border-slate-300'">
                                        <button @click="saveScore(student)" class="text-green-600 hover:text-green-800"><i class="bx bx-check text-lg"></i></button>
                                        <button @click="student.isEditing = false" class="text-red-600 hover:text-red-800"><i class="bx bx-x text-lg"></i></button>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="student.isEditing = true; student.tempScore = student.mark?.score" class="text-blue-500 hover:text-blue-700"><i class="bx bx-pencil"></i></button>
                                        <button @click="deleteScore(student)" class="text-red-500 hover:text-red-700"><i class="bx bx-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredStudents.length === 0">
                            <td colspan="5" class="px-3 py-8 text-center text-slate-400 text-sm italic">No students found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-t">{{ $students->links() }}</div>
        </div>

        {{-- Add Results --}}
        <div x-data="{ showAdd: false }" class="bg-white rounded-lg shadow-sm border border-slate-200">
            <button @click="showAdd = !showAdd" class="w-full p-4 text-left font-bold text-indigo-600 hover:bg-slate-50 text-sm">
                <i class="bx bx-plus-circle"></i> Add Results
            </button>
            <div x-show="showAdd" class="p-4 border-t" x-cloak>
                @if ($studentsWithoutMarks->isEmpty())
                    <div class="text-center py-4 text-sm text-slate-500 italic">All students have results.</div>
                @else
                    <form action="{{ route('marks.admin-store', [$exam->id, $exam->slug, $school->id, $school->slug, $stream->id, $paper->id]) }}" method="POST">
                        @csrf
                        <div class="max-h-96 overflow-y-auto mb-4 border rounded">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-100 uppercase text-[10px] sticky top-0">
                                    <tr><th class="p-2">ADM</th><th class="p-2">Name</th><th class="p-2">Score</th></tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($studentsWithoutMarks as $index => $student)
                                        <tr>
                                            <td class="p-2 text-xs">{{ $student->admission_number }}</td>
                                            <td class="p-2 text-xs">{{ $student->name }}</td>
                                            <td class="p-2"><input type="number" name="marks[{{ $student->id }}]" class="new-mark-input w-20 border rounded p-1 text-center text-xs" @keydown.enter.prevent="focusNext('.new-mark-input', {{ $index }})" placeholder="-"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded text-xs font-bold hover:bg-indigo-700">Mass Submit</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function marksTable() {
        return {
            search: '',
            sortColumn: 'score',
            sortDirection: 'desc',
            students: @json($students->items()).map(s => ({...s, isEditing: false, tempScore: null})),
            
            isInvalid: (val) => val === '' || val < 0 || val > 100,

            toggleSort(col) {
                this.sortDirection = (this.sortColumn === col && this.sortDirection === 'desc') ? 'asc' : 'desc';
                this.sortColumn = col;
            },

            focusNext(selector, index) {
                const inputs = document.querySelectorAll(selector);
                if (inputs[index + 1]) { inputs[index + 1].focus(); inputs[index + 1].select(); }
            },

            confirmDeleteAll() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to delete all marks entered for stream {{ $stream->name }} for paper {{ $paper->name }} in {{ $exam->name }}. Do you wish to continue?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Delete All'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('deleteAllForm').submit();
                    }
                });
            },

            async saveScore(student) {
                if (this.isInvalid(student.tempScore)) {
                    Swal.fire({ icon: 'error', title: 'Invalid Score', text: 'Score must be 0-100' });
                    return;
                }
                student.mark.score = student.tempScore;
                student.isEditing = false;
                Swal.fire({ icon: 'success', title: 'Updated', timer: 1000, showConfirmButton: false });
            },

            async deleteScore(student) {
                if (!student.mark?.id) return;
                
                const result = await Swal.fire({ 
                    title: 'Delete score?', 
                    icon: 'warning', 
                    showCancelButton: true, 
                    confirmButtonText: 'Delete', 
                    confirmButtonColor: '#d33' 
                });
                
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/school/{{ $school->id }}/{{ $school->slug }}/stream/{{ $stream->id }}/paper/{{ $paper->id }}/marks/delete/${student.mark.id}`, {
                            method: 'POST',
                            headers: { 
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ _method: 'DELETE' })
                        });

                        if (response.ok) {
                            Swal.fire({ icon: 'success', title: 'Deleted', timer: 1000, showConfirmButton: false });
                            window.location.reload();
                        } else {
                            throw new Error('Server returned error');
                        }
                    } catch (e) {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete mark.' });
                    }
                }
            },

            get filteredStudents() {
                return this.students.filter(s => 
                    s.name.toLowerCase().includes(this.search.toLowerCase()) || 
                    s.admission_number.toLowerCase().includes(this.search.toLowerCase())
                ).sort((a, b) => {
                    let vA = this.sortColumn === 'score' ? (a.mark?.score || 0) : a.admission_number;
                    let vB = this.sortColumn === 'score' ? (b.mark?.score || 0) : b.admission_number;
                    return this.sortDirection === 'asc' ? (vA > vB ? 1 : -1) : (vA < vB ? 1 : -1);
                });
            }
        }
    }
</script>
@endpush