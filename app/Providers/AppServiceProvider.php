<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Listeners\SendNewPostNotification;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;

class AppServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            SendNewPostNotification::class,
        ],
    ];
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        SendEmailVerificationNotification::class;
        SendNewPostNotification::class;
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
