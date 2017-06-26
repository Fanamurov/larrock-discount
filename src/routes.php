<?php

use Larrock\ComponentDiscount\AdminDiscountController;

Route::group(['prefix' => 'admin', 'middleware'=> ['web', 'level:2', 'LarrockAdminMenu']], function(){
    Route::resource('discount', AdminDiscountController::class, ['names' => [
        'index' => 'admin.discount.index',
        'show' => 'admin.discount.show',
    ]]);
});