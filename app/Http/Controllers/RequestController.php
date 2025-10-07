<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index()
    {
        $requests = RequestModel::with(['student', 'documentType', 'encoder'])->latest()->get();
        return view('requests.index', compact('requests'));
    }

    public function create()
    {
        $students = Student::all();
        $documentTypes = DocumentType::all();
        return view('requests.create', compact('students', 'documentTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'document_type_id' => 'required|exists:document_types,id',
            'representative_id' => 'nullable|exists:representatives,id',
            'authorization_id' => 'nullable|exists:authorizations,id',
        ]);

        $data['encoded_by'] = auth()->id();

        RequestModel::create($data);

        return redirect()->route('requests.index')->with('success', 'Request recorded successfully!');
    }

    public function show(RequestModel $request)
    {
        return view('requests.show', compact('request'));
    }
}

