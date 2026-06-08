@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Exams</h1>
            <a href="{{ route('exams.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Create
                New</a>
        </div>

        <div class="bg-white rounded-lg shadow border overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 uppercase text-[10px] text-slate-500 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Subject</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    @foreach ($exams as $exam)
                        <tr>
                            <td class="px-6 py-4 font-semibold text-blue-600 hover:underline">
                                <a href="{{ route('exams.show', ['exam' => $exam->id, 'slug' => $exam->slug]) }}">
                                    {{ $exam->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $exam->subject->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 capitalize">{{ $exam->status }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                {{-- Passing the ID and Slug as required by your edit route --}}
                                <a href="{{ route('exams.edit', ['exam' => $exam->id, 'slug' => $exam->slug]) }}"
                                    class="text-blue-600 hover:underline">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
