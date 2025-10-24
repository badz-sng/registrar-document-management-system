@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">User Management</h1>

<table class="w-full bg-white rounded shadow">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2 text-left">Name</th>
            <th class="p-2 text-left">Email</th>
            <th class="p-2 text-left">Role</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr class="border-t">
                <td class="p-2">{{ $user->name }}</td>
                <td class="p-2">{{ $user->email }}</td>
                <td class="p-2 capitalize">{{ $user->role }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
