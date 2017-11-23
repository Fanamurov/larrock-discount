<?php

namespace Larrock\ComponentDiscount\Helpers;

use Larrock\ComponentCatalog\CatalogComponent;
use Larrock\ComponentDiscount\Models\Discount;
use Request;
use Carbon\Carbon;
use Cart;
use Larrock\ComponentCart\Models\Cart as ModelCart;

class DiscountHelper
{
    protected $cart_total;

    /**
     * Установка значения суммы в корзине без скидок
     * Или текущая корзина из куков, или из сущ.заказа в БД
     * @param null $db_cart_total
     */
    public function set_total($db_cart_total = NULL)
    {
        $this->cart_total = (float)str_replace(',', '', Cart::instance('main')->total());
        if($db_cart_total && $db_cart_total > 0){
            $this->cart_total = (float)$db_cart_total;
        }
    }

    /**
     * Проверка и применение скидок к заказу
     * @param null $word    слово скидочного купона
     * @param null $db_cart_total   кастомное значение суммы заказа (например из БД)
     * @return array
     */
    public function check($word = NULL, $db_cart_total = NULL)
    {
        $this->set_total($db_cart_total);

        if($this->cart_total === 0){
            return NULL;
        }

        $data = [];
        if($cart = $this->check_cart()){
            $data['discount']['cart'] = $cart;
        }
        if($history = $this->check_history()){
            $data['discount']['history'] = $history;
        }
        if($kupon = $this->check_kupon($word)){
            $data['discount']['kupon'] = $kupon;
        }
        if($db_cart_total === NULL && Cart::instance('main')->count(TRUE) > 0){
            foreach (Cart::instance('main')->content() as $cart_item){
                if(isset($cart_item->model->get_category) && $category_model = $cart_item->model->get_category){
                    if($check_category_discount = $this->check_discount_category($category_model)){
                        $data['discount']['category'][$check_category_discount->id] = $check_category_discount;
                    }
                }
            }
        }

        $data['cost_before_discount'] = $data['cost_after_discount'] = $this->cart_total;
        $data['profit'] = 0;
        if(array_key_exists('discount', $data)){
            foreach ($data['discount'] as $discount_key => $item){
                if($discount_key === 'category'){
                    //Скидка к разделу у товара
                    //dd(Cart::instance('main')->content());
                    //$this->apply_discountsByTovar();
                    //dd($item);
                }else{
                    if($db_cart_total){
                        if($item->percent > 0){
                            $data['profit'] += $this->cart_total - $this->cart_total*(100-$item->percent)/100;
                        }
                        if($item->num > 0){
                            $data['profit'] += $item->num;
                        }
                    }else{
                        $data['profit'] += $item->profit_after_apply;
                    }
                }
            }
            if($data['profit'] > 0){
                $data['cost_after_discount'] = $this->cart_total - $data['profit'];
            }
        }

        return $data;
    }

    public function check_cart($db_cart_total = NULL)
    {
        if($db_cart_total !== NULL){
            $this->set_total($db_cart_total);
        }
        if($discount_cart = Discount::whereActive(1)
            ->whereType('Скидка в корзине')
            ->where('d_count', '>', 0)
            ->where('cost_min', '<', $this->cart_total)
            ->where('cost_max', '>', $this->cart_total)
            ->where('date_start', '<=', Carbon::now()->format('Y-m-d H:i:s'))
            ->where('date_end', '>=', Carbon::now()->format('Y-m-d H:i:s'))
            ->first()){
            return $discount_cart;
        }
        return NULL;
    }

    public function motivate_cart_discount($db_cart_total = NULL)
    {
        $this->set_total($db_cart_total);
        $get_discount = Discount::whereActive(1)
            ->whereType('Скидка в корзине')
            ->where('d_count', '>', 0)
            ->where('cost_max', '>', $this->cart_total)
            ->where('date_start', '<', Carbon::now()->format('Y-m-d H:i:s'))
            ->where('date_end', '>', Carbon::now()->format('Y-m-d H:i:s'))
            ->orderBy('cost_min', 'ASC')
            ->get();
        return $get_discount;
    }

    public function check_history()
    {
        //Смотрим историю покупок
        if($user_id = \Auth::guard()->id()){
            $sum = ModelCart::whereUser($user_id)->whereStatusOrder('Завершен')->sum('cost');
            if($discount_history = Discount::whereActive(1)
                ->whereType('Накопительная скидка')
                ->where('d_count', '>', 0)
                ->where('cost_min', '<', $sum)
                ->where('cost_max', '>', $sum)
                ->where('date_start', '<=', Carbon::now()->format('Y-m-d H:i:s'))
                ->where('date_end', '>=', Carbon::now()->format('Y-m-d H:i:s'))
                ->first()){
                return $discount_history;
            }
        }
        return NULL;
    }

    public function check_kupon($word)
    {
        if(Request::get('kupon')){
            $word = Request::get('kupon');
        }
        if($discount_cart = Discount::whereActive(1)
            ->whereType('Купон')
            ->where('d_count', '>', 0)
            ->where('word', '=', $word)
            ->where('date_start', '<=', Carbon::now()->format('Y-m-d H:i:s'))
            ->where('date_end', '>=', Carbon::now()->format('Y-m-d H:i:s'))
            ->first()){
            return $discount_cart;
        }
        return NULL;
    }

