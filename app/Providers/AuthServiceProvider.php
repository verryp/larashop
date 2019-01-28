<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // * Mendefinisikan Gate yang akan di gunakan di controller

        // * ex: hanya roles admin yang dapat akses route manage-users.
        // * karena roles bertipe json array, maka menggunakan fungsi json_decode buat ngubah ke php array
        Gate::define('manage-users', function($user) {
            return count(array_intersect(["ADMIN"], json_decode($user->roles)));
        });

        Gate::define('manage-categories', function($user) {
            return count(array_intersect(["ADMIN", "STAFF"], json_decode($user->roles)));
        });

        Gate::define('manage-books', function($user) {
            return count(array_intersect(["ADMIN", "STAFF"], json_decode($user->roles)));
        });

        Gate::define('manage-orders', function($user) {
            return count(array_intersect(["ADMIN", "STAFF"], json_decode($user->roles)));
        });
    }
}
