<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UserService;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the auth services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the auth services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(UserService::class, function () {
            return new UserService();
        });
    }
}
