<?php

namespace Larrock\ComponentDiscount\Facades;

use Illuminate\Support\Facades\Facade;

class LarrockDiscount extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'larrockdiscount';
    }

}