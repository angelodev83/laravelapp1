<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use RingCentral\SDK\SDK;

class RingCentralServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SDK::class, function ($app) {
            return new SDK(
                env('RC_CLIENT_ID'),
                env('RC_CLIENT_SECRET'),
                env('RC_SERVER_URL')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
