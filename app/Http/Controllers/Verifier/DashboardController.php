<?php

namespace App\Http\Controllers\Verifier;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        return view('verifier.dashboard', compact('currentUser'));
    }
}
