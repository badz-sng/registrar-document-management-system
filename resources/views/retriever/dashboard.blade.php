@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Retriever Dashboard</h1>

<table class="w-full bg-white rounded shadow">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2 text-left">Student</th>
            <th class="p-2">Document</th>
            <th class="p-2">Status</th>
            <th class="p-2">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($requests as $r)
            <tr class="border-t">
                <td class="p-2">{{ $r->student_name }}</td>
                <td class="p-2">{{ $r->document_type }}</td>
                <td class="p-2">{{ $r->status }}</td>
                <td class="p-2">
                    <form method="POST" action="{{ route('retriever.update.status', $r->id) }}">
                        @csrf
                        <button class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded">
                            Mark as Retrieved
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
