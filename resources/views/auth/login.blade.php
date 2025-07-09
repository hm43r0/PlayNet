
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0f0f0f] min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-[#181818] rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-white mb-6 text-center">Sign in to your account</h2>
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-200 mb-1">Email address</label>
                <input id="email" name="email" type="email" autocomplete="email" required autofocus value="{{ old('email') }}" class="block w-full rounded-lg bg-[#232323] border border-[#333] text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-200 mb-1">Password</label>
                <div class="relative">
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-lg bg-[#232323] border border-[#333] text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                    <button type="button" onclick="togglePassword('password', this)" tabindex="-1" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 focus:outline-none">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" class="ml-2 block text-sm text-gray-200">Remember me</label>
            </div>
            <button type="submit" class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 rounded-lg text-white font-semibold">Login</button>
            <p class="text-center text-sm text-gray-400">Don't have an account? <a href="{{ route('register') }}" class="text-blue-400 hover:underline">Sign up here</a></p>
        </form>
    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        btn.querySelector('i').classList.remove('fa-eye');
        btn.querySelector('i').classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        btn.querySelector('i').classList.remove('fa-eye-slash');
        btn.querySelector('i').classList.add('fa-eye');
    }
}
</script>
</html>