<?php

namespace Larrock\ComponentDiscount\Helpers;

use Cart;
use Cache;
use LarrockCart;
use Carbon\Carbon;
use LarrockDiscount;

class DiscountHelper
{
    /** @var float Стоимость корзины с примененными скидками */
    public $total;

    /** @var float Стоимость корзины без скидок */
    public $clear_total;

    /** @var float Выгода покупателя от скидок */
    public $profit;

    /** @var float Общая сумма потраченных денег покупателем по истории заказов */
    public $history;

    /** @var null|array Доступные скидки */
    public $discounts;

    /** @var LarrockDiscount Скидка в корзине */
    public $d_cart;

    /** @var LarrockDiscount Скидка по истории заказов */
    public $d_history;

    /** @var LarrockDiscount Скидка по купону */
    public $d_kupon;

    /** @var null|string Имя примененного купона */
    public $kupon;

    public function __construct()
    {
        $this->getActiveDiscounts();
        $this->total = $this->clear_total = (float) Cart::instance('main')->total(null, null, '');
        $this->history = (float) 0;
    }

    /** Получение всех активных скидок */
    public function getActiveDiscounts()
    {
        $this->discounts = Cache::rememberForever(sha1('getActiveDiscounts'), function () {
            return LarrockDiscount::getModel()->whereActive(1)->where('date_start', '<=', Carbon::now()->format('Y-m-d H:i:s'))
                ->where('date_end', '>=', Carbon::now()->format('Y-m-d H:i:s'))->get()->groupBy('type');
        });
    }

    /**
     * Проверка на возможность применения скидок и их применение
     * TODO: скидки к товарам, категориям товаров.
     * @param null|float|int $total
     * @param null|string $kupon
     * @return DiscountHelper
     */
    public function check($total = null, $kupon = null)
    {
        if ($total) {
            $this->total = $this->clear_total = (float) str_replace(',', '', $total);
        }

        if (\Request::has('kupon') && ! empty(\Request::get('kupon'))) {
            $this->kupon = \Request::get('kupon');
        }
        if ($kupon) {
            $this->kupon = $kupon;
        }

        if ($this->discounts) {
            //Скидка в корзине
            if (isset($this->discounts['Скидка в корзине'])) {
                $this->d_cart = $this->discounts['Скидка в корзине']->where('cost_min', '<=', $this->total)->where('cost_max', '>=', $this->total)
                    ->where('d_count', '>', 0)->sortByDesc(['percent'])->sortByDesc(['num'])->first();
            }

            if (isset($this->discounts['Купон'])) {
                $this->d_kupon = $this->discounts['Купон']->where('word', '=', $this->kupon)->where('d_count', '>', 0)
                    ->sortByDesc(['percent'])->sortByDesc(['num'])->first();
            }

            //Накопительная скидка по истории заказов
            if (\Auth::check() && isset($this->discounts['Накопительная скидка'])) {
                $cache_key = sha1('userHistoryCart'.\Auth::user()->id);
                $userHistory = Cache::rememberForever($cache_key, function () {
                    return LarrockCart::getModel()->whereUser(\Auth::user()->id)->whereStatusOrder('Завершен')->get();
                });
                if ($userHistory) {
                    $this->history = $userHistory->sum(['cost']);
                    $this->d_history = $this->discounts['Накопительная скидка']->where('cost_min', '<=', $this->history)
                        ->where('cost_max', '>=', $this->history)->where('d_count', '>', 0)
                        ->sortByDesc(['percent'])->sortByDesc(['num'])->first();
                }
            }

            $this->applyDiscounts();
        }

        return $this;
    }

    /**
     * Применение скидок
     * Вычисление total и profit.
     */
    protected function applyDiscounts()
    {
        if ($this->d_cart) {
            if ($this->d_cart->percent > 0) {
                $this->total -= ($this->total / 100) * $this->d_cart->percent;
            } elseif ($this->d_cart->num > 0) {
                $this->total -= (float) $this->d_cart->num;
            }
        }

        if ($this->d_history) {
            if ($this->d_history->percent > 0) {
                $this->total -= ($this->total / 100) * $this->d_history->percent;
            } elseif ($this->d_history->num > 0) {
                $this->total -= (float) $this->d_history->num;
            }
        }

        if ($this->d_kupon) {
            if ($this->d_kupon->percent > 0) {
                $this->total -= ($this->total / 100) * $this->d_kupon->percent;
            } elseif ($this->d_kupon->num > 0) {
                $this->total -= (float) $this->d_kupon->num;
            }
        }

        //Подсчет выгоды покупателя
        if ($this->total) {
            $this->profit = $this->clear_total - $this->total;
        }
    }

    /**
     * Подсчет кол-ва использования каждой примененной скидки.
     * @return $this
     */
    public function countApplyDiscounts()
    {
        $this->check();
        if ($this->d_cart) {
            $discount = $this->d_cart;
            $discount->d_count = --$discount->d_count;
            $discount->save();
        }
        if ($this->d_history) {
            $discount = $this->d_history;
            $discount->d_count = --$discount->d_count;
            $discount->save();
        }
        if ($this->kupon) {
            $discount = $this->d_kupon;
            $discount->d_count = --$discount->d_count;
            $discount->save();
        }

        return $this;
    }

    /**
     * Проверка купона.
     * @param $word
     * @return mixed
     */
    public function checkKupon($word)
    {
        if (isset($this->discounts['Купон'])) {
            $this->kupon = $word;

            return $this->discounts['Купон']->where('word', '=', $this->kupon)->where('d_count', '>', 0)
                ->sortByDesc(['percent'])->sortByDesc(['num'])->first();
        }

        return null;
    }
}
