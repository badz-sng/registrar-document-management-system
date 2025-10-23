<?php

namespace App\Http\Controllers\Verifier;

use App\Http\Controllers\Controller;
use App\Models\RequestModel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Load all requests with related documents
        $requests = RequestModel::with(['student', 'documents'])->get();

        return view('verifier.dashboard', compact('requests'));
    }

    public function toggleVerification(Request $request, $requestId, $documentId)
    {
        $req = RequestModel::findOrFail($requestId);

        // Find the pivot record for that document
        $pivot = $req->documents()->where('document_type_id', $documentId)->first();

        if (!$pivot) {
            return back()->with('error', 'Document not found for this request.');
        }

        // Toggle the is_verified flag
        $current = $pivot->pivot->is_verified;
        $req->documents()->updateExistingPivot($documentId, ['is_verified' => !$current]);

        // Check if all documents are verified
        $allVerified = $req->documents->every(fn($doc) => $doc->pivot->is_verified);

        if ($allVerified) {
            $req->update(['status' => 'for_release']);
        }

        return back()->with('success', 'Verification updated successfully.');
    }
}
