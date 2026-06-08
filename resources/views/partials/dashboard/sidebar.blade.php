<aside x-show="sidebarOpen" x-cloak @click.away="if (window.innerWidth < 768) sidebarOpen = false"
    class="fixed md:static inset-y-0 left-0 z-50 w-64 bg-blue-900 text-white transition-all duration-300 shadow-xl flex flex-col">

    <div class="flex items-center justify-between p-6 border-b border-blue-800 shrink-0">
        <span class="font-black text-2xl tracking-tighter">JamaJoint</span>
        <button @click="sidebarOpen = false" class="md:hidden text-2xl"><i class='bx bx-x'></i></button>
    </div>

    <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-1 custom-scrollbar">
        <a href="{{ route('dashboard') }}" class="flex items-center p-3 rounded-lg hover:bg-blue-800 {{ request()->routeIs('dashboard') ? 'bg-blue-800' : '' }}">
            <i class='bx bxs-dashboard'></i> <span class="ml-3">Dashboard</span>
        </a>

        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-blue-800"><i class='bx bx-edit-alt'></i>
            <span class="ml-3">Mark Entry</span></a>
        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-blue-800"><i class='bx bx-list-check'></i>
            <span class="ml-3">Exam Results</span></a>

        @can('manage-exams')
            <div class="pt-4 pb-2 px-3 text-[10px] uppercase font-bold text-blue-400 tracking-wider">Exam Admin</div>
            
            <a href="{{ route('exams.index') }}" 
               class="flex items-center p-3 rounded-lg hover:bg-blue-800 {{ request()->routeIs('exams.*') ? 'bg-blue-800' : '' }}">
               <i class='bx bx-file-find'></i> <span class="ml-3">Exams</span>
            </a>
            
            <a href="{{ route('schools.index') }}"
                class="flex items-center p-3 rounded-lg hover:bg-blue-800 {{ request()->routeIs('schools.*') ? 'bg-blue-800' : '' }}">
                <i class='bx bx-buildings'></i> <span class="ml-3">Schools</span>
            </a>
        @endcan

        @can('manage-system')
            <div class="pt-4 pb-2 px-3 text-[10px] uppercase font-bold text-blue-400 tracking-wider">System Management</div>
            
            <a href="{{ route('users.index') }}" 
                class="flex items-center p-3 rounded-lg hover:bg-blue-800 {{ request()->routeIs('users.*') ? 'bg-blue-800' : '' }}">
                <i class='bx bx-group'></i> <span class="ml-3">Users</span>
            </a>
            
            <a href="#" class="flex items-center p-3 rounded-lg hover:bg-blue-800"><i class='bx bx-calendar'></i>
                <span class="ml-3">Academic Years</span></a>
            
            <a href="{{ route('subjects.index') }}"
                class="flex items-center p-3 rounded-lg hover:bg-blue-800 {{ request()->routeIs('subjects.*') ? 'bg-blue-800' : '' }}">
                <i class='bx bx-book'></i>
                <span class="ml-3">Subjects</span>
            </a>
            
            <a href="{{ route('grading-systems.index') }}"
                class="flex items-center p-3 rounded-lg hover:bg-blue-800 {{ request()->routeIs('grading-systems.*') ? 'bg-blue-800' : '' }}">
                <i class='bx bx-check-double'></i>
                <span class="ml-3">Grading Systems</span>
            </a>
            
            <a href="#" class="flex items-center p-3 rounded-lg hover:bg-blue-800"><i class='bx bx-chart'></i> 
                <span class="ml-3">Reports</span>
            </a>
        @endcan
    </nav>

    <div class="p-4 border-t border-blue-800 bg-blue-900 shrink-0">
        <button onclick="confirmLogout()"
            class="w-full flex items-center p-3 text-red-300 hover:text-white rounded-lg"><i class='bx bx-log-out'></i>
            <span class="ml-3">Logout</span></button>
    </div>
</aside>