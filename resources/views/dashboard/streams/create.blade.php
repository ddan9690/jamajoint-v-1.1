@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <h2 class="text-xl font-bold text-slate-800">Add New Stream to {{ $form->name }}</h2>

    <form action="{{ route('forms.streams.store', [$school->id, $school->slug, $form->id]) }}" method="POST" class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700">Stream Name</label>
                <input type="text" name="name" required class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('forms.streams.index', [$school->id, $school->slug, $form->id]) }}" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">Save Stream</button>
            </div>
        </div>
    </form>
</div>
@endsection