<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password | JamaJoint</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen px-4">

    <div class="w-full max-w-sm bg-white p-8 shadow-xl rounded-2xl border border-gray-100" x-data="{ show: false }">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Set New Password</h2>
            <p class="text-gray-500 text-sm mt-2">Enter a new secure password for your account.</p>
        </div>

        <form method="POST" action="/reset-password" class="space-y-4">
            @csrf
            
            <input type="hidden" name="token" value="{{ $token ?? '' }}">

            <div class="relative">
                <input :type="show ? 'text' : 'password'" 
                       name="password" 
                       placeholder="New Password" 
                       class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" 
                       required>
                <button type="button" 
                        @click="show = !show" 
                        class="absolute right-3 top-3 text-sm text-blue-600 font-semibold" 
                        x-text="show ? 'Hide' : 'Show'"></button>
            </div>

            <input type="password" 
                   name="password_confirmation" 
                   placeholder="Confirm New Password" 
                   class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" 
                   required>

            <button type="submit" 
                    class="w-full bg-blue-600 text-white p-3 rounded-lg font-bold hover:bg-blue-700 transition">
                Update Password
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            <a href="/login" class="text-blue-600 font-bold hover:underline">Back to Login</a>
        </div>
    </div>

</body>
</html>