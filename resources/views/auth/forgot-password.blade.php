<form method="POST" action="/forgot-password" class="space-y-4">
    @csrf
    <input type="email" name="email" placeholder="Enter your registered email" class="w-full p-3 border rounded-lg" required>
    <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-lg font-bold hover:bg-blue-700">Send Reset Code</button>
</form>