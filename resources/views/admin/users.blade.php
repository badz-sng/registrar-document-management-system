@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">User Management</h1>

  @if(session('success'))
    <div class="bg-green-100 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="bg-red-100 text-red-800 p-2 rounded mb-4">{{ session('error') }}</div>
  @endif
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
                                data-user-disabled="{{ $user->disabled ? '1' : '0' }}"
                                data-user-last-login-time="{{ optional($user->latestLogin)->created_at ? \Carbon\Carbon::parse($user->latestLogin->created_at)->setTimezone('Asia/Manila')->format('Y-m-d H:i:s') . ' PHT' : '' }}"
                                data-user-last-login-ip="{{ optional($user->latestLogin)->ip_address ?? '' }}"
                                @click="$dispatch('user-selected', { id: $event.currentTarget.dataset.userId, name: $event.currentTarget.dataset.userName, email: $event.currentTarget.dataset.userEmail, role: $event.currentTarget.dataset.userRole, disabled: $event.currentTarget.dataset.userDisabled, lastLoginTime: $event.currentTarget.dataset.userLastLoginTime, lastLoginIp: $event.currentTarget.dataset.userLastLoginIp }); $dispatch('open-modal','user-details')"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">View</button>
                        @if(Route::has('admin.users.edit'))
                          <a href="{{ route('admin.users.edit', $user->id) }}" class="block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">Edit</a>
                        @else
                          <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">Edit</a>
                        @endif
                        @if(auth()->id() === $user->id)
                          <button type="button" class="w-full text-left px-4 py-2 text-sm text-gray-500" role="menuitem" disabled>Cannot disable own account</button>
                        @else
                          <form method="POST" action="{{ route('admin.users.toggleDisabled', $user->id) }}" onsubmit="return confirm('{{ $user->disabled ? "Enable this account?" : "Disable this account? (User will not be able to login)" }}')" class="block">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ $user->disabled ? 'text-green-600' : 'text-red-600' }}" role="menuitem">{{ $user->disabled ? 'Enable' : 'Disable' }}</button>
                          </form>
                        @endif
                      </div>
                    </div>
                  </td>
              </tr>
          @endforeach
      </tbody>
  </table>

  <x-modal name="user-details" focusable>
    <div x-data="{ selected: null, loginHistory: [], loadingHistory: false, async fetchHistory(){ if(!selected || !selected.id) return; this.loadingHistory = true; this.loginHistory = []; try{ const res = await fetch(`/admin/users/${selected.id}/login-history`); if(!res.ok){ throw new Error('Failed to fetch'); } const json = await res.json(); this.loginHistory = json.data || []; }catch(e){ console.error(e); alert('Unable to load login history'); } finally{ this.loadingHistory = false } } }" x-on:user-selected.window="selected = $event.detail" x-on:modal-opened.window="if($event.detail && $event.detail.name === 'user-details'){ if(selected && selected.id){ fetchHistory() } else { setTimeout(()=>{ if(selected && selected.id) fetchHistory() }, 50) } }">
      <p><strong>Name:</strong> <span x-text="selected.name"></span></p>
      <p class="mt-2"><strong>Email:</strong> <span x-text="selected.email"></span></p>
      <p class="mt-2"><strong>Role:</strong> <span x-text="selected.role"></span></p>
      <p class="mt-2"><strong>Status:</strong> <span x-text="(selected.disabled && selected.disabled === '1') ? 'Disabled' : (selected.disabled ? 'Active' : 'Disabled')"></span></p>
      <p class="mt-2 text-sm text-gray-500"><strong>ID:</strong> <span x-text="selected.id"></span></p>

      <hr class="my-3">

      <h4 class="text-sm font-medium text-gray-700">Last Login</h4>
      <p class="mt-1 text-sm"><strong>Time:</strong> <span x-text="selected.lastLoginTime ? selected.lastLoginTime : 'No record'"></span></p>
      <p class="mt-1 text-sm"><strong>IP:</strong> <span x-text="selected.lastLoginIp ? selected.lastLoginIp : '-'"></span></p>

      <div class="mt-4">
        <button type="button" @click="fetchHistory()" :disabled="loadingHistory || !selected || !selected.id" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50"> 
          <span x-show="!loadingHistory">View full login history</span>
          <span x-show="loadingHistory">Loading...</span>
        </button>
      </div>

      <template x-if="loginHistory.length">
        <div class="mt-3 bg-gray-50 p-3 rounded">
          <h5 class="text-sm font-medium mb-2">Login History</h5>
          <table class="w-full text-sm">
            <thead class="text-left text-gray-600 text-xs">
              <tr>
                <th class="pb-1">Time</th>
                <th class="pb-1">IP</th>
              </tr>
            </thead>
            <tbody>
              <template x-for="item in loginHistory" :key="item.id">
                <tr class="border-t">
                  <td class="pt-2" x-text="item.created_at"></td>
                  <td class="pt-2" x-text="item.ip_address"></td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </template>

      <template x-if="!loadingHistory && loginHistory.length === 0">
        <p class="mt-2 text-sm text-gray-500">No additional login records found.</p>
      </template>
    </div>
  </x-modal>
</div>
@endsection
