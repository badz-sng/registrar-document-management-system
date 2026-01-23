<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RequestModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Mail\RequestForReleaseConfirmation;
use Illuminate\Support\Facades\Mail;

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
        $requests = RequestModel::with(['student', 'documents'])
            ->whereIn('status', ['for_signature', 'for_release'])
            ->get();

        return view('admin.for-release', compact('requests'));
    }

    public function toggleSigned($requestId, $documentId)
    {
        $requestModel = RequestModel::findOrFail($requestId);
        $pivot = $requestModel->documents()->where('document_type_id', $documentId)->first();

        if ($pivot) {
            $current = $pivot->pivot->is_signed;
            $requestModel->documents()->updateExistingPivot($documentId, [
                'is_signed' => !$current,
            ]);

            // Reload the relationship to get fresh data after the update
            $requestModel->load('documents');

            // Check if all documents are signed
            $allSigned = $requestModel->documents->every(fn($doc) => $doc->pivot->is_signed);

            if ($allSigned) {
                $requestModel->update(['status' => 'for_release']);
                
                // Send confirmation email to the student
                Mail::to($requestModel->student->email)->send(new RequestForReleaseConfirmation($requestModel));
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

    /**
     * Return full login history JSON for a given user (admin only)
     */
    public function loginHistory($id)
    {
        $user = User::findOrFail($id);

        $histories = $user->loginHistories()->orderBy('created_at', 'desc')->get()->map(function ($h) {
            return [
                'id' => $h->id,
                'created_at' => \Carbon\Carbon::parse($h->created_at)->setTimezone('Asia/Manila')->format('Y-m-d H:i:s') . ' PHT',
                'ip_address' => $h->ip_address,
            ];
        });

        return response()->json(['data' => $histories]);
    }

    /**
     * Show edit form for a user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the given user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:' . implode(',', User::ROLES)],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Toggle disabled state for a user.
     */
    public function toggleDisabled($id)
    {
        $user = User::findOrFail($id);

        // Prevent self-disable
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot disable/enable your own account.');
        }

        $user->disabled = ! $user->disabled;
        $user->save();

        $status = $user->disabled ? 'disabled' : 'enabled';

        return back()->with('success', "User account has been {$status}.");
    }
}
