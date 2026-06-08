@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="userManager(@js($users), @js($schools))" x-cloak>

    {{-- HEADER & SEARCH --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
        <h1 class="text-2xl font-bold text-slate-900">Manage System Users</h1>
        <div class="mt-4 flex flex-col md:flex-row md:items-center gap-6">
            <input type="text" x-model="search" placeholder="Search by name, email, phone, or school..." 
                   class="w-full md:w-80 px-4 py-2 border border-slate-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500/20">
            
            <label class="flex items-center gap-2 cursor-pointer bg-slate-100 px-4 py-2 rounded-lg hover:bg-slate-200 transition">
                <input type="checkbox" x-model="onlyOnline" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                <span class="text-sm font-semibold text-slate-700">Show Online users</span>
            </label>
        </div>
    </div>

    {{-- USERS TABLE --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 border-b uppercase text-[10px] text-slate-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-4">#</th>
                        <th class="px-6 py-4 cursor-pointer hover:text-blue-600" @click="sortBy('name')">Name ↕</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Phone</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 cursor-pointer hover:text-blue-600" @click="sortBy('last_login_at')">Last Login ↕</th>
                        <th class="px-6 py-4">School</th>
                        <th class="px-6 py-4">County</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 whitespace-nowrap">
                    <template x-for="(user, index) in paginatedUsers" :key="user.id">
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 text-slate-500" x-text="(page * 50) + index + 1"></td>
                            <td class="px-6 py-4 font-bold text-slate-900" x-text="user.name"></td>
                            <td class="px-6 py-4 text-slate-600" x-text="user.email"></td>
                            <td class="px-6 py-4 text-slate-600" x-text="user.phone || 'N/A'"></td>
                            
                            <td class="px-6 py-4">
                                <div x-show="user.is_online" class="flex w-6" title="Online"><i class='bx bxs-circle text-green-500 text-[12px] animate-pulse'></i></div>
                                <div x-show="!user.is_online" class="flex w-6" title="Offline"><i class='bx bxs-circle text-slate-300 text-[12px]'></i></div>
                            </td>
                            
                            <td class="px-6 py-4 text-xs text-slate-500" x-text="user.last_login_at ? new Date(user.last_login_at).toLocaleDateString() : 'Never'"></td>
                            
                            <td class="px-6 py-4">
                                <div x-show="!user.isEditingSchool" class="flex items-center gap-2">
                                    <span class="font-semibold text-slate-700" x-text="user.school?.name || 'N/A'"></span>
                                    <button @click="user.isEditingSchool = true; $nextTick(() => initSelect2(user))" class="text-blue-500 hover:text-blue-700">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                </div>
                                <div x-show="user.isEditingSchool" class="w-48">
                                    <select :id="'select-school-' + user.id" class="school-select2 w-full">
                                        <template x-for="school in schools" :key="school.id">
                                            <option :value="school.id" :selected="user.school_id == school.id" x-text="school.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-slate-500" x-text="user.school?.county?.name || 'N/A'"></td>
                            
                            <td class="px-6 py-4">
                                <select @change="updateRole(user.id, $event.target.value)" class="border-slate-300 rounded text-xs p-1">
                                    <option value="teacher" :selected="user.role == 'teacher'">Teacher</option>
                                    <option value="exam_admin" :selected="user.role == 'exam_admin'">Exam Admin</option>
                                    <option value="super_admin" :selected="user.role == 'super_admin'">Super Admin</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button @click="deleteUser(user.id)" class="text-red-600 hover:underline text-xs">Delete</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t flex justify-between items-center text-slate-600">
            <span class="text-xs font-bold uppercase">Page <span x-text="page + 1"></span> of <span x-text="maxPage + 1"></span></span>
            <div class="space-x-2">
                <button @click="page--" :disabled="page === 0" class="px-3 py-1 border rounded disabled:opacity-50">Prev</button>
                <button @click="page++" :disabled="page >= maxPage" class="px-3 py-1 border rounded disabled:opacity-50">Next</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function userManager(initialUsers, initialSchools) {
    return {
        users: initialUsers.map(u => ({...u, isEditingSchool: false})),
        schools: initialSchools,
        search: '',
        onlyOnline: false,
        page: 0,
        sortCol: 'name',
        sortAsc: true,

        get filteredUsers() {
            return this.users.filter(u => {
                const matchesSearch = u.name.toLowerCase().includes(this.search.toLowerCase()) ||
                                      u.email.toLowerCase().includes(this.search.toLowerCase()) ||
                                      (u.phone && u.phone.includes(this.search)) ||
                                      (u.school?.name && u.school.name.toLowerCase().includes(this.search.toLowerCase()));
                const matchesOnline = !this.onlyOnline || u.is_online;
                return matchesSearch && matchesOnline;
            }).sort((a, b) => {
                let modifier = this.sortAsc ? 1 : -1;
                let valA = a[this.sortCol] || '';
                let valB = b[this.sortCol] || '';
                
                // Handle date comparison for last_login_at
                if (this.sortCol === 'last_login_at') {
                    return (new Date(valA || 0) - new Date(valB || 0)) * modifier;
                }
                return valA.toString().localeCompare(valB.toString()) * modifier;
            });
        },
        get paginatedUsers() { return this.filteredUsers.slice(this.page * 50, (this.page + 1) * 50); },
        get maxPage() { return Math.max(0, Math.ceil(this.filteredUsers.length / 50) - 1); },
        sortBy(col) { if (this.sortCol === col) this.sortAsc = !this.sortAsc; this.sortCol = col; },

        initSelect2(user) {
            $(`#select-school-${user.id}`).select2({ width: '100%' }).on('change', (e) => {
                this.saveSchool(user, e.target.value);
            });
        },

        saveSchool(user, schoolId) {
            fetch(`/users/${user.id}/update-school`, { 
                method: 'PATCH', 
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json'},
                body: JSON.stringify({ school_id: schoolId })
            }).then(response => { if(response.ok) { toastr.success('School updated'); location.reload(); } });
        },
        
        updateRole(id, role) {
            fetch(`/users/${id}/update-role`, { 
                method: 'PATCH', 
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json'},
                body: JSON.stringify({role})
            }).then(() => toastr.success('Role updated'));
        },
        deleteUser(id) {
            if(confirm('Are you sure you want to delete this user?')) {
                fetch(`/users/${id}`, { method: 'DELETE', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} })
                .then(() => location.reload());
            }
        }
    }
}
</script>
@endpush