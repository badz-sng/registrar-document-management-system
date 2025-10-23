@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Verifier Dashboard</h1>

<table class="w-full bg-white rounded shadow">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2 text-left">Student</th>
            <th class="p-2 text-left">Documents</th>
            <th class="p-2 text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($requests as $r)
            <tr class="border-t">
                <td class="p-2">{{ $r->student->name ?? 'N/A' }}</td>
                <td class="p-2">
                    <ul>
                        @foreach ($r->documents as $doc)
                            <li class="flex items-center justify-between border-b py-1">
                                <span>{{ $doc->name }}</span>
                                <form method="POST" action="{{ route('verifier.toggleVerification', [$r->id, $doc->id]) }}">
                                    @csrf
                                    <button type="submit"
                                        class="px-2 py-1 rounded text-xs
                                            {{ $doc->pivot->is_verified ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-700' }}">
                                        {{ $doc->pivot->is_verified ? 'Verified' : 'Mark Verified' }}
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td class="p-2 text-center capitalize">{{ str_replace('_', ' ', $r->status) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
