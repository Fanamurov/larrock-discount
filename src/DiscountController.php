<?php

namespace Larrock\ComponentDiscount;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Larrock\ComponentDiscount\Facades\LarrockDiscount;
use Larrock\ComponentDiscount\Helpers\DiscountHelper;

class DiscountController extends Controller
{
    protected $config;

    public function __construct()
    {
        $this->config = LarrockDiscount::shareConfig();
    }

    public function checkKuponDiscount(Request $request)
    {
        if($request->has('keyword')){
            $discountHelper = new DiscountHelper();
            if($data = $discountHelper->check_kupon($request->get('keyword'))){
                return response()->json(['type' => 'success', 'message' => $data->description]);
            }
        }
        return response()->json(['type' => 'notice', 'message' => 'Такого купона нет']);
    }
}