@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-slate-800">Create New School</h2>
        <a href="{{ route('schools.index') }}" class="text-sm text-slate-500 hover:text-blue-600 transition">
            &larr; Back to List
        </a>
    </div>

    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        </script>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
        <form action="{{ route('schools.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">School Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" 
                           class="w-full mt-1 p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">School Type</label>
                    <select name="type" class="w-full mt-1 p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="mixed" {{ old('type') == 'mixed' ? 'selected' : '' }}>Mixed</option>
                        <option value="girls" {{ old('type') == 'girls' ? 'selected' : '' }}>Girls</option>
                        <option value="boys" {{ old('type') == 'boys' ? 'selected' : '' }}>Boys</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">County</label>
                    <select name="county_id" class="w-full mt-1 p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Select a County</option>
                        @foreach($counties as $county)
                            <option value="{{ $county->id }}" {{ old('county_id') == $county->id ? 'selected' : '' }}>
                                {{ $county->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Create School
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection