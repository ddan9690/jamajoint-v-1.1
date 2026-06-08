@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        {{-- HEADER --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">{{ $school->name }}</h2>
                <p class="text-slate-500 text-sm">
                    County: {{ $school->county->name ?? 'N/A' }} | Type: {{ ucfirst($school->type) }}
                </p>
            </div>
            <a href="{{ route('schools.index') }}" class="text-sm text-slate-500 hover:text-blue-600 transition">
                &larr; Back to List
            </a>
        </div>

        {{-- FORMS GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($forms as $form)
                <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 hover:shadow-md transition">
                    <h3 class="text-lg font-bold text-slate-800">{{ $form->name }}</h3>
                    <div class="mt-2 text-3xl font-semibold text-blue-600">{{ $form->total_students }}</div>
                    <p class="text-slate-500 text-sm mb-4">Total Students</p>

                    <a href="{{ route('schools.forms.streams.index', [
                        'school' => $school->id, 
                        'slug'   => $school->slug, 
                        'form'   => $form->id
                    ]) }}" 
                       class="block text-center w-full bg-slate-100 hover:bg-slate-200 text-slate-700 py-2 rounded-lg text-sm font-semibold transition">
                        Manage Streams
                    </a>
                </div>
            @endforeach
        </div>

        {{-- TEACHERS SECTION --}}
        <div class="mt-10">
            <h3 class="text-xl font-bold text-slate-800 mb-4">Teachers in this School</h3>
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Phone</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($teachers as $teacher)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $teacher->name }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $teacher->phone ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-slate-500 italic">
                                    No teachers in this school found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection