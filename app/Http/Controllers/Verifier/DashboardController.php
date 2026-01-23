<?php

namespace App\Http\Controllers\Verifier;

use App\Http\Controllers\Controller;
use App\Models\RequestModel;
use Illuminate\Http\Request;
use App\Mail\RequestVerifyingConfirmation;
use Illuminate\Support\Facades\Mail;

class DashboardController extends Controller
{
    public function index()
    {
        // Show all requests that are ready for verification
        $requests = RequestModel::with(['student', 'documentTypes'])
            ->where('status', 'ready_for_verification')
            ->get();

        return view('verifier.dashboard', compact('requests'));
    }

    public function toggleVerification($requestId, $documentId)
    {
        $requestModel = RequestModel::findOrFail($requestId);

        // use documentTypes() instead of document()
        $doc = $requestModel->documentTypes()->where('document_type_id', $documentId)->first();

        if ($doc) {
            $current = $doc->pivot->is_verified;

            // update the pivot record correctly
            $requestModel->documentTypes()->updateExistingPivot($documentId, [
                'is_verified' => !$current,
            ]);

            // reload the relationship to get fresh data
            $requestModel->load('documentTypes');

            // check if all are verified
            $allVerified = $requestModel->documentTypes->every(fn($d) => $d->pivot->is_verified);

            if ($allVerified) {
                $requestModel->update(['status' => 'verified']);
                
                // Send confirmation email to the student
                Mail::to($requestModel->student->email)->send(new RequestVerifyingConfirmation($requestModel));
            }
        }

        return back()->with('success', 'Document verification updated.');
    }
}
