<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EmailService;

class EmailServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the email services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the email services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EmailService::class, function () {
            return new EmailService();
        });
    }
}
