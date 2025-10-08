<?php

namespace App\Http\Controllers\Processor;

use App\Http\Controllers\Controller;
use App\Models\RequestModel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $requests = RequestModel::where('status', 'Retrieved')->get();
        return view('processor.dashboard', compact('requests'));
    }

    public function updateStatus(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);
        $req->update(['status' => $request->status]);

        return back()->with('success', 'Status updated successfully.');
    }
}
