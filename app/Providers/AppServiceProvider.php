<?php

namespace App\Providers;

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
        // Remove the aliasMiddleware line
        // The line below can be deleted:
        // $this->app['router']->aliasMiddleware('seller.admin', \App\Http\Middleware\SellerAdminMiddleware::class);
    }
}
