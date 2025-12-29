<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RequestModel;

class DashboardController extends Controller
{
    public function index(){
        
        $users = User::all();

        $stats = [
            'total_requests' => [
                'value' => RequestModel::count(),
                'description' => 'All requests submitted by students.',
            ],
            // 'in_process' => [
            //     'value' => RequestModel::whereIn('status', ['status', 'in_process'])->count(),
            //     'description' => 'Requests currently being processed by staff.',
            // ],
            'pending' => [
                'value' => RequestModel::where('status', 'pending')->count(),
                'description' => 'Evelopes that are for retrieval',
            ],
            'for_processing' => [
                'value' => RequestModel::where('status', 'Retrieved')->count(),
                'description' => 'Requests ready to be processed by the processor.',
            ],
            'for_verifying' => [
                'value' => RequestModel::where('status', 'ready_for_verification')->count(),
                'description' => 'Requests pending verification before signature.',
            ],
            'for signature' => [
                'value' => RequestModel::where('status', 'verified')->count(),
                'description' => 'Verified Requests, up for signature.',
            ],
            'released' => [
                'value' => RequestModel::where('status', 'released')->count(),
                'description' => 'Requests that have been successfully released.',
            ],
        ];

        return view('admin.dashboard', compact('users', 'stats'));
    }

   public function forRelease()
    {
        $requests = \App\Models\RequestModel::with(['student', 'documents'])
            ->whereIn('status', ['for_signature', 'for_release'])
            ->get();

        return view('admin.for-release', compact('requests'));
    }

    public function toggleSigned($requestId, $documentId)
    {
        $requestModel = \App\Models\RequestModel::findOrFail($requestId);
        $pivot = $requestModel->documents()->where('document_type_id', $documentId)->first();

        if ($pivot) {
            $current = $pivot->pivot->is_signed;
            $requestModel->documents()->updateExistingPivot($documentId, [
                'is_signed' => !$current,
            ]);

            // Check if all documents are signed
            $allSigned = $requestModel->documents->every(fn($doc) => $doc->pivot->is_signed);

            if ($allSigned) {
                $requestModel->update(['status' => 'for_release']);
            }
        }

        return back()->with('success', 'Document signing status updated.');
    }

    public function users()
    {
        // Eager-load the latest login entry for each user so we can show it in the modal
        $users = User::with('latestLogin')->get();
        return view('admin.users', compact('users'));
    }
}
