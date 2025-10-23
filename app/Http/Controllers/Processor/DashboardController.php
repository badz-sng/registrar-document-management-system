<?php

namespace App\Http\Controllers\Processor;

use App\Http\Controllers\Controller;
use App\Models\RequestModel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Only show requests that have been retrieved
        $requests = \App\Models\RequestModel::where('status', 'retrieved')
            ->with(['student', 'documentTypes'])
            ->get();

        return view('processor.dashboard', compact('requests'));
    }

    public function updateStatus(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);
        $req->update(['status' => $request->status]);

        return back()->with('success', 'Status updated successfully.');
    }

    public function markAsPrepared($id)
    {
    $request = \App\Models\Request::findOrFail($id);

    if ($request->status !== 'retrieved') {
        return back()->with('error', 'Only retrieved requests can be marked as prepared.');
    }

    $request->status = 'prepared';
    $request->save();

    return back()->with('success', 'Request marked as prepared successfully.');
    }
}
