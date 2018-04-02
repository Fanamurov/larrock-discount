<?php

namespace Larrock\ComponentDiscount\Middleware;

use View;
use Closure;
use Larrock\ComponentDiscount\Helpers\DiscountHelper;

class DiscountsShare
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $discountHelper = new DiscountHelper();
        View::share('discountsShare', $discountHelper->check());

        return $next($request);
    }
}
