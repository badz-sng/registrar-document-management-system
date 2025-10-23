<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(RequestModel $request)
    {
        $request->update([
            'status' => 'for_release',
            'verifier_id' => auth()->id(),
            'verified_at' => now(),
        ]);

        ProcessingLog::create([
            'request_id' => $request->id,
            'personnel_id' => auth()->id(),
            'action' => 'Verified document',
        ]);

        return back()->with('success', 'Document verified successfully!');
    }
}

