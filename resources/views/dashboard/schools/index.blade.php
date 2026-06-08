@extends('layouts.app')

@section('content')
    <div class="space-y-4">

        {{-- SUCCESS ALERT --}}
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium">
                        {{ session('success') }}
                    </span>
                    <button onclick="this.parentElement.parentElement.remove()"
                        class="text-green-700 hover:text-green-900 font-bold text-sm">
                        ✕
                    </button>
                </div>
            </div>
        @endif

        {{-- ERROR ALERT --}}
        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium">
                        {{ session('error') }}
                    </span>
                    <button onclick="this.parentElement.parentElement.remove()"
                        class="text-red-700 hover:text-red-900 font-bold text-sm">
                        ✕
                    </button>
                </div>
            </div>
        @endif

        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-slate-800">Manage Schools</h2>

            @can('manage-system')
                <a href="{{ route('schools.create') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                    Add New School
                </a>
            @endcan
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3 w-12">#</th>
                        <th class="px-4 py-3">School Name</th>
                        <th class="px-4 py-3">County</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 whitespace-nowrap">
                    @forelse($schools as $index => $school)
                        <tr class="hover:bg-slate-50 transition">

                            <td class="px-4 py-2 text-slate-500">
                                {{ $index + 1 }}
                            </td>

                            {{-- SHOW --}}
                            <td class="px-4 py-2">
                                <a href="{{ route('schools.show', ['school' => $school->id, 'slug' => $school->slug]) }}"
                                    class="font-medium text-blue-600 hover:underline">
                                    {{ $school->name }}
                                </a>
                            </td>

                            <td class="px-4 py-2 text-slate-600">
                                {{ $school->county->name ?? 'N/A' }}
                            </td>

                            <td class="px-4 py-2 capitalize text-slate-600">
                                {{ $school->type }}
                            </td>

                            <td class="px-4 py-2 text-right space-x-3">

                                {{-- EDIT --}}
                                <a href="{{ route('schools.edit', ['school' => $school->id, 'slug' => $school->slug]) }}"
                                    class="text-slate-400 hover:text-indigo-600 transition">
                                    <i class='bx bx-edit text-base'></i>
                                </a>

                                {{-- DELETE --}}
                                <form action="{{ route('schools.destroy', ['school' => $school->id, 'slug' => $school->slug]) }}"
                                    method="POST"
                                    class="inline-block delete-form">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="text-red-600 hover:text-red-700 transition font-medium">
                                        <i class='bx bx-trash text-base'></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                No schools found in the system.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- SWEETALERT DELETE --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This will delete the school.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Delete',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });

                });
            });

        });
    </script>
@endsection