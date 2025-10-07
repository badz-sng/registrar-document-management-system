<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReleaseController extends Controller
{
    public function release(RequestModel $request)
    {
        ReleaseRecord::create([
            'request_id' => $request->id,
            'released_by' => auth()->id(),
            'released_to' => $request->representative_id ? 'representative' : 'student',
        ]);

        $request->update(['status' => 'released']);

        return back()->with('success', 'Document released successfully.');
    }
}
