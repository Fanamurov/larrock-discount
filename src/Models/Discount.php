<?php

namespace Larrock\ComponentDiscount\Models;

use Illuminate\Database\Eloquent\Model;
use Larrock\ComponentCategory\Models\Category;
use Larrock\ComponentDiscount\Facades\LarrockDiscount;
use Larrock\Core\Models\Seo;
use Larrock\Core\Traits\GetLink;

/**
 * App\Models\Discount
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $url
 * @property string $type
 * @property string $word
 * @property integer $cost_min
 * @property integer $cost_max
 * @property integer $percent
 * @property integer $num
 * @property integer $d_count
 * @property \Carbon\Carbon $date_start
 * @property \Carbon\Carbon $date_end
 * @property integer $position
 * @property integer $active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $total_after_apply
 * @property-read mixed $profit_after_apply
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount whereWord($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount whereCostMin($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount whereCostMax($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount wherePercent($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount whereNum($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount whereDCount($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount whereDateStart($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount whereDateEnd($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount wherePosition($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentDiscount\Models\Discount whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Discount extends Model
{
    use GetLink;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable(LarrockDiscount::addFillableUserRows([]));
        $this->config = LarrockDiscount::getConfig();
        $this->table = LarrockDiscount::getTable();
    }

    protected $casts = [
        'position' => 'integer',
        'active' => 'integer',
        'cost_min' => 'integer',
        'cost_max' => 'integer',
        'num' => 'integer',
        'percent' => 'integer',
        'd_count' => 'integer',
    ];

    protected $appends = [
        'total_after_apply',
        'profit_after_apply',
    ];

    protected $dates = ['created_at', 'updated_at', 'date_start', 'date_end'];

    public function get_seo()
    {
        return $this->hasOne(Seo::class, 'id_connect', 'id');
    }

    /**
     * Вычисляет сумму товаров в корзине после применения скидки
     *
     * @return float
     */
    public function getTotalAfterApplyAttribute()
    {
        $total = (float)str_replace(',', '', \Cart::instance('main')->total());
        if($this->percent > 0){
            return $total*(100-$this->percent)/100;
        }
        if($this->num > 0){
            return $total-$this->num;
        }
        return $total;
    }

    public function getProfitAfterApplyAttribute()
    {
        $total = (float)str_replace(',', '', \Cart::instance('main')->total());
        if($this->percent > 0){
            return $total - $total*(100-$this->percent)/100;
        }
        if($this->num > 0){
            return $this->num;
        }
        return 0;
    }

    public function get_category_discount()
    {
        return $this->hasMany(Category::class, 'discount_id', 'id')->orderBy('position', 'DESC');
    }
}
