@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">User Management</h1>

  <table class="w-full bg-white rounded shadow">
      <thead class="bg-gray-100">
          <tr>
              <th class="p-2 text-left">Name</th>
              <th class="p-2 text-left">Email</th>
              <th class="p-2 text-left">Role</th>
              <th class="p-2 text-left">Actions</th>
          </tr>
      </thead>
      <tbody>
          @foreach ($users as $user)
              <tr class="border-t">
                  <td class="p-2">{{ $user->name }}</td>
                  <td class="p-2">{{ $user->email }}</td>
                  <td class="p-2 capitalize">{{ $user->role }}</td>
                  <td class="p-2">
                    <div x-data="{ open: false }" class="relative inline-block text-left" x-cloak>
                      <button
                        @click="open = !open"
                        @keydown.escape="open = false"
                        :aria-expanded="open.toString()"
                        aria-haspopup="true"
                        class="bg-blue-500 text-white px-2 py-1 rounded inline-flex items-center"
                      >
                        Options
                        <svg class="ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 011.08 1.04l-4.25 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" /></svg>
                      </button>

                      <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition
                        class="absolute right-0 mt-2 w-40 bg-white border rounded shadow z-10"
                        style="display: none;"
                        role="menu"
                        aria-orientation="vertical"
                        aria-labelledby="options-menu"
                      >
                        <button type="button"
                                data-user-id="{{ $user->id }}"
                                data-user-name="{{ e($user->name) }}"
                                data-user-email="{{ e($user->email) }}"
                                data-user-role="{{ e($user->role) }}"
                                data-user-last-login-time="{{ optional($user->latestLogin)->created_at ? \Carbon\Carbon::parse($user->latestLogin->created_at)->setTimezone('Asia/Manila')->format('Y-m-d H:i:s') . ' PHT' : '' }}"
                                data-user-last-login-ip="{{ optional($user->latestLogin)->ip_address ?? '' }}"
                                @click="$dispatch('open-modal','user-details'); $dispatch('user-selected', { id: $event.currentTarget.dataset.userId, name: $event.currentTarget.dataset.userName, email: $event.currentTarget.dataset.userEmail, role: $event.currentTarget.dataset.userRole, lastLoginTime: $event.currentTarget.dataset.userLastLoginTime, lastLoginIp: $event.currentTarget.dataset.userLastLoginIp })"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">View</button>
                        @if(Route::has('admin.users.edit'))
                          <a href="{{ route('admin.users.edit', $user->id) }}" class="block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">Edit</a>
                        @else
                          <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">Edit</a>
                        @endif
                        @if(Route::has('admin.users.destroy'))
                          <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Delete this user?')" class="block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 text-red-600" role="menuitem">Delete</button>
                          </form>
                        @else
                          <button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 text-red-600" role="menuitem" onclick="alert('Delete action not available')">Delete</button>
                        @endif
                      </div>
                    </div>
                  </td>
              </tr>
          @endforeach
      </tbody>
  </table>

  <x-modal name="user-details" focusable>
    <div x-data="{ selected: null }" x-on:user-selected.window="selected = $event.detail">
      <p><strong>Name:</strong> <span x-text="selected.name"></span></p>
      <p class="mt-2"><strong>Email:</strong> <span x-text="selected.email"></span></p>
      <p class="mt-2"><strong>Role:</strong> <span x-text="selected.role"></span></p>
      <p class="mt-2 text-sm text-gray-500"><strong>ID:</strong> <span x-text="selected.id"></span></p>

      <hr class="my-3">

      <h4 class="text-sm font-medium text-gray-700">Last Login</h4>
      <p class="mt-1 text-sm"><strong>Time:</strong> <span x-text="selected.lastLoginTime ? selected.lastLoginTime : 'No record'"></span></p>
      <p class="mt-1 text-sm"><strong>IP:</strong> <span x-text="selected.lastLoginIp ? selected.lastLoginIp : '-'"></span></p>
    </div>
  </x-modal>
</div>
@endsection
