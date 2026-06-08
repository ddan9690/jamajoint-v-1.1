<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // 1. The Super Admin Bypass
        // This runs before all other gate checks. 
        // If it returns true, the policy/gate is automatically authorized.
        Gate::before(function (User $user, $ability) {
            if ($user->role === 'super_admin') {
                return true;
            }
        });

        // 2. Existing Gates
        Gate::define('manage-exams', fn(User $user) => in_array($user->role, ['super_admin', 'exam_admin']));
        Gate::define('manage-system', fn(User $user) => $user->role === 'super_admin');
    }
}