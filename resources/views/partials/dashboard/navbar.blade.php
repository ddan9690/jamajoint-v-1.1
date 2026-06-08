<header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 md:px-6">
    <!-- Sidebar Toggle -->
    <button @click="sidebarOpen = !sidebarOpen" class="text-2xl text-slate-600 hover:text-blue-600 transition-colors">
        <i class='bx bx-menu'></i>
    </button>
    
    <!-- User Profile Dropdown -->
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center gap-2 text-sm font-medium text-slate-700 hover:text-blue-700 transition-colors">
            <!-- Simplified User Avatar -->
            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center font-bold text-blue-700 uppercase">
                <i class='bx bx-user'></i>
            </div>
            <span>{{ Auth::user()->name }}</span>
            <i :class="open ? 'bx-chevron-up' : 'bx-chevron-down'" class='bx text-lg'></i>
        </button>

        <!-- Dropdown Menu -->
        <div x-show="open" 
             x-cloak
             @click.away="open = false" 
             class="absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-lg shadow-xl py-2 z-50">
            <a href="#" class="block px-4 py-2 hover:bg-slate-50 text-slate-700">My Profile</a>
            <a href="#" class="block px-4 py-2 hover:bg-slate-50 text-slate-700">Change Password</a>
            
            <div class="border-t border-slate-100 my-1"></div>

            <!-- Logout Trigger -->
            <button onclick="confirmLogout()" 
                    class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 transition-colors">
                Logout
            </button>
        </div>
    </div>
</header>

<!-- Self-Contained Logout Logic -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Ready to leave?',
            text: "You will be logged out of JamaJoint.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1e40af',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>