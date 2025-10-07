<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect('/login');
    }

    return match (auth()->user()->role) {
        'encoder' => redirect()->route('requests.create'),
        'processor' => redirect()->route('requests.index'),
        'verifier' => redirect()->route('requests.verify.index'),
        'admin' => redirect()->route('dashboard'),
        default => redirect()->route('dashboard'),
    };
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ENCODER ROUTES
Route::middleware(['auth', 'role:encoder'])->group(function () {
    Route::get('/requests/create', [App\Http\Controllers\RequestController::class, 'create'])->name('requests.create');
});

// PROCESSOR ROUTES
Route::middleware(['auth', 'role:processor'])->group(function () {
    Route::get('/requests', [App\Http\Controllers\RequestController::class, 'index'])->name('requests.index');
});

// VERIFIER ROUTES
Route::middleware(['auth', 'role:verifier'])->group(function () {
    Route::get('/verify', [App\Http\Controllers\VerificationController::class, 'index'])->name('requests.verify.index');
});

// ADMIN ROUTES
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
