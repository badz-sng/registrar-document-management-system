<!DOCTYPE html>
<html lang="en" id="main-html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FilePilot Login</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 text-gray-900">
    <!-- Dark mode toggle removed -->

    <div class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 space-y-6">
        {{-- Logo --}}
        <div class="text-center">
            <h1 class="text-3xl font-bold text-blue-600">FilePilot</h1>
            <p class="text-gray-500 text-sm mt-2">Document Monitoring & Management System</p>
        </div>

        {{-- Login Form --}}
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-2 rounded mb-2">{{ session('error') }}</div>
    @endif
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
                Login
            </button>
        </form>

        <div class="flex items-center justify-center">
            <div class="border-t w-1/3 border-gray-200"></div>
            <span class="mx-2 text-xs text-gray-400">Developed by</span>
            <div class="border-t w-1/3 border-gray-200"></div>
        </div>
<p class="text-center text-xs text-gray-400 mt-6 flex items-center justify-center gap-2">
    <img src="{{ asset('logos/github-mark.svg') }}" alt="GitHub Logo" class="w-4 h-4 inline-block">
    <a href="https://github.com/badz-sng" class="text-gray-400 hover:underline">Emmanuel Sunga</a>
</p>

        {{-- Footer --}}
    <p class="text-center text-xs text-gray-400 mt-6">
            © {{ date('Y') }} FilePilot. All rights reserved.
        </p>

    </div>
</body>
</html>
