<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FilePilot Login</title>
    @vite('resources/css/app.css')
</head>
<body x-data="{ dark: false }" :class="dark ? 'bg-gray-900 text-white' : 'bg-gradient-to-br from-blue-50 to-blue-100'">
    
    <button @click="dark = !dark"
        class="absolute top-5 right-5 p-2 rounded-lg text-gray-600 hover:bg-gray-200"
        title="Toggle dark mode">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path x-show="!dark" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 3v1m0 16v1m8.485-8.485h1M3.515 12.515h1M16.95 7.05l.707-.707M6.343 17.657l.707-.707M16.95 16.95l.707.707M6.343 6.343l.707.707M12 5a7 7 0 100 14a7 7 0 000-14z" />
            <path x-show="dark" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
        </svg>
    </button>

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 space-y-6">
        {{-- Logo --}}
        <div class="text-center">
            <h1 class="text-3xl font-bold text-blue-600">FilePilot</h1>
            <p class="text-gray-500 text-sm mt-2">Document Monitoring & Management System</p>
        </div>

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" id="email" required autofocus
                       class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('email')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required
                       class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('password')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            {{-- Remember me --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center space-x-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span>Remember me</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-blue-600 text-sm hover:underline">
                    Forgot Password?
                </a>
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition duration-200">
                Sign In
            </button>
        </form>

        {{-- Divider --}}
        <div class="flex items-center justify-center">
            <div class="border-t w-1/3 border-gray-200"></div>
            <span class="mx-2 text-xs text-gray-400">or</span>
            <div class="border-t w-1/3 border-gray-200"></div>
        </div>

        {{-- Register link --}}
        <p class="text-center text-sm text-gray-600">
            Don’t have an account?
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register</a>
        </p>

        {{-- Footer --}}
        <p class="text-center text-xs text-gray-400 mt-6">
            © {{ date('Y') }} FilePilot. All rights reserved.
        </p>
    </div>

</body>
</html>
