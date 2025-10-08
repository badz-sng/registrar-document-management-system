<?php

namespace App\Http\Controllers\Retriever;

use App\Http\Controllers\Controller;
use App\Models\RequestModel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $requests = RequestModel::where('status', 'Pending')->get();
        return view('retriever.dashboard', compact('requests'));
    }

    public function updateStatus(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);
        $req->update(['status' => 'Retrieved']);

        return back()->with('success', 'Envelope marked as Retrieved.');
    }
}
