@extends('layouts.app')

@section('title', 'Results - ' . $exam->name)

@section('content')
<div class="px-2 md:px-4 space-y-6">
    {{-- Header --}}
    <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-slate-200">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-slate-800">{{ $exam->name }}</h1>
                <p class="text-xs md:text-sm text-slate-500 mt-1">
                    {{ $exam->subject->name ?? 'N/A' }} | Form: {{ $exam->form->name ?? 'N/A' }} | Term: {{ $exam->term->name ?? 'N/A' }} | Year: {{ $exam->academicYear->year ?? 'N/A' }}
                    @if($exam->status === 'finalized')
                        | Status: <span class="uppercase font-bold text-green-600">finalized</span>
                    @endif
                </p>
            </div>
            
            @can('manage-exams')
                <div class="flex gap-2">
                    @if($exam->status === 'finalized')
                        <button type="button" onclick="confirmStatusChange('{{ route('exams.change-status', [$exam->id, $exam->slug]) }}', 'unpublish')" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2"><i class='bx bx-undo'></i> Unpublish</button>
                    @endif
                </div>
            @endcan
        </div>
        
        @can('manage-exams')
            @if($exam->status === 'finalized')
                <div class="mt-4 pt-3 border-t border-slate-100">
                    @if($exam->visibility === 'private')
                        <p class="text-sm text-slate-600"><i class='bx bx-lock text-amber-600'></i> Results are <span class="font-semibold text-amber-600">private</span>. Only admins can view. 
                            <button onclick="changeVisibility('{{ route('exams.change-visibility', [$exam->id, $exam->slug]) }}', 'public')" class="text-indigo-600 font-medium underline">Click to make public</button></p>
                    @else
                        <p class="text-sm text-slate-600"><i class='bx bx-globe text-green-600'></i> Results are <span class="font-semibold text-green-600">public</span>. Participating schools can view. 
                            <button onclick="changeVisibility('{{ route('exams.change-visibility', [$exam->id, $exam->slug]) }}', 'private')" class="text-indigo-600 font-medium underline">Click to make private</button></p>
                    @endif
                </div>
            @endif
        @endcan
    </div>

    {{-- Stats Cards (Dynamic Data) --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <div class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Participating Schools</div>
            <div class="text-xl md:text-2xl font-black mt-1 text-slate-800">{{ $overviewData['participating_schools'] }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <div class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Total Students</div>
            <div class="text-xl md:text-2xl font-black mt-1 text-slate-800">{{ $overviewData['total_students'] }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <div class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Mean Score</div>
            <div class="text-xl md:text-2xl font-black mt-1 text-slate-800">{{ number_format($overviewData['mean_score'], 2) }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <div class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Mean Grade</div>
            <div class="text-xl md:text-2xl font-black mt-1 text-slate-800">{{ $overviewData['overall_grade'] ?? $overviewData['mean_grade'] }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <div class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Pass Rate</div>
            {{-- <div class="text-xl md:text-2xl font-black mt-1 text-slate-800">{{ $overviewData['percentage_pass'] }}%</div> --}}
        </div>
    </div>

    {{-- Analysis Links --}}
    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 ml-1">Analysis & Reports</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="#" class="bg-white p-4 rounded-lg shadow-sm border border-slate-200 hover:shadow-md text-center transition-all"><i class='bx bx-buildings text-xl text-indigo-600'></i><p class="font-bold text-slate-800 mt-2 text-xs">School Ranking</p></a>
            <a href="#" class="bg-white p-4 rounded-lg shadow-sm border border-slate-200 hover:shadow-md text-center transition-all"><i class='bx bx-group text-xl text-indigo-600'></i><p class="font-bold text-slate-800 mt-2 text-xs">Stream Ranking</p></a>
            <a href="#" class="bg-white p-4 rounded-lg shadow-sm border border-slate-200 hover:shadow-md text-center transition-all"><i class='bx bx-user text-xl text-indigo-600'></i><p class="font-bold text-slate-800 mt-2 text-xs">Student Ranking</p></a>
            <a href="#" class="bg-white p-4 rounded-lg shadow-sm border border-slate-200 hover:shadow-md text-center transition-all"><i class='bx bx-medal text-xl text-indigo-600'></i><p class="font-bold text-slate-800 mt-2 text-xs">Overall Student Ranking</p></a>
            <a href="#" class="bg-white p-4 rounded-lg shadow-sm border border-slate-200 hover:shadow-md text-center transition-all"><i class='bx bx-trophy text-xl text-indigo-600'></i><p class="font-bold text-slate-800 mt-2 text-xs">Top Performers</p></a>
            <a href="#" class="bg-white p-4 rounded-lg shadow-sm border border-slate-200 hover:shadow-md text-center transition-all"><i class='bx bx-school text-xl text-indigo-600'></i><p class="font-bold text-slate-800 mt-2 text-xs">My School Results</p></a>
        </div>
    </div>
</div>

<script>
    function confirmStatusChange(url, action) {
        Swal.fire({ 
            title: 'Are you sure?', 
            icon: 'warning', 
            showCancelButton: true, 
            confirmButtonText: 'Yes, ' + action, 
            confirmButtonColor: '#059669' 
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
        Swal.fire({ 
            title: 'Update Visibility', 
            text: 'Set visibility to ' + targetVisibility + '?', 
            icon: 'question', 
            showCancelButton: true, 
            confirmButtonColor: '#059669', 
            confirmButtonText: 'Confirm' 
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
</script>

@push('scripts')
    <script>
        @if(session('success')) Swal.fire({icon: 'success', title: 'Success', text: @json(session('success')), timer: 3000, showConfirmButton: false}); @endif
        @if(session('error')) Swal.fire({icon: 'error', title: 'Error', text: @json(session('error')), timer: 3000, showConfirmButton: false}); @endif
    </script>
@endpush
@endsection