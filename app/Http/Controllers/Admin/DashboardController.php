<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'role','department')->get();
        $currentUser = Auth::user();

        return view('admin.dashboard', compact('users', 'currentUser'));
    }
}
