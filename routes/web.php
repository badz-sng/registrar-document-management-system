<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Role-based dashboard controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Encoder\DashboardController as EncoderDashboard;
use App\Http\Controllers\Processor\DashboardController as ProcessorDashboard;
use App\Http\Controllers\Verifier\DashboardController as VerifierDashboard;
use App\Http\Controllers\Retriever\DashboardController as RetrieverDashboard;

/*
|--------------------------------------------------------------------------
| Home / Role-based redirection
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect('/login');
    }

    return match (auth()->user()->role) {
        'encoder' => redirect()->route('encoder.dashboard'),
        'processor' => redirect()->route('processor.dashboard'),
        'verifier' => redirect()->route('verifier.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        'retriever' => redirect()->route('retriever.dashboard'),
        default => redirect('/login'),
    };
});

/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');
});

/*
|--------------------------------------------------------------------------
| ENCODER DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:encoder'])->prefix('encoder')->group(function () {
    Route::get('/dashboard', [EncoderDashboard::class, 'index'])->name('encoder.dashboard');
});

/*
|--------------------------------------------------------------------------
| PROCESSOR DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:processor'])->prefix('processor')->group(function () {
    Route::get('/dashboard', [ProcessorDashboard::class, 'index'])->name('processor.dashboard');
});

/*
|--------------------------------------------------------------------------
| VERIFIER DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:verifier'])->prefix('verifier')->group(function () {
    Route::get('/dashboard', [VerifierDashboard::class, 'index'])->name('verifier.dashboard');
});

/*
|--------------------------------------------------------------------------
| RETRIEVER DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:retriever'])->prefix('retriever')->group(function () {
    Route::get('/dashboard', [RetrieverDashboard::class, 'index'])->name('retriever.dashboard');
});

/*
|--------------------------------------------------------------------------
| PROFILE ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Include Laravel Auth routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

// pang salo to kasi may mali sa intended redirect sa login
// so pag walang specific na route, dito siya mag reroute ahahahah AAAAAAAAAAAAAAAAAAAAAAAAAAAAA

Route::fallback(function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'encoder' => redirect()->route('encoder.dashboard'),
            'processor' => redirect()->route('processor.dashboard'),
            'verifier' => redirect()->route('verifier.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            'retriever' => redirect()->route('retriever.dashboard'),
            default => redirect('/login'),
        };
    }

    return redirect('/login');
});