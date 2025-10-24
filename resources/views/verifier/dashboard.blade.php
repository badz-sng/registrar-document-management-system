@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Documents for Verification</h1>

<table class="w-full bg-white rounded shadow">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2 text-left">Student</th>
            <th class="p-2 text-left">Documents</th>
            <th class="p-2 text-left">Action and Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($requests as $req)
            <tr class="border-t">
                <td class="p-2">{{ $req->student->name ?? 'N/A' }}</td>
                <td class="p-2">
                    <ul class="space-y-1">
                        @foreach ($req->documentTypes as $doc)
                            <li class="flex items-center justify-between">
                                <span>{{ $doc->name }}</span>
                                <form method="POST" action="{{ route('verifier.toggle', [$req->id, $doc->id]) }}">
                                    @csrf
                                    <button 
                                        type="submit" 
                                        class="px-2 py-1 text-xs rounded 
                                        {{ $doc->pivot->is_verified ? 'bg-green-500 text-white' : 'bg-gray-300 hover:bg-green-400' }}">
                                        {{ $doc->pivot->is_verified ? 'Verified' : 'Mark as Verified' }}
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td class="p-2 capitalize">{{ str_replace('_', ' ', $req->status) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
