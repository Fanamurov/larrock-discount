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

        $timestamp = date('Y_m_d_His', time());
        $timestamp_after = date('Y_m_d_His', time()+10);
        $migrations = [];
        if ( !class_exists('CreateDiscountTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_create_discount_table.php'] = database_path('migrations/'.$timestamp.'_create_discount_table.php');
        }
        if ( !class_exists('UpdateCartDiscountsTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_update_cart_discounts_table.php'] = database_path('migrations/'.$timestamp.'_update_cart_discounts_table.php');
        }
        if ( !class_exists('UpdateCategoryDiscountsTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_update_category_discounts_table.php'] = database_path('migrations/'.$timestamp.'_update_category_discounts_table.php');
        }
        if ( !class_exists('AddForeignKeysToCartDiscountTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_add_foreign_keys_to_cart_discount_table.php'] = database_path('migrations/'.$timestamp_after.'_add_foreign_keys_to_cart_discount_table.php');
        }
        if ( !class_exists('AddForeignKeysToCategoryDiscountTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_add_foreign_keys_to_category_discount_table.php'] = database_path('migrations/'.$timestamp_after.'_add_foreign_keys_to_category_discount_table.php');
        }

        $this->publishes($migrations, 'migrations');
    }
}
