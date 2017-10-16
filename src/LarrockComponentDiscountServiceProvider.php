<?php

namespace Larrock\ComponentDiscount;

use Illuminate\Support\ServiceProvider;
use Larrock\ComponentDiscount\Middleware\DiscountsShare;

class LarrockComponentDiscountServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__.'/views', 'larrock');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/larrock')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make(DiscountComponent::class);

        $this->app['router']->aliasMiddleware('DiscountsShare', DiscountsShare::class);
    }
}