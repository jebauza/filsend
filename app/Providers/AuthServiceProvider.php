<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        /* GATES */

        /* Users */
        Gate::define('users.set-state', function ($user, $permission) {
            return $user->can($permission);
        });

        Gate::define('users.updateAndShow', function ($auth_user, $permission, $user) {
            if($auth_user->can($permission)) {
                return true;
            }else if($auth_user->id === $user->id){
                return true;
            }

            return false;
        });
    }
}
