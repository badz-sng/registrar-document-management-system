<?php

namespace App\Http\Controllers\Retriever;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        return view('retriever.dashboard', compact('currentUser'));
    }
}
