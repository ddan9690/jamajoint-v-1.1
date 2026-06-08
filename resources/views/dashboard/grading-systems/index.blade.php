@extends('layouts.app')

@section('content')
<div class="space-y-4" x-data="gradingManager()" x-cloak>

    <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Grading Systems</h1>
            <p class="text-slate-500 text-sm mt-1">Define structures for calculating results.</p>
        </div>
        <button @click="openModal(false)" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
            + Create System
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[10px] tracking-wider">
                <tr>
                    <th class="px-4 py-3">System Name</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($gradingSystems as $system)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $system->name }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($system->is_active)
                                <span class="text-green-600 font-bold">Active</span>
                            @else
                                <span class="text-slate-400">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('grading-systems.grades.index', $system->id) }}" 
                               class="text-blue-600 hover:underline">Manage Grades</a>
                            <button @click="openModal(true, {{ $system->id }}, '{{ addslashes($system->name) }}')" 
                                    class="text-slate-400 hover:text-indigo-600"><i class='bx bx-edit'></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- MODAL --}}
    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-cloak>
        <div class="bg-white rounded-xl w-full max-w-sm p-6 shadow-xl" @click.away="closeModal()">
            <h3 class="text-lg font-bold mb-4" x-text="editMode ? 'Edit System' : 'New Grading System'"></h3>
            <form @submit.prevent="submitSystem()">
                <input type="text" x-model="name" required class="w-full p-2 border rounded-lg" placeholder="e.g. KCSE 2026">
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="closeModal()" class="text-slate-500">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function gradingManager() {
    return {
        open: false, editMode: false, systemId: null, name: '',
        openModal(edit = false, id = null, n = '') {
            this.open = true; this.editMode = edit; this.systemId = id; this.name = n;
        },
        closeModal() { this.open = false; },
        submitSystem() {
            let url = this.editMode 
                ? "{{ route('grading-systems.update', ':id') }}".replace(':id', this.systemId) 
                : "{{ route('grading-systems.store') }}";
            
            $.ajax({
                url: url, 
                type: 'POST',
                data: { 
                    name: this.name, 
                    _token: $('meta[name="csrf-token"]').attr('content'), 
                    ...(this.editMode ? {_method: 'PUT'} : {}) 
                },
                success: () => location.reload(),
                error: (xhr) => toastr.error('An error occurred.')
            });
        }
    }
}
</script>
@endpush