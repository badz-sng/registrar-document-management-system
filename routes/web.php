<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Role-based dashboard controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Encoder\DashboardController as EncoderDashboard;
use App\Http\Controllers\Processor\DashboardController as ProcessorDashboard;
use App\Http\Controllers\Verifier\DashboardController as VerifierDashboard;
use App\Http\Controllers\Retriever\DashboardController as RetrieverDashboard;

use App\Http\Controllers\RequestController; 

/*
|----------------------------------------   ----------------------------------
| Home / Role-based redirection
|--------------------------------------------------------------------------
*/
// Home route: redirects to role-specific dashboard. We use the centralized
// User::ROLE_* constants here so the redirect targets stay in sync with the
// roles defined in the User model.
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect('/login');
    }

    return match (auth()->user()->role) {
        \App\Models\User::ROLE_ENCODER => redirect()->route('encoder.dashboard'),
        \App\Models\User::ROLE_PROCESSOR => redirect()->route('processor.dashboard'),
        \App\Models\User::ROLE_VERIFIER => redirect()->route('verifier.dashboard'),
        \App\Models\User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
        \App\Models\User::ROLE_RETRIEVER => redirect()->route('retriever.dashboard'),
        default => redirect('/login'),
    };
});

/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/
// Route groups protected by the RoleMiddleware. The middleware signature expects
// a string like 'role:encoder', so we assemble that string from the centralized
// constants above. This keeps the route protection tied to the single source of truth.
Route::middleware(['auth', 'role:'.\App\Models\User::ROLE_ADMIN])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');
});

/*
|--------------------------------------------------------------------------
| ENCODER DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:'.\App\Models\User::ROLE_ENCODER])->prefix('encoder')->group(function () {
    Route::get('/dashboard', [EncoderDashboard::class, 'index'])->name('encoder.dashboard');
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/requests/store', [RequestController::class, 'store'])->name('requests.store');
});

/*
|--------------------------------------------------------------------------
| PROCESSOR DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:'.\App\Models\User::ROLE_PROCESSOR])
    ->prefix('processor')
    ->group(function () {
        Route::get('/dashboard', [ProcessorDashboard::class, 'index'])
            ->name('processor.dashboard');
        Route::post('/requests/{id}/mark-prepared', [ProcessorDashboard::class, 'markAsPrepared'])
            ->name('processor.markPrepared');
        Route::post('/requests/{request}/documents/{document}/toggle', [RequestController::class, 'togglePrepared'])
            ->name('requests.documents.toggle');

    });


/*
|--------------------------------------------------------------------------
| VERIFIER DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:'.\App\Models\User::ROLE_VERIFIER])->prefix('verifier')->group(function () {
    Route::get('/dashboard', [VerifierDashboard::class, 'index'])->name('verifier.dashboard');
    Route::prefix('verifier')->middleware(['auth', 'role:verifier'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Verifier\DashboardController::class, 'index'])->name('verifier.dashboard');
    Route::post('/toggle/{requestId}/{documentId}', [App\Http\Controllers\Verifier\DashboardController::class, 'toggleVerification'])->name('verifier.toggle');
});

});

/*
|--------------------------------------------------------------------------
| RETRIEVER DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:'.\App\Models\User::ROLE_RETRIEVER])->prefix('retriever')->group(function () {
    Route::get('/dashboard', [RetrieverDashboard::class, 'index'])->name('retriever.dashboard');
    // Allow retrievers to mark a request as retrieved. The controller enforces
    // that only the assigned retriever (or no retriever) can perform this action.
    Route::post('/requests/{id}/retrieve', [RetrieverDashboard::class, 'updateStatus'])->name('retriever.update.status');
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
| REGISTER NEW USER (ADMIN ONLY)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::post('/admin/register', [RegisteredUserController::class, 'store'])
        ->middleware('can:create-user')
        ->name('admin.register');
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
            \App\Models\User::ROLE_ENCODER => redirect()->route('encoder.dashboard'),
            \App\Models\User::ROLE_PROCESSOR => redirect()->route('processor.dashboard'),
            \App\Models\User::ROLE_VERIFIER => redirect()->route('verifier.dashboard'),
            \App\Models\User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            \App\Models\User::ROLE_RETRIEVER => redirect()->route('retriever.dashboard'),
            default => redirect('/login'),
        };
    }

    return redirect('/login');
});