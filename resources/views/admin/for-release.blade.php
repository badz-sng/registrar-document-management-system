@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">For Signature / Release</h1>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@foreach ($requests as $req)
<div class="bg-white shadow rounded-lg mb-6 p-4">
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg font-semibold">{{ $req->student->name ?? 'N/A' }}</h2>
        <span class="text-sm text-gray-500 capitalize">
            Status: <strong>{{ str_replace('_', ' ', $req->status) }}</strong>
        </span>
    </div>

    <table class="w-full text-sm border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 text-left">Document</th>
                <th class="p-2 text-center">Signed</th>
                <th class="p-2 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($req->documents as $doc)
            <tr class="border-t">
                <td class="p-2">{{ $doc->name }}</td>
                <td class="p-2 text-center">
                    @if ($doc->pivot->is_signed)
                        <span class="text-green-600 font-semibold">✔ Yes</span>
                    @else
                        <span class="text-red-500 font-semibold">✖ No</span>
                    @endif
                </td>
                <td class="p-2 text-center">
                    <form action="{{ route('admin.toggleSigned', [$req->id, $doc->id]) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                            class="px-3 py-1 text-sm rounded 
                                   {{ $doc->pivot->is_signed ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                            {{ $doc->pivot->is_signed ? 'Mark Unsigned' : 'Mark Signed' }}
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endforeach
@endsection
