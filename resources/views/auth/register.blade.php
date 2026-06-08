<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | JamaJoint</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-sm p-8 bg-white shadow-xl rounded-2xl border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-600">JamaJoint</h1>
            <p class="text-gray-500 mt-2">Create your teacher portal account</p>
        </div>

        <form method="POST" action="/register" class="space-y-4" x-data="registerForm()">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. John Doe" 
                       class="w-full p-3 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g. name@gmail.com" 
                       class="w-full p-3 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="number" name="phone" value="{{ old('phone') }}" placeholder="0712 345 678" 
                       class="w-full p-3 border {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">County</label>
                <select x-ref="countySelect" name="county_id" class="w-full p-3 border {{ $errors->has('county_id') ? 'border-red-500' : 'border-gray-300' }} rounded-lg">
                    <option value="">Select your county</option>
                    @foreach($counties as $county)
                        <option value="{{ $county->id }}" {{ old('county_id') == $county->id ? 'selected' : '' }}>{{ $county->name }}</option>
                    @endforeach
                </select>
                @error('county_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">School</label>
                <select x-ref="schoolSelect" name="school_id" class="w-full p-3 border {{ $errors->has('school_id') ? 'border-red-500' : 'border-gray-300' }} rounded-lg">
                    <option value="">Select your school</option>
                </select>
                @error('school_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="relative" x-data="{ show: false }">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input :type="show ? 'text' : 'password'" name="password" placeholder="Create a secure password" 
                       class="w-full p-3 border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                <button type="button" @click="show = !show" class="absolute right-3 top-9 text-sm text-blue-600 font-semibold">
                    <span x-text="show ? 'Hide' : 'Show'"></span>
                </button>
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" placeholder="Confirm your password" 
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-lg font-bold hover:bg-blue-700 transition">Register Now</button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            Already have an account? <a href="/login" class="text-blue-600 font-bold">Sign In</a>
        </p>
    </div>

    <script>
        function registerForm() {
            return {
                init() {
                    $(this.$refs.countySelect).select2({ width: '100%' });
                    $(this.$refs.schoolSelect).select2({ width: '100%' });

                    // Re-populate schools if validation failed
                    if (this.$refs.countySelect.value) {
                        this.fetchSchools(this.$refs.countySelect.value);
                    }

                    $(this.$refs.countySelect).on('change', (e) => {
                        this.fetchSchools(e.target.value);
                    });
                },
                fetchSchools(countyId) {
                    if (!countyId) return;
                    fetch(`/get-schools/${countyId}`)
                        .then(res => res.json())
                        .then(data => {
                            let oldSchool = "{{ old('school_id') }}";
                            $(this.$refs.schoolSelect).empty().append('<option value="">Select your school</option>');
                            data.forEach(school => {
                                let selected = (school.id == oldSchool) ? 'selected' : '';
                                $(this.$refs.schoolSelect).append(new Option(school.name, school.id, false, selected));
                            });
                            $(this.$refs.schoolSelect).trigger('change');
                        });
                }
            }
        }
    </script>
</body>
</html>