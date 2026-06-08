<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Email | JamaJoint</title>
     @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen px-4">
    <div class="w-full max-w-sm p-8 bg-white shadow-xl rounded-2xl border border-gray-100 text-center">
        <h2 class="text-2xl font-bold mb-4">Verify Your Email</h2>
        <p class="text-gray-500 mb-6 text-sm">We sent a 6-digit code to your email. Please enter it to activate your account.</p>
        <form method="POST" action="/verify-register-otp" class="space-y-4">
            @csrf
            <input type="text" name="otp" placeholder="6-digit code" class="w-full p-3 border rounded-lg text-center text-xl tracking-widest" required maxlength="6">
            <button type="submit" class="w-full bg-green-500 text-white p-3 rounded-lg font-bold hover:bg-green-600">Activate Account</button>
        </form>
    </div>
</body>
</html>