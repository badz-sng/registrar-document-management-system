@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded shadow p-6">
    <h2 class="text-xl font-bold mb-4">Edit User</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
        @csrf
        @method('PATCH')

        <div class="mb-4">
            <label class="block text-gray-700">Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="border p-2 w-full rounded" required>
            @error('name')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="border p-2 w-full rounded" required>
            @error('email')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Role</label>
            <select name="role" class="border p-2 w-full rounded" required>
                @foreach(\App\Models\User::ROLES as $r)
                    <option value="{{ $r }}" {{ old('role', $user->role) === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
            @error('role')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="flex gap-2 justify-end">
            <a href="{{ route('admin.users.index') }}" class="px-3 py-2 border rounded">Cancel</a>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
        </div>
    </form>
</div>
@endsection