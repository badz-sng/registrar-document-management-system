@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">Account Settings</h2>

<div class="mb-8">
    <h3 class="text-lg font-semibold mb-2">Change Password</h3>
    <form method="POST" action="{{ route('profile.update') }}" class="bg-white p-4 rounded shadow w-full max-w-md">
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
