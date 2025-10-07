<?php

namespace App\Providers;

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
                    $role = auth()->user()->role ?? 'encoder';

                    $redirect = match ($role) {
                        'encoder' => route('requests.create'),
                        'processor' => route('requests.index', ['status' => 'in_process']),
                        'verifier' => route('requests.verify.index'),
                        'admin' => route('dashboard'),
                        default => route('dashboard'),
                    };

                    return redirect()->intended($redirect);
                }
            };
        });
    }
}
