@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Processor Dashboard</h1>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 text-red-800 p-2 rounded mb-4">{{ session('error') }}</div>
@endif

<table class="w-full bg-white rounded shadow">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2 text-left">Student Name</th>
            <th class="p-2">Requested Documents</th>
            <th class="p-2">Status</th>
            <th class="p-2">Action</th>
        </tr>
    </thead>
    <tbody>
    @forelse ($requests as $r)
        <tr class="border-t align-top">
            <td class="p-2">{{ $r->student->name ?? 'Unknown Student' }}</td>

            <td class="p-2">
                @php
                    $documents = $r->documentTypes;
                @endphp

                @if ($documents->isEmpty())
                    <span class="text-gray-500">No documents</span>
                @else
                    <ul class="space-y-1">
                        @foreach ($documents as $doc)
                            <li class="flex items-center space-x-2">
                                <form method="POST" action="{{ route('requests.documents.toggle', [$r->id, $doc->id]) }}">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1 rounded text-sm font-semibold
                                               {{ $doc->pivot->is_prepared ? 'bg-green-600 text-white' : 'bg-gray-300 text-black' }}">
                                        {{ $doc->pivot->is_prepared ? 'Prepared' : 'Mark as Prepared' }}
                                    </button>
                                </form>
                                <span>{{ $doc->name }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </td>

            <td class="p-2">
                <span class="font-semibold {{ $r->status === 'ready_for_verifying' ? 'text-green-700' : 'text-gray-700' }}">
                    {{ ucfirst($r->status) }}
                </span>
            </td>

            <td class="p-2 text-center">
                @if ($r->status !== 'ready_for_verifying')
                    <form method="POST" action="{{ route('processor.markPrepared', $r->id) }}">
                        @csrf
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                            Mark as Ready for Verifying
                        </button>
                    </form>
                @else
                    <span class="text-green-700 font-semibold">Ready for Verifying</span>
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="p-4 text-center text-gray-500">
                No retrieved requests found.
            </td>
        </tr>
    @endforelse
</tbody>

</table>
@endsection
