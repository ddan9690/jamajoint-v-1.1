<form method="POST" action="/verify-reset-otp" class="space-y-4">
    @csrf
    <input type="text" name="otp" placeholder="Enter 6-digit reset code" class="w-full p-3 border rounded-lg text-center" required>
    <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-lg font-bold hover:bg-blue-700">Verify & Reset</button>
</form>