    public function check_discount_category($categories)
    {
        $discount = NULL;

        foreach ($categories as $value){
            //Ищем прикрепленные скидки к разделу товара
            if($value->discount_id !== NULL){
                $discount = Discount::whereId($value->discount_id)
                    ->where('date_start', '<', Carbon::now()->format('Y-m-d H:i:s'))
                    ->where('date_end', '>', Carbon::now()->format('Y-m-d H:i:s'))
                    ->whereActive(1)->first();
            }
            if($discount === NULL){
                //Если нет, проверяем, прикреплены ли скидки к разделам выше
                if(isset($value->get_parent->discount_id) && $value->get_parent->discount_id !== NULL){
                    $discount = Discount::whereId($value->get_parent->discount_id)
                        ->where('date_start', '<=', Carbon::now()->format('Y-m-d H:i:s'))
                        ->where('date_end', '>=', Carbon::now()->format('Y-m-d H:i:s'))
                        ->whereActive(1)->first();
                }
            }
            if($discount === NULL && isset($value->get_parent->get_parent)){
                if($value->get_parent->get_parent->discount_id !== NULL){
                    $discount = Discount::whereId($value->get_parent->get_parent->discount_id)
                        ->where('date_start', '<=', Carbon::now()->format('Y-m-d H:i:s'))
                        ->where('date_end', '>=', Carbon::now()->format('Y-m-d H:i:s'))
                        ->whereActive(1)->first();
                }
            }
            if($discount === NULL && isset($value->get_parent->get_parent->get_parent)){
                if($value->get_parent->get_parent->get_parent->discount_id !== NULL){
                    $discount = Discount::whereId($value->get_parent->get_parent->get_parent->discount_id)
                        ->where('date_start', '<=', Carbon::now()->format('Y-m-d H:i:s'))
                        ->where('date_end', '>=', Carbon::now()->format('Y-m-d H:i:s'))
                        ->whereActive(1)->first();
                }
            }
        }
        return $discount;
    }

    public function apply_discount_category($tovar)
    {
        if($discount = $this->check_discount_category($tovar->get_category)){
            if($discount->percent > 0){
                $tovar['cost_old'] = $tovar['cost'];
                $tovar['cost'] = $tovar['cost']*(100-$discount->percent)/100;
                $tovar->discount_tovar = collect();
                $tovar->discount_tovar->push($discount);
                $tovar['apply_discount'] = TRUE;
            }
            if($discount->num > 0){
                $tovar['cost_old'] = $tovar['cost'];
                $tovar['cost'] -= $discount->num;
                $tovar->discount_tovar = collect();
                $tovar->discount_tovar->push($discount);
                $tovar['apply_discount'] = TRUE;
            }
        }
        $tovar['cost'] = (float)$tovar['cost'];
        $tovar['cost_old'] = (float)$tovar['cost_old'];
        return $tovar;
    }

    public function getCostDiscount($tovar)
    {
        if($discount = $this->check_discount_category($tovar->get_category)){
            if($discount->percent > 0){
                return $tovar['cost']*(100-$discount->percent)/100;
            }
            if($discount->num > 0){
                $tovar['cost'] -= $discount->num;
                return $tovar['cost'];
            }
        }
        return (float)$tovar['cost'];
    }

    public function discountCountApply($discounts)
    {
        foreach ($discounts as $discount_item){
            if($find_discount = Discount::whereId($discount_item->id)->first()){
                --$find_discount->d_count;
            }
            $find_discount->update();
        }
    }

    public function apply_discount_param($tovar)
    {
        $config = new CatalogComponent();

        $discount_rows = [];
        foreach($config->rows as $key => $row){
            if(get_class($row) === 'Larrock\Core\Helpers\FormBuilder\Tags' || get_class($row) === 'Larrock\Core\Helpers\FormBuilder\TagsCreate'){
                $discount_rows[$row->name] = $row;
                $discount_rows[$row->name]['values'] = \DB::table($row->connect['table'])->get();
            }
        }

        foreach ($discount_rows as $discount_key => $discount_value){
            $search_data = array_map('trim', explode(',', $tovar->{$discount_key}));
            foreach ($discount_value['values'] as $value){
                if(in_array($value->title, $search_data, TRUE) && isset($value->discount)){
                    if($value->discount){
                        $get_discount = Discount::whereActive(1)->whereType('Скидка для параметра')->whereId($value->discount)->first();

                        $original_cost = $tovar['cost'];
                        if($tovar['cost_old'] > 0){
                            $original_cost = $tovar['cost_old'];
                        }
                        $new_cost = 99999999;
                        if($get_discount->percent > 0){
                            $new_cost = $original_cost*(100-$get_discount->percent)/100;
                        }
                        if((int)$get_discount->num > 0){
                            $new_cost = (float)$original_cost - (float)$get_discount->num;
                        }
                        if($new_cost < $tovar->cost){
                            $tovar->cost = $new_cost;
                            $tovar->cost_old = $original_cost;
                            $tovar->discount_tovar = collect();
                            $tovar->discount_tovar->push($get_discount);
                            $tovar->apply_discount = TRUE;
                        }
                    }
                }
            }
        }
        return $tovar;
    }

    public function apply_discountsByTovar($tovar, $change_total = NULL)
    {
        if($change_total){
            $this->set_total($tovar->cost);
        }
        $tovar = $this->apply_discount_category($tovar);
        $tovar = $this->apply_discount_param($tovar);
        $tovar->cost = (float)$tovar->cost;
        $tovar->old_cost = (float)$tovar->old_cost;
        return $tovar;
    }
}