@extends('layouts.app')

@section('content')
<div class="space-y-4" x-data="subjectManager()" x-cloak>

    {{-- HEADER SECTION --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">System Subjects</h1>
            <p class="text-slate-500 text-sm mt-1">Manage global curriculum subjects and their respective papers.</p>
        </div>
        <button @click="openModal(false)" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
            + Add New Subject
        </button>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[10px] tracking-wider">
                <tr>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Short</th>
                    <th class="px-4 py-3 text-center">Compulsory</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($subjects as $subject)
                    <tr id="row-{{ $subject->id }}" class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3 font-mono text-slate-600">{{ $subject->code }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $subject->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $subject->short }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($subject->is_compulsory)
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-[10px] font-bold uppercase">Yes</span>
                            @else
                                <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-[10px] font-bold uppercase">No</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            {{-- Manage Papers Action --}}
                            <a href="{{ route('subjects.show', [$subject->id, $subject->slug]) }}" 
                               class="text-slate-400 hover:text-blue-600 transition" title="Manage Papers">
                                <i class='bx bx-file'></i>
                            </a>
                            {{-- Edit Action --}}
                            <button @click="openModal(true, {{ $subject->id }}, '{{ addslashes($subject->name) }}', '{{ $subject->code }}', '{{ $subject->short }}', {{ $subject->is_compulsory ? 1 : 0 }}, '{{ $subject->slug }}')"
                                    class="text-slate-400 hover:text-indigo-600 transition" title="Edit"><i class='bx bx-edit'></i></button>
                            {{-- Delete Action --}}
                            <button onclick="confirmDelete({{ $subject->id }}, '{{ $subject->slug }}')"
                                    class="text-red-600 hover:text-red-700 transition" title="Delete"><i class='bx bx-trash'></i></button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No subjects defined yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL --}}
    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-cloak>
        <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-xl">
            <h3 class="text-lg font-bold mb-4 text-slate-800" x-text="editMode ? 'Edit Subject' : 'Add New Subject'"></h3>
            <form @submit.prevent="submitSubject()">
                <div class="space-y-4">
                    <input type="text" x-model="formData.name" required class="w-full p-2 border rounded-lg" placeholder="Subject Name">
                    <div class="flex gap-4">
                        <input type="text" x-model="formData.code" required class="w-1/2 p-2 border rounded-lg" placeholder="Code (e.g. 101)">
                        <input type="text" x-model="formData.short" class="w-1/2 p-2 border rounded-lg" placeholder="Short Name">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                        <input type="checkbox" x-model="formData.is_compulsory" class="w-4 h-4 text-blue-600"> Compulsory Subject
                    </label>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="closeModal()" class="text-slate-500 font-medium">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function subjectManager() {
    return {
        open: false, editMode: false, subjectSlug: null,
        formData: { id: null, name: '', code: '', short: '', is_compulsory: false },
        
        openModal(edit = false, id = null, name = '', code = '', short = '', comp = 0, slug = '') {
            this.open = true; 
            this.editMode = edit; 
            this.subjectSlug = slug;
            this.formData = { id, name, code, short, is_compulsory: !!comp };
        },
        
        closeModal() { this.open = false; },
        
        submitSubject() {
            let url = this.editMode 
                ? "{{ route('subjects.update', ['subject' => ':id', 'slug' => ':slug']) }}".replace(':id', this.formData.id).replace(':slug', this.subjectSlug)
                : "{{ route('subjects.store') }}";
            
            $.ajax({
                url: url,
                type: 'POST',
                data: { 
                    ...this.formData, 
                    _token: $('meta[name="csrf-token"]').attr('content'), 
                    _method: this.editMode ? 'PUT' : 'POST' 
                },
                success: (res) => { 
                    toastr.success(res.message); 
                    location.reload(); 
                },
                error: (xhr) => {
                    toastr.error(xhr.responseJSON?.message || 'Operation failed.');
                }
            });
        }
    }
}

function confirmDelete(id, slug) {
    Swal.fire({ 
        title: 'Delete Subject?', 
        text: 'This will also remove all associated papers.',
        icon: 'warning', 
        showCancelButton: true, 
        confirmButtonColor: '#dc2626', 
        confirmButtonText: 'Yes, delete' 
    }).then((res) => {
        if (res.isConfirmed) {
            $.ajax({
                url: "{{ route('subjects.destroy', ['subject' => ':id', 'slug' => ':slug']) }}".replace(':id', id).replace(':slug', slug),
                type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
                success: () => { $('#row-'+id).fadeOut(); toastr.success('Deleted'); }
            });
        }
    });
}
</script>
@endpush