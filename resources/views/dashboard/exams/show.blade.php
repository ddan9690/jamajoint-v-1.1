@extends('layouts.app')

@section('content')
    <div class="px-2 md:px-4 space-y-6" x-data="{ schoolSearch: '' }">
        {{-- Header --}}
        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-slate-200">
            <h1 class="text-xl md:text-2xl font-bold text-slate-800">{{ $exam->name }}</h1>
            <p class="text-xs md:text-sm text-slate-500 mt-1">
                {{ $exam->subject->name ?? 'N/A' }} | 
                Form: {{ $exam->form->name ?? 'N/A' }} | 
                Status: <span class="uppercase font-bold text-indigo-600">{{ $exam->status }}</span>
            </p>
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

            {{-- Configuration Grid --}}
            @can('super-admin')
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
                                    <tr><td colspan="2" class="px-4 py-4 text-center text-slate-400 italic">No admins assigned.</td></tr>
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

    @push('scripts')
        <script>
            $(document).ready(function() { $('#admin-select').select2({ placeholder: "Search for a user...", width: '100%' }); });

            function confirmRemove(url) {
                Swal.fire({ title: 'Are you sure?', text: "Remove school from exam?", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes' })
                .then((result) => { if (result.isConfirmed) { submitForm(url, 'DELETE'); } });
            }

            function confirmAdminRemove(url) {
                Swal.fire({ title: 'Are you sure?', text: "Remove admin access?", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes' })
                .then((result) => { if (result.isConfirmed) { submitForm(url, 'DELETE'); } });
            }

            function submitForm(url, method) {
                let form = document.createElement('form'); form.action = url; form.method = 'POST';
                form.innerHTML = '@csrf @method("DELETE")'; document.body.appendChild(form); form.submit();
            }
        </script>
    @endpush
@endsection