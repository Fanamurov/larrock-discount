<?php

namespace Larrock\ComponentDiscount\Middleware;

use Closure;
use View;

class DiscountsShare
{

    /**
     * Handle an incoming request.
     *
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
