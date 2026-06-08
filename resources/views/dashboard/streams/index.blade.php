@extends('layouts.app')

@section('content')
<div class="space-y-4" x-data="streamManager()" x-cloak>

    {{-- ALERTS --}}
    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium">{{ session('success') }}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="text-green-700 hover:text-green-900 font-bold text-sm">✕</button>
            </div>
        </div>
    @endif

    {{-- HEADER SECTION --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $school->name }}</h1>
        <p class="text-slate-500 font-medium mt-1">
            Form Management: <span class="text-blue-600 font-semibold">{{ $form->name }}</span>
        </p>
    </div>

    {{-- ACTIONS BAR --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Streams</h2>
            <p class="text-xs text-slate-400">Manage streams and student records for this form</p>
        </div>
        
        <div class="flex gap-2">
            {{-- DESCRIPTIVE IMPORT BUTTON --}}
            <a href="{{ route('schools.forms.import.view', [$school->id, $school->slug, $form->id]) }}" 
               class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-700 transition flex items-center gap-2 group">
                <i class='bx bx-cloud-upload text-lg'></i>
                <div class="flex flex-col text-left">
                    <span>Import Students</span>
                    <span class="text-[10px] opacity-80 font-normal">Bulk upload via Excel file</span>
                </div>
            </a>
            
            <button @click="openModal(false)" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                + Add New Stream
            </button>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[10px] tracking-wider">
                <tr>
                    <th class="px-4 py-3 w-12">#</th>
                    <th class="px-4 py-3">Stream</th>
                    <th class="px-4 py-3 text-center">Students</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 whitespace-nowrap">
                @forelse($streams as $index => $stream)
                    <tr id="row-{{ $stream->id }}" class="hover:bg-slate-50 transition">
                        <td class="px-4 py-2 text-slate-500">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 font-medium text-blue-600 hover:underline">
                            <a href="{{ route('schools.forms.streams.students.index', [$school->id, $school->slug, $form->id, $stream->id]) }}">
                                {{ $stream->name }}
                            </a>
                        </td>
                        <td class="px-4 py-2 text-center text-slate-600">{{ $stream->students_count }}</td>
                        <td class="px-4 py-2 text-right space-x-3">
                            <button @click="openModal(true, {{ $stream->id }}, '{{ addslashes($stream->name) }}')"
                                    class="text-slate-400 hover:text-indigo-600 transition">
                                <i class='bx bx-edit text-base'></i>
                            </button>
                            <button onclick="confirmDelete({{ $stream->id }})"
                                    class="text-red-600 hover:text-red-700 transition font-medium">
                                <i class='bx bx-trash text-base'></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">No streams found for this form.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL --}}
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-cloak>
        <div class="bg-white rounded-xl w-full max-w-sm p-6 shadow-xl" @click.away="closeModal()">
            <h3 class="text-lg font-bold mb-4 text-slate-800" x-text="editMode ? 'Edit Stream' : 'Add New Stream'"></h3>
            <form @submit.prevent="submitStream()">
                <input type="text" x-model="streamName" required class="w-full p-2 border-2 border-slate-200 rounded-lg focus:border-blue-500 outline-none transition" placeholder="Enter stream name">
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="closeModal()" class="text-slate-500 font-medium hover:text-slate-700">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function streamManager() {
    return {
        open: false, editMode: false, streamId: null, streamName: '',
        openModal(edit = false, id = null, name = '') {
            this.open = true; this.editMode = edit; this.streamId = id; this.streamName = name;
        },
        closeModal() { this.open = false; this.editMode = false; this.streamId = null; this.streamName = ''; },
        submitStream() {
            let url = this.editMode 
                ? "{{ route('schools.forms.streams.update', [$school->id, $school->slug, $form->id, ':id']) }}".replace(':id', this.streamId)
                : "{{ route('schools.forms.streams.store', [$school->id, $school->slug, $form->id]) }}";
            $.ajax({
                url: url, type: 'POST',
                data: { name: this.streamName, _token: $('meta[name="csrf-token"]').attr('content'), ...(this.editMode ? { _method: 'PUT' } : {}) },
                success: (res) => { toastr.success(res.message); location.reload(); },
                error: (xhr) => { toastr.error(xhr.responseJSON?.message || 'Error occurred.'); }
            });
        }
    }
}

function confirmDelete(id) {
    Swal.fire({ title: 'Are you sure?', text: "This action cannot be undone.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Yes, delete it' })
    .then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('schools.forms.streams.destroy', [$school->id, $school->slug, $form->id, ':id']) }}".replace(':id', id),
                type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
                success: function(res) { $('#row-'+id).fadeOut(300, () => $(this).remove()); toastr.success(res.message); },
                error: function(xhr) { toastr.error('Could not delete stream.'); }
            });
        }
    });
}
</script>
@endpush