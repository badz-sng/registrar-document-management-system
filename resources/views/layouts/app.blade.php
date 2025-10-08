<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst(auth()->user()->role ?? 'Dashboard') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.0/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen">
    <nav class="bg-white shadow p-4 flex justify-between items-center">
        <div class="font-bold text-xl">
            {{ ucfirst(auth()->user()->role) }} Dashboard
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                Logout
            </button>
        </form>
    </nav>

    <main class="p-6">
        @yield('content')
    </main>
</body>
</html>
