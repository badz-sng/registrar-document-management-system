@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Encode Student Request</h1>

@if(session('success'))
    <div class="bg-green-200 text-green-800 p-2 mb-4 rounded">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('requests.store') }}" class="bg-white p-4 rounded shadow mb-6">
    @csrf

    <div class="mb-2">
        <label class="block text-gray-600">Student Number</label>
        <input type="text" name="student_no" class="border p-2 w-full rounded" required value="{{ old('student_no') }}">
        @error('student_no')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-2">
        <label class="block text-gray-600">Student Name</label>
        <input type="text" name="name" class="border p-2 w-full rounded" required value="{{ old('name') }}">
        @error('name')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-2">
        <label class="block text-gray-600">Course</label>
        <input type="text" name="course" class="border p-2 w-full rounded" required value="{{ old('course') }}">
        @error('course')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-2">
        <label class="block text-gray-600">Year Level</label>
        <input type="text" name="year_level" class="border p-2 w-full rounded" required value="{{ old('year_level') }}">
        @error('year_level')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-2">
        <label class="block text-gray-600">Address</label>
        <input type="text" name="address" class="border p-2 w-full rounded" required value="{{ old('address') }}">
        @error('address')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-2">
        <label class="block text-gray-600">Contact Number</label>
        <input type="text" name="contact_number" class="border p-2 w-full rounded" required value="{{ old('contact_number') }}">
        @error('contact_number')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-2">
        <label class="block text-gray-600">Email</label>
        <input type="email" name="email" class="border p-2 w-full rounded" required value="{{ old('email') }}">
        @error('email')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-2">
        <label class="block text-gray-600">Document Types</label>
        <select name="document_type_id[]" class="border p-2 w-full rounded" multiple required>
            @foreach ($documentTypes as $type)
                <option value="{{ $type->id }}" {{ (collect(old('document_type_id'))->contains($type->id)) ? 'selected' : '' }}>{{ $type->name }}</option>
            @endforeach
        </select>
        <small class="text-gray-500">Hold Ctrl (Windows) or Command (Mac) to select multiple.</small>
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
