<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthorizationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'representative_id' => 'required|exists:representatives,id',
            'authorization_letter' => 'required|file|mimes:pdf,jpg,png',
            'valid_until' => 'required|date',
        ]);

        $path = $request->file('authorization_letter')->store('authorizations', 'public');
        $data['authorization_letter_path'] = $path;

        Authorization::create($data);

        return back()->with('success', 'Authorization uploaded successfully.');
    }
}

