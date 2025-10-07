<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProcessingController extends Controller
{
    public function process(RequestModel $request)
    {
        $request->update([
            'status' => 'in_process',
            'processor_id' => auth()->id(),
        ]);

        ProcessingLog::create([
            'request_id' => $request->id,
            'personnel_id' => auth()->id(),
            'action' => 'Started processing document',
        ]);

        return back()->with('success', 'Processing started.');
    }

    public function complete(RequestModel $request)
    {
        $request->update(['status' => 'ready_for_verification']);

        ProcessingLog::create([
            'request_id' => $request->id,
            'personnel_id' => auth()->id(),
            'action' => 'Document processing completed',
        ]);

        return back()->with('success', 'Document ready for verification.');
    }
}
