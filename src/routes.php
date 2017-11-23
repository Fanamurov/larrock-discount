<?php

Route::post('/ajax/checkKuponDiscount', 'Larrock\ComponentDiscount\DiscountController@checkKuponDiscount')->name('checkKuponDiscount');

Route::group(['prefix' => 'admin', 'middleware'=> ['web', 'level:2', 'LarrockAdminMenu', 'SaveAdminPluginsData', 'SiteSearchAdmin']], function(){
    Route::resource('discount', 'Larrock\ComponentDiscount\AdminDiscountController', ['names' => [
        'index' => 'admin.discount.index',
        'show' => 'admin.discount.show',
    ]]);
});