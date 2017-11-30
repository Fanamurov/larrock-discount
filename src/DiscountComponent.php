<?php

namespace Larrock\ComponentDiscount;

use Larrock\Core\Component;
use Larrock\Core\Helpers\FormBuilder\FormDate;
use Larrock\Core\Helpers\FormBuilder\FormInput;
use Larrock\Core\Helpers\FormBuilder\FormSelect;
use Larrock\Core\Helpers\FormBuilder\FormTextarea;
use Larrock\ComponentDiscount\Models\Discount;

class DiscountComponent extends Component
{
    public function __construct()
    {
        $this->name = $this->table = 'discount';
        $this->title = 'Скидки';
        $this->description = 'Скидочная система для каталога';
        $this->model = Discount::class;
        $this->addRows()->addPositionAndActive();
    }

    protected function addRows()
    {
        $row = new FormInput('title', 'Название скидки');
        $this->rows['title'] = $row->setValid('max:255|required')->setTypo()->setFillable();

        $row = new FormTextarea('description', 'Описание скидки');
        $this->rows['description'] = $row->setTypo()->setFillable();

        $row = new FormSelect('type', 'Тип скидки');
        $this->rows['type'] = $row->setValid('max:255|required')->setDefaultValue('Скидка в корзине')
            ->setOptions(['Скидка в корзине', 'Накопительная скидка', 'Купон', 'Скидка для параметра'])
            ->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')->setFillable();

        $row = new FormInput('word', 'Слово-активатор скидки');
        $this->rows['word'] = $row->setValid('max:255')
            ->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')->setInTableAdmin()->setFillable();

        $row = new FormInput('cost_min', 'Минимальная сумма для активации');
        $this->rows['cost_min'] = $row->setValid('integer')
            ->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')->setFillable();

        $row = new FormInput('cost_max', 'Максимальная сумма для активации');
        $this->rows['cost_max'] = $row->setValid('integer')
            ->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')->setFillable();

        $row = new FormInput('percent', 'Скидка к сумме в процентах');
        $this->rows['percent'] = $row->setValid('max:100|integer')
            ->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')->setInTableAdmin()->setFillable();

        $row = new FormInput('num', 'Скидка к сумме в абс. величине');
        $this->rows['num'] = $row->setValid('integer')
            ->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')->setInTableAdmin()->setFillable();

        $row = new FormInput('d_count', 'Сколько раз может быть использован');
        $this->rows['d_count'] = $row->setValid('integer')
            ->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')->setInTableAdmin()->setFillable();

        $row = new FormDate('date_start', 'Дата начала акции');
        $this->rows['date_start'] = $row->setDefaultValue(date('Y-m-d H:i:s'))
            ->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')->setFillable();

        $row = new FormDate('date_end', 'Дата окончания акции');
        $this->rows['date_end'] = $row->setDefaultValue(date('Y-m-d H:i:s'))
            ->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')->setFillable();

        return $this;
    }
}