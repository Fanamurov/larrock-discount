<?php

namespace Larrock\ComponentDiscount;

use Larrock\Core\Component;
use Larrock\ComponentDiscount\Models\Discount;
use Larrock\Core\Helpers\FormBuilder\FormDate;
use Larrock\Core\Helpers\FormBuilder\FormInput;
use Larrock\Core\Helpers\FormBuilder\FormSelect;
use Larrock\Core\Helpers\FormBuilder\FormTextarea;

class DiscountComponent extends Component
{
    public function __construct()
    {
        $this->name = $this->table = 'discount';
        $this->title = 'Скидки';
        $this->description = 'Скидочная система для каталога';
        $this->model = \config('larrock.models.discount', Discount::class);
        $this->addRows()->addPositionAndActive();
    }

    protected function addRows()
    {
        $row = new FormInput('title', 'Название скидки');
        $this->setRow($row->setValid('max:255|required')->setTypo()->setFillable());

        $row = new FormTextarea('description', 'Описание скидки');
        $this->setRow($row->setTypo()->setFillable());

        $row = new FormSelect('type', 'Тип скидки');
        $this->setRow($row->setValid('max:255|required')->setDefaultValue('Скидка в корзине')
            ->setOptions(['Скидка в корзине', 'Накопительная скидка', 'Купон', 'Скидка для параметра'])
            ->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setFillable());

        $row = new FormInput('word', 'Слово-активатор скидки');
        $this->setRow($row->setValid('max:255')
            ->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setInTableAdmin()->setFillable());

        $row = new FormInput('cost_min', 'Минимальная сумма для активации');
        $this->setRow($row->setValid('integer')->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setFillable());

        $row = new FormInput('cost_max', 'Максимальная сумма для активации');
        $this->setRow($row->setValid('integer')->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setFillable());

        $row = new FormInput('percent', 'Скидка к сумме в процентах');
        $this->setRow($row->setValid('max:100|integer')
            ->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setInTableAdmin()->setFillable());

        $row = new FormInput('num', 'Скидка к сумме в абс. величине');
        $this->setRow($row->setValid('integer')->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')
            ->setInTableAdmin()->setFillable());

        $row = new FormInput('d_count', 'Сколько раз может быть использован');
        $this->setRow($row->setValid('integer')->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')
            ->setInTableAdmin()->setFillable()->setDefaultValue(9999999));

        $row = new FormDate('date_start', 'Дата начала акции');
        $this->setRow($row->setDefaultValue(date('Y-m-d H:i:s'))
            ->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setFillable());

        $row = new FormDate('date_end', 'Дата окончания акции');
        $this->setRow($row->setDefaultValue(date('Y-m-d H:i:s'))
            ->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setFillable());

        return $this;
    }
}
