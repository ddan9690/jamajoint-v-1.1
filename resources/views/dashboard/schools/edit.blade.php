@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-slate-800">Edit School: {{ $school->name }}</h2>
        <a href="{{ route('schools.index') }}" class="text-sm text-slate-500 hover:text-blue-600 transition">
            &larr; Back to List
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
        <form action="{{ route('schools.update', ['school' => $school->slug, 'slug' => $school->slug]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">School Name</label>
                    <input type="text" name="name" value="{{ old('name', $school->name) }}" 
                           class="w-full mt-1 p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">School Type</label>
                    <select name="type" class="w-full mt-1 p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="mixed" {{ $school->type == 'mixed' ? 'selected' : '' }}>Mixed</option>
                        <option value="girls" {{ $school->type == 'girls' ? 'selected' : '' }}>Girls</option>
                        <option value="boys" {{ $school->type == 'boys' ? 'selected' : '' }}>Boys</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">County</label>
                    <select name="county_id" class="w-full mt-1 p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        @foreach($counties as $county)
                            <option value="{{ $county->id }}" {{ $school->county_id == $county->id ? 'selected' : '' }}>
                                {{ $county->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                           {{ $school->is_active ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                    <label for="is_active" class="text-sm font-medium text-slate-700">School is currently active</label>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Update School Details
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection