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
<div class="flex items-center justify-center">
    <div class="border-t w-1/3 border-gray-200"></div>
        <span class="mx-2 text-xs text-gray-400">Change status of Users and Requests</span>
    <div class="border-t w-1/3 border-gray-200"></div>
</div>
{{-- New Section: Buttons for dedicated pages --}}
<div class="mt-6 flex gap-4">
    <a href="{{ route('admin.users.index') }}" 
       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow">
        Manage Users
    </a>

    <a href="{{ route('admin.forRelease') }}" 
       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow">
        For Signature / Release
    </a>
</div>
    <br>
<div class="flex items-center justify-center">
    <div class="border-t w-1/3 border-gray-200"></div>
        <span class="mx-2 text-xs text-gray-400">Overview of System Users and Roles</span>
    <div class="border-t w-1/3 border-gray-200"></div>
</div>
<h2 class="text-xl font-semibold mb-2">System Users</h2>
<table class="w-full bg-white rounded shadow mb-8">
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
