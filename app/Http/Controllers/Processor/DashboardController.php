<?php

namespace App\Http\Controllers\Processor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        return view('processor.dashboard', compact('currentUser'));
    }
}
