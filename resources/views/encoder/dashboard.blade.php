@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Encode Student Request</h1>

@if(session('success'))
    <div class="bg-green-200 text-green-800 p-2 mb-4 rounded">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('requests.store') }}" class="bg-white p-4 rounded shadow mb-6">
    @csrf

    <div class="mb-2">
        <label class="block text-gray-600">Student</label>
        <select name="student_id" class="border p-2 w-full rounded" required>
            <option value="">-- Select Student --</option>
            @foreach ($students as $student)
                <option value="{{ $student->id }}">{{ $student->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-2">
        <label class="block text-gray-600">Document Type</label>
        <select name="document_type_id" class="border p-2 w-full rounded" required>
            <option value="">-- Select Document Type --</option>
            @foreach ($documentTypes as $doc)
                <option value="{{ $doc->id }}">{{ $doc->name }}</option>
            @endforeach
        </select>
    </div>

    <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">Encode</button>
</form>

<h2 class="text-xl font-semibold mb-2">My Encoded Requests</h2>
<table class="w-full bg-white rounded shadow">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2 text-left">Student</th>
            <th class="p-2">Document</th>
            <th class="p-2">Status</th>
            <th class="p-2">Release Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($requests as $r)
            <tr class="border-t">
                <td class="p-2">{{ $r->student_name }}</td>
                <td class="p-2">{{ $r->document_type }}</td>
                <td class="p-2">{{ $r->status }}</td>
                <td class="p-2">{{ \Carbon\Carbon::parse($r->release_date)->toFormattedDateString() }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
