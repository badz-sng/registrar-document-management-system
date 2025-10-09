@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Encode Student Request</h1>

@if(session('success'))
    <div class="bg-green-200 text-green-800 p-2 mb-4 rounded">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('requests.store') }}" class="bg-white p-4 rounded shadow mb-6">
    @csrf

    <div class="mb-2">
        <label class="block text-gray-600">Student Name</label>
        <input type="text" name="student_name" class="border p-2 w-full rounded" required>
    </div>

    <div class="mb-2">
        <label class="block text-gray-600">Document Type</label>
        <select name="document_type_id" class="border p-2 w-full rounded" required>
            <option value="">-- Select --</option>
            <option value="F-137">F-137</option>
            <option value="F-138">F-138</option>
            <option value="TOR">TOR</option>
            <option value="Transfer Credential">Transfer Credential</option>
            <option value="Good Moral Certificate">Good Moral Certificate</option>
            <option value="Diploma">Diploma</option>
            <option value="Certificate of Grades">Certificate of Grades</option>
            <option value="Certificate of Enrollment">Certificate of Enrollment</option>
            <option value="Certificate of Graduation">Certificate of Graduation</option>
            <option value="Honorable Dismissal">Honorable Dismissal</option>
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
                <td class="p-2">{{ $r->student->name ?? 'N/A' }}</td>
                <td class="p-2">{{ $r->documentType->name ?? 'N/A' }}</td>
                <td class="p-2">{{ $r->status }}</td>
                <td class="p-2">{{ \Carbon\Carbon::parse($r->estimated_release_date)->toFormattedDateString() }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
