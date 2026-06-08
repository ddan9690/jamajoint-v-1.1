@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="studentManager(@js($students))" x-cloak>

    {{-- HEADER SECTION --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $school->name }}</h1>
        <div class="flex justify-between items-center mt-4">
            <p class="text-slate-500 font-medium">
                Form: <span class="text-blue-600 font-semibold">{{ $form->name }}</span> | 
                Stream: <span class="text-blue-600 font-semibold">{{ $stream->name }}</span>
            </p>
            <input type="text" x-model="search" placeholder="Search by name, adm, or index..." 
                   class="px-4 py-2 border border-slate-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500/20 w-64">
        </div>
    </div>

    {{-- ACTIONS BAR --}}
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-slate-800">Students List</h2>
        <button @click="openModal(false)" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
            + Add New Student
        </button>
    </div>

    {{-- STUDENTS TABLE --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[10px] tracking-wider">
                <tr>
                    <th class="px-4 py-3 w-12">#</th>
                    <th class="px-4 py-3 cursor-pointer hover:text-blue-600" @click="sortBy('admission_number')">Adm ↕</th>
                    <th class="px-4 py-3 cursor-pointer hover:text-blue-600" @click="sortBy('name')">Name ↕</th>
                    <th class="px-4 py-3">Gender</th>
                    <th class="px-4 py-3 cursor-pointer hover:text-blue-600" @click="sortBy('index_number')">Index ↕</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 whitespace-nowrap">
                <template x-for="(student, index) in paginatedStudents" :key="student.id">
                    <tr :id="'row-'+student.id" class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3 text-slate-500" x-text="(page * 50) + index + 1"></td>
                        <td class="px-4 py-3 font-medium text-slate-900" x-text="student.admission_number"></td>
                        <td class="px-4 py-3 text-slate-600" x-text="student.name"></td>
                        <td class="px-4 py-3 text-slate-600" x-text="student.gender"></td>
                        <td class="px-4 py-3 text-slate-600" x-text="student.index_number"></td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <button @click="openModal(true, student.id, student.name, student.admission_number, student.index_number, student.gender)" 
                                    class="text-slate-400 hover:text-indigo-600 transition">
                                <i class='bx bx-edit text-base'></i>
                            </button>
                            <button @click="confirmDelete(student.id)" 
                                    class="text-red-600 hover:text-red-700 transition font-medium">
                                <i class='bx bx-trash text-base'></i>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        
        {{-- PAGINATION CONTROLS --}}
        <div class="p-4 border-t flex justify-between items-center text-slate-600">
            <span class="text-xs">Showing <span x-text="paginatedStudents.length"></span> of <span x-text="filteredStudents.length"></span> students</span>
            <div class="space-x-2">
                <button @click="page--" :disabled="page === 0" class="px-3 py-1 border rounded disabled:opacity-50">Prev</button>
                <button @click="page++" :disabled="page >= maxPage" class="px-3 py-1 border rounded disabled:opacity-50">Next</button>
            </div>
        </div>
    </div>

    {{-- MODAL (Remains same) --}}
    <div x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4" x-cloak>
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl" @click.away="closeModal()">
            <form @submit.prevent="submitStudent()" class="p-6 space-y-5">
                <h3 class="text-lg font-bold text-slate-800" x-text="editMode ? 'Edit Student' : 'Add New Student'"></h3>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Full Name</label>
                    <input type="text" x-model="name" required class="w-full px-4 py-2.5 bg-slate-50 border rounded-lg outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Admission #</label>
                        <input type="text" x-model="adm" required class="w-full px-4 py-2.5 bg-slate-50 border rounded-lg outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Gender</label>
                        <select x-model="gender" required class="w-full px-4 py-2.5 bg-slate-50 border rounded-lg outline-none">
                            <option value="">Select</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Index # (Optional)</label>
                    <input type="text" x-model="index" class="w-full px-4 py-2.5 bg-slate-50 border rounded-lg outline-none">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="closeModal()" class="px-5 py-2.5 text-slate-600 font-semibold hover:bg-slate-100 rounded-lg">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function studentManager(initialStudents) {
    return {
        students: initialStudents,
        search: '',
        page: 0,
        sortCol: 'admission_number',
        sortAsc: true,
        open: false, editMode: false, studentId: null, name: '', adm: '', index: '', gender: '',

        get filteredStudents() {
            return this.students.filter(s => 
                s.name.toLowerCase().includes(this.search.toLowerCase()) ||
                s.admission_number.toLowerCase().includes(this.search.toLowerCase()) ||
                (s.index_number && s.index_number.toLowerCase().includes(this.search.toLowerCase()))
            ).sort((a, b) => {
                let modifier = this.sortAsc ? 1 : -1;
                return (a[this.sortCol] || '').toString().localeCompare((b[this.sortCol] || '').toString()) * modifier;
            });
        },
        get paginatedStudents() { return this.filteredStudents.slice(this.page * 50, (this.page + 1) * 50); },
        get maxPage() { return Math.max(0, Math.ceil(this.filteredStudents.length / 50) - 1); },
        sortBy(col) { if (this.sortCol === col) this.sortAsc = !this.sortAsc; this.sortCol = col; },
        
        openModal(edit = false, id = null, name = '', adm = '', index = '', gender = '') {
            this.open = true; this.editMode = edit; this.studentId = id; 
            this.name = name; this.adm = adm; this.index = index; this.gender = gender;
        },
        closeModal() { this.open = false; },
        
        submitStudent() {
            let url = this.editMode ? "{{ route('schools.forms.streams.students.update', [$school->id, $school->slug, $form->id, $stream->id, ':id']) }}".replace(':id', this.studentId) : "{{ route('schools.forms.streams.students.store', [$school->id, $school->slug, $form->id, $stream->id]) }}";
            $.ajax({
                url: url, type: 'POST',
                data: { name: this.name, admission_number: this.adm, index_number: this.index, gender: this.gender, _token: $('meta[name="csrf-token"]').attr('content'), ...(this.editMode ? { _method: 'PUT' } : {}) },
                success: () => { location.reload(); }
            });
        },
        confirmDelete(id) {
            Swal.fire({ title: 'Are you sure?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Yes, delete' })
            .then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('schools.forms.streams.students.destroy', [$school->id, $school->slug, $form->id, $stream->id, ':id']) }}".replace(':id', id),
                        type: 'POST',
                        data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
                        success: () => { location.reload(); }
                    });
                }
            });
        }
    }
}
</script>
@endpush