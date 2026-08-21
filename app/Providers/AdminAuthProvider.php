<?php

namespace App\Providers;

use App\Auth\AdminUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AdminAuthProvider extends ServiceProvider
{
    public function boot(): void
    {
        Auth::provider('admin', function ($app, array $config) {
            return new AdminUserProvider();
        });
    }
}
