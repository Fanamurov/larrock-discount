<?php

namespace Larrock\ComponentDiscount\Models;

use LarrockDiscount;
use Larrock\Core\Component;
use Larrock\Core\Traits\GetLink;
use Illuminate\Database\Eloquent\Model;
use Larrock\ComponentCategory\Models\Category;

/**
 * \Larrock\ComponentDiscount\Models\Discount.
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $url
 * @property string $type
 * @property string $word
 * @property int $cost_min
 * @property int $cost_max
 * @property int $percent
 * @property int $num
 * @property int $d_count
 * @property \Carbon\Carbon $date_start
 * @property \Carbon\Carbon $date_end
 * @property int $position
 * @property int $active
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
    /** @var $this Component */
    protected $config;

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

    protected $dates = ['created_at', 'updated_at', 'date_start', 'date_end'];

    public function getConfig()
    {
        return $this->config;
    }

    public function getCategoryDiscount()
    {
        return $this->hasMany(Category::class, 'discount_id', 'id')->orderBy('position', 'DESC');
    }
}
