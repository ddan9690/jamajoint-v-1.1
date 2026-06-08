@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="importManager()">
    <div class="bg-white p-8 rounded-lg shadow-sm border border-slate-200">
        <h2 class="text-xl font-bold text-slate-800">Bulk Import Students</h2>
        <p class="text-slate-500 text-sm mt-2">
            Upload an Excel file to process streams and students. 
            Required headers: <b>adm, index, name, stream, gender</b>.
        </p>

        {{-- Form submission handled by Alpine.js AJAX --}}
        <form x-ref="importForm" @submit.prevent="uploadFile()" class="mt-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700">Select Excel File (.xlsx, .xls)</label>
                <input type="file" name="file" x-ref="fileInput" accept=".xlsx, .xls" required 
                       class="mt-2 block w-full text-sm text-slate-500 border border-slate-300 rounded-lg p-2 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
            </div>
            
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('schools.forms.streams.index', [$school->id, $school->slug, $form->id]) }}" 
                   class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Cancel</a>
                <button type="submit" x-bind:disabled="isUploading"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 disabled:bg-blue-300 transition-colors">
                    <span x-text="isUploading ? 'Uploading...' : 'Process Import'"></span>
                </button>
            </div>
        </form>
    </div>

    {{-- Progress Modal --}}
    <div x-show="isUploading" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-cloak>
        <div class="bg-white rounded-lg p-6 w-full max-w-sm shadow-xl">
            <h3 class="font-bold text-lg mb-4 text-slate-800">Uploading File...</h3>
            <div class="w-full bg-slate-100 rounded-full h-4 overflow-hidden border border-slate-200">
                <div class="bg-blue-600 h-4 transition-all duration-300" :style="'width: ' + uploadProgress + '%'"></div>
            </div>
            <p class="text-center text-sm mt-2 font-medium text-slate-600" x-text="uploadProgress + '%'"></p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function importManager() {
    return {
        isUploading: false,
        uploadProgress: 0,
        uploadFile() {
            const file = this.$refs.fileInput.files[0];
            if (!file) return;

            // Client-Side File Type Validation
            const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
            if (!validTypes.includes(file.type)) {
                Swal.fire({ icon: 'error', title: 'Invalid File', text: 'Please upload a valid Excel file (.xlsx or .xls).' });
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            this.isUploading = true;
            this.uploadProgress = 0;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', "{{ route('schools.forms.import.process', [$school->id, $school->slug, $form->id]) }}", true);

            // Track upload progress
            xhr.upload.onprogress = (e) => {
                if (e.lengthComputable) {
                    this.uploadProgress = Math.round((e.loaded / e.total) * 100);
                }
            };

            // Handle Server Response
            xhr.onload = () => {
                this.isUploading = false;
                let response;
                try { 
                    response = JSON.parse(xhr.responseText); 
                } catch (e) { 
                    response = { message: 'An error occurred. Please ensure your columns are correct: adm, index, name, stream, gender.' }; 
                }

                if (xhr.status === 200 && response.success) {
                    Swal.fire({ icon: 'success', title: 'Success!', text: response.message })
                        .then(() => window.location.href = "{{ route('schools.forms.streams.index', [$school->id, $school->slug, $form->id]) }}");
                } else {
                    Swal.fire({ icon: 'error', title: 'Import Failed', text: response.message });
                }
            };

            xhr.onerror = () => {
                this.isUploading = false;
                Swal.fire({ icon: 'error', title: 'Upload Failed', text: 'A network error occurred.' });
            };

            xhr.send(formData);
        }
    }
}
</script>
@endpush