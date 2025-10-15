<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst(auth()->user()->role ?? 'Dashboard') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.0/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen">
    <nav class="bg-white shadow p-4 flex justify-between items-center">
        <a href="/">
        <div class="font-bold text-xl">
            {{ ucfirst(auth()->user()->role) }} Dashboard
        </div>
        </a>
        <div class="relative inline-block text-left">
            <!-- User Menu -->
            <button type="button" class="flex items-center px-4 py-2 bg-gray-200 rounded hover:bg-gray-300" id="user-menu-button" onclick="document.getElementById('user-menu').classList.toggle('hidden')">
                {{ Auth::user()->name }}
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="user-menu" class="absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg hidden z-10">
                <a href="/" class="block px-4 py-2 hover:bg-gray-100">Homepage</a>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100">Account</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 text-red-500">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="p-6">
        @yield('content')
    </main>
</body>
</html>
