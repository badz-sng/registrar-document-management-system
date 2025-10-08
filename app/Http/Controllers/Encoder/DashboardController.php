<?php

namespace App\Http\Controllers\Encoder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequestModel;
use App\Helpers\ProcessingDays;
use Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $requests = RequestModel::where('user_id', Auth::id())->get();
        return view('encoder.dashboard', compact('requests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_name' => 'required',
            'document_type' => 'required',
        ]);

        $releaseDate = \App\Helpers\ProcessingDays::computeReleaseDate(now(), $validated['document_type']);

        RequestModel::create([
            'user_id' => Auth::id(),
            'student_name' => $validated['student_name'],
            'document_type' => $validated['document_type'],
            'status' => 'Pending',
            'encoded_date' => now(),
            'release_date' => $releaseDate,
        ]);

        return back()->with('success', 'Request successfully encoded.');
    }
}
