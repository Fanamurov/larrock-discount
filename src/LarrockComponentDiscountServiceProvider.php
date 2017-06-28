<?php

namespace Larrock\ComponentDiscount;

use Illuminate\Support\ServiceProvider;

class LarrockComponentDiscountServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'larrock');

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/larrock'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/routes.php';
        $this->app->make(DiscountComponent::class);

        $timestamp = date('Y_m_d_His', time());
        $migrations = [];
        if ( !class_exists('CreateDiscountTable')){
            $migrations = [__DIR__.'/../database/migrations/0000_00_00_000000_create_discount_table.php' => database_path('migrations/'.$timestamp.'_create_discount_table.php')];
        }
        if ( !class_exists('UpdateCartDiscountsTable')){
            $migrations = [__DIR__.'/../database/migrations/0000_00_00_000000_update_cart_table.php' => database_path('migrations/'.$timestamp.'_update_cart_table.php')];
        }
        if ( !class_exists('UpdateCategoryDiscountsTable')){
            $migrations = [__DIR__.'/../database/migrations/0000_00_00_000000_update_category_table.php' => database_path('migrations/'.$timestamp.'_update_category_table.php')];
        }
        if ( !class_exists('AddForeignKeysToCartDiscountTable')){
            $migrations = [__DIR__.'/../database/migrations/0000_00_00_000000_add_foreign_keys_to_cart_table.php' => database_path('migrations/'.$timestamp.'_add_foreign_keys_to_cart_discounts_table.php')];
        }
        if ( !class_exists('AddForeignKeysToCategoryDiscountTable')){
            $migrations = [__DIR__.'/../database/migrations/0000_00_00_000000_add_foreign_keys_to_category_table.php' => database_path('migrations/'.$timestamp.'_add_foreign_keys_to_category_discounts_table.php')];
        }

        $this->publishes([
            $migrations
        ], 'migrations');
    }
}
