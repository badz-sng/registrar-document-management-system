@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">{{ ucfirst(auth()->user()->role ?? 'Dashboard') }} Account Settings</h2>

<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6 text-center">
<div class="mb-8">
    <form method="POST" action="{{ route('profile.update') }}" class="bg-white p-4 rounded shadow w-full max-w-md">
        <h3 class="text-lg font-semibold mb-2">Change Password</h3>
        @csrf
        @method('PATCH')
        <div class="mb-4">
            <label class="block text-gray-700">New Password</label>
            <input type="password" name="password" class="border p-2 w-full rounded" required>
            @error('password')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Confirm Password</label>
            <input type="password" name="password_confirmation" class="border p-2 w-full rounded" required>
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Update Password</button>
    </form>
</div>
@if(auth()->user()->role === 'admin')
    <div class="mb-8">
        <form method="POST" action="{{ route('admin.register') }}" class="bg-white p-4 rounded shadow w-full max-w-md">
            <h3 class="text-lg font-semibold mb-2">Register New Account</h3>
            @csrf
            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif


            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Name</label>
                <input id="name" type="text" name="name"
                       class="border p-2 w-full rounded"
                       value="{{ old('name') }}" required autofocus autocomplete="name">
                @error('name')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700">Email</label>
                <input id="email" type="email" name="email"
                       class="border p-2 w-full rounded"
                       value="{{ old('email') }}" required autocomplete="username">
                @error('email')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Password</label>
                <input id="password" type="password" name="password"
                       class="border p-2 w-full rounded" required autocomplete="new-password">
                @error('password')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="border p-2 w-full rounded" required autocomplete="new-password">
                @error('password_confirmation')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label for="role" class="block text-gray-700">Select Role</label>
                <select name="role" id="role" required
                        class="border p-2 w-full rounded">
                    @foreach(\App\Models\User::ROLES as $r)
                        <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Register
            </button>
        </form>
    </div>
@endif


</div>


<div>
    <h3 class="text-lg font-semibold mb-2">Login History</h3>
    <table class="w-full bg-white rounded shadow">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 text-left">Login Time</th>
                <th class="p-2 text-left">IP Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loginHistory as $login)
                <tr class="border-t">
                    <td class="p-2">{{ \Carbon\Carbon::parse($login->created_at)->setTimezone('Asia/Manila')->format('Y-m-d H:i:s') }} PHT</td>
                    <td class="p-2">{{ $login->ip_address }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
