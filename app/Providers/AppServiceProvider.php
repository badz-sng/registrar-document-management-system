<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    // Determine the redirect after login based on the user's role.
                    // We use User role constants so this logic stays in sync with other
                    // role checks and with the registration validation.
                    // If you add new roles, extend both User::ROLES and the mapping below.
                    $role = auth()->user()->role ?? \App\Models\User::ROLE_ENCODER;

                    $redirect = match ($role) {
                        \App\Models\User::ROLE_ENCODER => route('requests.create'),
                        \App\Models\User::ROLE_PROCESSOR => route('requests.index', ['status' => 'in_process']),
                        \App\Models\User::ROLE_VERIFIER => route('requests.verify.index'),
                        \App\Models\User::ROLE_ADMIN => route('dashboard'),
                        \App\Models\User::ROLE_RETRIEVER => route('retriever.dashboard'),
                        default => route('dashboard'),
                    };

                    return redirect()->intended($redirect);
                }
            };
        });

        Gate::define('create-user', function ($user) {
            return $user->role === \App\Models\User::ROLE_ADMIN;
        });
    }
}
