@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Admin Overview</h1>

<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6 text-center">
    @foreach ($stats as $label => $data)
        <div class="bg-white p-4 rounded shadow hover:shadow-md transition-shadow duration-200">
            <h2 class="text-gray-600 capitalize">{{ str_replace('_', ' ', $label) }}</h2>
            <p class="text-3xl font-bold text-blue-600">{{ $data['value'] }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ $data['description'] }}</p>
        </div>
    @endforeach
</div>


<h2 class="text-xl font-semibold mb-2">User Management</h2>
<table class="w-full bg-white rounded shadow">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2 text-left">Name</th>
            <th class="p-2 text-left">Role</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr class="border-t">
                <td class="p-2">{{ $user->name }}</td>
                <td class="p-2 capitalize">{{ $user->role }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
