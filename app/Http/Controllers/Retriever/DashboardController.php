<?php

namespace App\Http\Controllers\Retriever;

use App\Http\Controllers\Controller;
use App\Models\RequestModel;
use App\Models\DocumentType;
use App\Helpers\ProcessingDays;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Mail\RequestRetrieveConfirmation;
use Illuminate\Support\Facades\Mail;

class DashboardController extends Controller
{
    public function index()
    {
        // $requests = RequestModel::where('retriever_id', auth()->id())->get();
        $requests = RequestModel::with('student')->get();
        return view('retriever.dashboard', compact('requests'));
    }

    public function updateStatus(Request $request, $id)
    {
        // Find the request and ensure this authenticated user is allowed to mark it.
        $req = RequestModel::findOrFail($id);

        // Only allow the assigned retriever to change the status, or allow if no
        // retriever is assigned yet (so they can pick it up). This prevents users
        // from toggling arbitrary requests.
        if ($req->retriever_id && $req->retriever_id !== auth()->id()) {
            abort(403, 'Unauthorized to update this request.');
        }

        // Determine processing days based on the document types (use max)
        $docIds = $req->document_type_ids ?? [$req->document_type_id];
        $maxProcessingDays = 0;
        
        foreach ($docIds as $docId) {
            $doc = DocumentType::findOrFail($docId);
            $days = ProcessingDays::getProcessingDays($doc->name);
            $maxProcessingDays = max($maxProcessingDays, $days);
        }
        
        // Compute release date from today based on max processing days
        $releaseDate = ProcessingDays::computeReleaseDate(Carbon::now(), $maxProcessingDays);

        // Use a consistent, lowercase status token for storage. UI can render
        // human-readable versions (e.g., ucfirst) as needed.
        $req->update([
            'status' => 'retrieved',
            'retriever_id' => auth()->id(),
            'estimated_release_date' => $releaseDate,
        ]);

        // Send confirmation email to the student
        Mail::to($req->student->email)->send(new RequestRetrieveConfirmation($req));

        return back()->with('success', 'Envelope marked as retrieved.');
    }
}
