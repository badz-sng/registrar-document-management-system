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
            'in_process' => [
                'value' => RequestModel::whereIn('status', ['status', 'in_process'])->count(),
                'description' => 'Requests currently being processed by staff.',
            ],
            'pending' => [
                'value' => RequestModel::where('status', 'pending')->count(),
                'description' => 'Requests waiting to be reviewed or assigned.',
            ],
            'for_processing' => [
                'value' => RequestModel::where('status', 'Retrieved')->count(),
                'description' => 'Requests ready to be processed by the next role.',
            ],
            'for_verifying' => [
                'value' => RequestModel::where('status', 'ready_for_verification')->count(),
                'description' => 'Requests pending verification before release.',
            ],
            'for_release' => [
                'value' => RequestModel::where('status', 'for_release')->count(),
                'description' => 'Requests that are approved and awaiting pickup.',
            ],
            'released' => [
                'value' => RequestModel::where('status', 'released')->count(),
                'description' => 'Requests that have been successfully released.',
            ],
        ];

        return view('admin.dashboard', compact('users', 'stats'));
    }
}
