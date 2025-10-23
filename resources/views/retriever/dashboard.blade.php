@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Retriever Dashboard</h1>

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
        @foreach ($requests as $r)
            <tr class="border-t">
                <td class="p-2">{{ $r->student->name ?? 'Unknown Student' }}</td>
                <td class="p-2">
                    @php
                        $documents = $r->documentTypes();
                    @endphp

                    @if ($documents->isEmpty())
                        <span class="text-gray-500">No documents</span>
                    @else
                        {{ $documents->pluck('name')->join(', ') }}
                    @endif
                </td>
                <td class="p-2">{{ ucfirst($r->status) }}</td>
                <td class="p-2">
                    @if (strtolower($r->status) === 'pending')
                        <form method="POST" action="{{ route('retriever.update.status', $r->id) }}">
                            @csrf
                            <button class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded">
                                Mark as Retrieved
                            </button>
                        </form>
                    @else
                        <span class="text-gray-600">{{ ucfirst($r->status) }}</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
