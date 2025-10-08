<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Admin Dashboard</h1>
        <div>
            <span class="text-gray-700 font-medium">
                {{ $currentUser->name }} ({{ ucfirst($currentUser->role) }})
            </span>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="ml-3 bg-red-500 text-white px-3 py-1 rounded">Logout</button>
            </form>
        </div>
    </div>

    <div class="bg-white shadow rounded p-4">
        <h2 class="text-xl font-semibold mb-4">List of Users</h2>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Name</th>
                    <th class="border p-2">Role</th>
                    <th class="border p-2">Department</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="border p-2">{{ $user->id }}</td>
                    <td class="border p-2">{{ $user->name }}</td>
                    <td class="border p-2 capitalize">{{ $user->role }}</td>
                    <td class="border p-2">{{ $user->department }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
