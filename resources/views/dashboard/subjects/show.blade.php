@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Subject Header --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
        <a href="{{ route('subjects.index') }}" class="text-sm text-blue-600 hover:underline mb-2 block">&larr; Back to Subjects</a>
        <h1 class="text-2xl font-bold text-slate-900">{{ $subject->name }}</h1>
        <p class="text-slate-500">Subject Code: <span class="font-mono bg-slate-100 px-2 py-0.5 rounded">{{ $subject->code }}</span></p>
    </div>

    {{-- Papers Management --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
        <h2 class="font-bold text-lg mb-4 text-slate-800">Manage Papers</h2>
        
        {{-- Success/Error Feedback --}}
        @if(session('success'))
            <div class="bg-green-50 text-green-700 p-3 rounded-lg mb-4 text-sm border border-green-200 font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error') || $errors->has('paper_number'))
            <div class="bg-red-50 text-red-700 p-3 rounded-lg mb-4 text-sm border border-red-200 font-medium">
                {{ session('error') ?? $errors->first('paper_number') }}
            </div>
        @endif
        
        {{-- Add Paper Form --}}
        <form action="{{ route('subjects.papers.store', $subject->id) }}" method="POST" class="flex gap-2 mb-6">
            @csrf
            <div class="flex items-center w-full border border-slate-300 rounded-lg overflow-hidden bg-slate-50">
                <span class="px-3 text-slate-500 font-medium border-r bg-slate-100 h-full flex items-center">Paper</span>
                <input type="number" name="paper_number" placeholder="1" min="1" class="w-full p-2 outline-none bg-transparent" required>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">Add</button>
        </form>

        {{-- Papers List --}}
        <div class="border rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Paper Name</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($subject->papers as $paper)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $paper->name }}</td>
                            <td class="px-4 py-3 text-right">
                                <form action="{{ route('subjects.papers.destroy', $paper->id) }}" method="POST" onsubmit="return confirm('Delete this paper?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline font-semibold">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-6 text-center text-slate-400 italic">No papers added yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection