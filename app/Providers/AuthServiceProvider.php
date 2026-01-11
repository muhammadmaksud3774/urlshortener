<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        // Add your model policies here if needed
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('superadmin', function (User $user) {
            return $user->hasRole('SuperAdmin');
        });

        Gate::define('admin', function (User $user) {
            return $user->hasRole('Admin');
        });

        Gate::define('member', function (User $user) {
            return $user->hasRole('Member');
        });
    }
}
