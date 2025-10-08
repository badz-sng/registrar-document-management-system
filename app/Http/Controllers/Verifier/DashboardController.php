<?php

namespace App\Http\Controllers\Verifier;

use App\Http\Controllers\Controller;
use App\Models\RequestModel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $requests = RequestModel::where('verifier_id', auth()->id())->get();
        return view('verifier.dashboard', compact('requests'));
    }

    public function updateStatus(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);
        $req->update(['status' => $request->status]);

        return back()->with('success', 'Verification status updated.');
    }
}
