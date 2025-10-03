<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

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
        // Check permission by name
        Blade::if('permission', function ($permissionName) {
            $user = Auth::user();
            return $user && $user->hasPermission($permissionName);
        });

        // Check role by name
        Blade::if('role', function ($roleName) {
            $user = Auth::user();
            return $user && $user->roles->contains('name', $roleName);
        });
    }
}
