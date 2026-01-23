<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestModel;
use App\Models\ReleaseRecord;
use App\Mail\RequestForReleaseConfirmation;
use Illuminate\Support\Facades\Mail;

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

        // Send confirmation email to the student
        Mail::to($request->student->email)->send(new RequestForReleaseConfirmation($request));

        return back()->with('success', 'Document released successfully.');
    }
}
