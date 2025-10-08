<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RequestModel;

class DashboardController extends Controller
{
    public function index()
    {
        $users = User::all();
        $stats = [
            'total_requests' => RequestModel::count(),
            'pending' => RequestModel::where('status', 'Pending')->count(),
            'for_processing' => RequestModel::where('status', 'For Processing')->count(),
            'for_verifying' => RequestModel::where('status', 'For Verifying')->count(),
            'for_release' => RequestModel::where('status', 'For Release')->count(),
        ];

        return view('admin.dashboard', compact('users', 'stats'));
    }
}
