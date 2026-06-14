@extends('layouts.app')

@section('content')
<div class="px-2 md:px-4 space-y-6" x-data="{ schoolSearch: '' }">
    {{-- Header with Status Change Button --}}
    <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-slate-200">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-slate-800">{{ $exam->name }}</h1>
                <p class="text-xs md:text-sm text-slate-500 mt-1">
                    {{ $exam->subject->name ?? 'N/A' }} | 
                    Form: {{ $exam->form->name ?? 'N/A' }} | 
                    Status: <span class="uppercase font-bold {{ $exam->status === 'finalized' ? 'text-green-600' : ($exam->status === 'processing' ? 'text-amber-600' : 'text-slate-400') }}">
                        {{ $exam->status }}
                    </span>
                </p>
            </div>
            
            @can('manage-exams')
                @if($exam->status === 'processing')
                    <button type="button" 
                            onclick="confirmStatusChange('{{ route('exams.change-status', [$exam->id, $exam->slug]) }}', 'publish')"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors flex items-center gap-2">
                        <i class='bx bx-check-circle'></i> Publish Exam
                    </button>
                @elseif($exam->status === 'finalized')
                    <button type="button" 
                            onclick="confirmStatusChange('{{ route('exams.change-status', [$exam->id, $exam->slug]) }}', 'unpublish')"
                            class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors flex items-center gap-2">
                        <i class='bx bx-undo'></i> Unpublish Exam
                    </button>
                @else
                    <button type="button" 
                            onclick="confirmStatusChange('{{ route('exams.change-status', [$exam->id, $exam->slug]) }}', 'start')"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors flex items-center gap-2">
                        <i class='bx bx-play'></i> Start Exam
                    </button>
                @endif
            @endcan
        </div>
        
        {{-- Visibility Status with Click to Change --}}
        @if($exam->status === 'finalized')
            <div class="mt-4 pt-3 border-t border-slate-100">
                @if($exam->visibility === 'private')
                    <p class="text-sm text-slate-600">
                        <i class='bx bx-lock text-amber-600'></i> 
                        Results are currently <span class="font-semibold text-amber-600">private</span>. 
                        Only admins can view the analyzed results.
                        <button type="button" 
                                onclick="changeVisibility('{{ route('exams.change-visibility', [$exam->id, $exam->slug]) }}', 'public')"
                                class="text-indigo-600 hover:text-indigo-800 font-medium ml-1 underline">
                                Click here to make public
                        </button>
                    </p>
                @else
                    <p class="text-sm text-slate-600">
                        <i class='bx bx-globe text-green-600'></i> 
                        Results are currently <span class="font-semibold text-green-600">public</span>. 
                        Participating schools can view the analyzed results.
                        <button type="button" 
                                onclick="changeVisibility('{{ route('exams.change-visibility', [$exam->id, $exam->slug]) }}', 'private')"
                                class="text-indigo-600 hover:text-indigo-800 font-medium ml-1 underline">
                                Click here to make private
                        </button>
                    </p>
                @endif
            </div>
        @endif
    </div>

    {{-- Exam Settings Section (Exam Configurations) with Inline Editing --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden" 
         x-data="examConfigurations({{ json_encode($exam->subject->papers->map(function($paper) use ($exam) {
             $config = $exam->configurations->where('paper_id', $paper->id)->first();
             return [
                 'paper_id' => $paper->id,
                 'paper_name' => $paper->name,
                 'max_score' => $config ? $config->max_score : '',
                 'weight' => $config ? (int) $config->weight : '',
                 'has_config' => !is_null($config)
             ];
         })) }})">
        
        <div class="p-4 border-b bg-slate-50">
            <div>
                <h3 class="font-bold text-slate-700 flex items-center gap-2">
                    <i class='bx bx-cog'></i> Exam Settings
                </h3>
                <p class="text-xs text-slate-500 mt-1">Paper configurations (max score & weight %) for this exam</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs border-b">
                    <tr>
                        <th class="px-4 py-3">Paper</th>
                        <th class="px-4 py-3 text-center">Max Score</th>
                        <th class="px-4 py-3 text-center">Weight (%)</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(paper, index) in papers" :key="paper.paper_id">
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-700" x-text="paper.paper_name"></td>
                            <td class="px-4 py-3 text-center">
                                <div x-show="!paper.editing">
                                    <span x-show="paper.has_config" class="font-bold text-indigo-600" x-text="paper.max_score"></span>
                                    <span x-show="!paper.has_config" class="text-slate-400 italic">Not set</span>
                                </div>
                                <div x-show="paper.editing" class="flex justify-center gap-1">
                                    <input type="number" 
                                           x-model="paper.max_score" 
                                           class="w-20 border border-slate-300 rounded px-2 py-1 text-center text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                                           min="0"
                                           step="1">
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div x-show="!paper.editing">
                                    <span x-show="paper.has_config" class="font-bold text-indigo-600" x-text="paper.weight + '%'"></span>
                                    <span x-show="!paper.has_config" class="text-slate-400 italic">Not set</span>
                                </div>
                                <div x-show="paper.editing" class="flex justify-center gap-1">
                                    <input type="number" 
                                           x-model="paper.weight" 
                                           class="w-20 border border-slate-300 rounded px-2 py-1 text-center text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                                           min="0"
                                           max="100"
                                           step="1">
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div x-show="!paper.editing">
                                    <button @click="startEdit(index)" 
                                            class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                        Change
                                    </button>
                                </div>
                                <div x-show="paper.editing" class="flex justify-center gap-2">
                                    <button @click="saveConfig(index)" 
                                            class="text-green-600 hover:text-green-800 text-xs font-medium">
                                        Save
                                    </button>
                                    <button @click="cancelEdit(index)" 
                                            class="text-red-600 hover:text-red-800 text-xs font-medium">
                                        Cancel
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <div class="p-3 border-t bg-slate-50 text-right">
            <span class="text-xs text-slate-500">
                <i class='bx bx-info-circle'></i> 
                Total papers configured: <span x-text="configuredCount"></span> / <span x-text="papers.length"></span>
            </span>
        </div>
    </div>

    {{-- Participating Schools Table --}}
    @can('manage-exams')
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <h3 class="font-bold text-slate-800">Participating Schools ({{ $registeredCount }})</h3>
                <input type="text" x-model="schoolSearch" placeholder="Search schools..." 
                       class="border p-2 rounded-lg text-sm w-full md:w-64">
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                        <tr>
                            <th class="p-3 whitespace-nowrap">School</th>
                            <th class="p-3 whitespace-nowrap">County</th>
                            @foreach($exam->subject->papers as $paper)
                                <th class="p-3 text-center whitespace-nowrap">{{ $paper->name }}</th>
                            @endforeach
                            <th class="p-3 text-center whitespace-nowrap">Marksheet</th>
                            <th class="p-3 text-right whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($schools as $school)
                            <tr x-show="schoolSearch === '' || '{{ strtolower($school->name) }}'.includes(schoolSearch.toLowerCase())">
                                <td class="p-3 font-medium text-slate-800 whitespace-nowrap">{{ $school->name }}</td>
                                <td class="p-3 text-slate-600 whitespace-nowrap">{{ $school->county->name ?? 'N/A' }}</td>
                                
                                @foreach($exam->subject->papers as $paper)
                                    @php 
                                        $submitted = $school->submission_map->get($paper->id, 0);
                                        $total = $school->students_count;
                                    @endphp
                                    <td class="p-3 text-center text-xs whitespace-nowrap">
                                        <span class="{{ $submitted > 0 ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                                            {{ $submitted }}/{{ $total }}
                                        </span>
                                    </td>
                                @endforeach

                                <td class="p-3 text-center whitespace-nowrap">
                                    <a href="{{ route('exams.school.download-marksheet', [$exam->id, $exam->slug, $school->id, $school->slug]) }}"
                                       class="text-indigo-600 font-bold text-xs hover:underline">
                                       Download
                                    </a>
                                </td>

                                <td class="p-3 text-right whitespace-nowrap">
                                    @php 
                                        $hasAnySubmission = $school->submission_map->contains(fn($count) => $count > 0);
                                    @endphp
                                    <div class="flex gap-2 justify-end">
                                        @if($hasAnySubmission)
                                            <a href="{{ route('exams.school.view-submissions', [$exam->id, $exam->slug, $school->id, $school->slug]) }}"
                                               class="px-3 py-1 rounded text-xs font-bold text-white bg-green-600 hover:bg-green-700">
                                               View
                                            </a>
                                        @else
                                            <a href="{{ route('marks.submit-streams', [$exam->id, $exam->slug, $school->id, $school->slug]) }}"
                                               class="px-3 py-1 rounded text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700">
                                               Submit
                                            </a>
                                        @endif
                                        
                                        <button onclick="confirmRemove('{{ route('exams.remove-school', [$exam->id, $school->id]) }}')"
                                                class="text-red-500 font-bold text-xs underline">Remove</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Configuration Grid (Register Schools & Admins) --}}
        @can('super-admin')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Register Schools Section --}}
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden" x-data="{ search: '' }">
                    <div class="p-4 border-b bg-slate-50 font-bold text-slate-700">Register Schools</div>
                    <div class="p-2 border-b"><input type="text" x-model="search" placeholder="Search..." class="w-full border p-2 rounded text-sm"></div>
                    <form action="{{ route('exams.register-schools-bulk', $exam->id) }}" method="POST">
                        @csrf
                        <div class="max-h-60 overflow-y-auto">
                            <table class="w-full text-left text-sm">
                                <tbody class="divide-y">
                                    @foreach ($allSchools as $school)
                                        <tr x-show="search === '' || '{{ strtolower($school->name) }}'.includes(search.toLowerCase())">
                                            <td class="px-4 py-2"><input type="checkbox" name="school_ids[]" value="{{ $school->id }}"></td>
                                            <td class="px-4 py-2 font-medium">{{ $school->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t flex justify-between items-center bg-slate-50">
                            <div>{{ $allSchools->links() }}</div>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">Register Selected</button>
                        </div>
                    </form>
                </div>

                {{-- Admin Access Section --}}
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-4 border-b bg-slate-50 font-bold text-slate-700">Exam Admin Access</div>
                    <table class="w-full text-left text-sm">
                        <tbody class="divide-y">
                            @forelse($exam->examAdmins as $admin)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $admin->user->name ?? 'Unknown' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <button onclick="confirmAdminRemove('{{ route('exams.remove-admin', [$exam->id, $admin->user_id]) }}')" class="text-red-500 text-xs underline">Remove</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="px-4 py-4 text-center text-slate-400 italic">No admins assigned.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <form action="{{ route('exams.add-admin', $exam->id) }}" method="POST" class="p-4 border-t bg-slate-50 flex gap-2">
                        @csrf
                        <select name="user_id" id="admin-select" class="w-full" required>
                            <option value="">Select a user...</option>
                            @foreach (\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">Add</button>
                    </form>
                </div>
            </div>
        @endcan
    @endcan
</div>

<script>
    function examConfigurations(initialPapers) {
        return {
            papers: initialPapers.map(p => ({
                ...p,
                editing: false,
                original_max_score: p.max_score,
                original_weight: p.weight
            })),
            
            get configuredCount() {
                return this.papers.filter(p => p.has_config).length;
            },
            
            startEdit(index) {
                this.papers[index].editing = true;
                this.papers[index].original_max_score = this.papers[index].max_score;
                this.papers[index].original_weight = this.papers[index].weight;
            },
            
            cancelEdit(index) {
                this.papers[index].editing = false;
                this.papers[index].max_score = this.papers[index].original_max_score;
                this.papers[index].weight = this.papers[index].original_weight;
            },
            
            saveConfig(index) {
                const paper = this.papers[index];
                
                const maxScoreChanged = paper.max_score != paper.original_max_score;
                const weightChanged = paper.weight != paper.original_weight;
                
                if (!maxScoreChanged && !weightChanged) {
                    paper.editing = false;
                    Swal.fire({
                        icon: 'info',
                        title: 'No Changes',
                        text: 'No changes were made to the configuration',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
                
                if (paper.max_score === '' || paper.max_score === null) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Max score is required' });
                    return;
                }
                if (paper.weight === '' || paper.weight === null) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Weight is required' });
                    return;
                }
                
                const maxScore = parseInt(paper.max_score);
                const weight = parseInt(paper.weight);
                
                if (isNaN(maxScore) || maxScore < 0) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Max score must be a positive number' });
                    return;
                }
                if (isNaN(weight) || weight < 0 || weight > 100) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Weight must be between 0 and 100' });
                    return;
                }
                
                fetch('{{ route("exams.configurations.store", [$exam->id, $exam->slug]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        paper_id: paper.paper_id,
                        max_score: maxScore,
                        weight: weight
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        paper.has_config = true;
                        paper.editing = false;
                        paper.original_max_score = paper.max_score;
                        paper.original_weight = paper.weight;
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message || 'Configuration saved successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to save configuration' });
                        paper.max_score = paper.original_max_score;
                        paper.weight = paper.original_weight;
                        paper.editing = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while saving' });
                    paper.max_score = paper.original_max_score;
                    paper.weight = paper.original_weight;
                    paper.editing = false;
                });
            }
        }
    }

    function confirmStatusChange(url, action) {
        let title, confirmButtonText, icon;
        
        if (action === 'publish') {
            title = 'Publish this exam?';
            confirmButtonText = 'Yes, Publish';
            icon = 'success';
        } else if (action === 'unpublish') {
            title = 'Unpublish this exam?';
            confirmButtonText = 'Yes, Unpublish';
            icon = 'warning';
        } else {
            title = 'Start this exam?';
            confirmButtonText = 'Yes, Start';
            icon = 'info';
        }
        
        Swal.fire({
            title: title,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmButtonText,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.action = url;
                form.method = 'POST';
                form.innerHTML = '@csrf @method("PATCH")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function changeVisibility(url, targetVisibility) {
        let title, confirmButtonText, message;
        
        if (targetVisibility === 'public') {
            title = 'Make Results Public?';
            message = 'When you make results public, all participating schools will be able to view the analyzed results.';
            confirmButtonText = 'Yes, Make Public';
        } else {
            title = 'Make Results Private?';
            message = 'When you make results private, only admins will be able to view the analyzed results.';
            confirmButtonText = 'Yes, Make Private';
        }
        
        Swal.fire({
            title: title,
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmButtonText,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.action = url;
                form.method = 'POST';
                form.innerHTML = '@csrf @method("PATCH")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function confirmRemove(url) {
        Swal.fire({
            title: 'Remove School?',
            text: 'This school will no longer have access to this exam. Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Remove',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm(url, 'DELETE');
            }
        });
    }

    function confirmAdminRemove(url) {
        Swal.fire({
            title: 'Remove Admin Access?',
            text: 'This user will no longer have admin access to this exam.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Remove',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm(url, 'DELETE');
            }
        });
    }

    function submitForm(url, method) {
        let form = document.createElement('form');
        form.action = url;
        form.method = 'POST';
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    }
</script>

@push('scripts')
    <script>
        $(document).ready(function() { 
            $('#admin-select').select2({ placeholder: "Search for a user...", width: '100%' }); 
        });
        
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: @json(session('success')),
                timer: 3000,
                showConfirmButton: false
            });
        @endif
        
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: @json(session('error')),
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
@endpush
@endsection