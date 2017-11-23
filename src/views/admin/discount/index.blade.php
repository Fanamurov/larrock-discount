@extends('larrock::admin.main')
@section('title') {{ $app->name }} admin @endsection

@section('content')
    <div class="container-head uk-margin-bottom">
        <div class="add-panel uk-margin-bottom uk-text-right">
            <a class="uk-button" href="#modal-help" data-uk-modal="{target:'#modal-help'}"><i class="uk-icon-question"></i></a>
            <a class="uk-button uk-button-primary" href="/admin/{{ $app->name }}/create">Добавить скидку</a>
        </div>
        <div id="modal-help" class="uk-modal">
            <div class="uk-modal-dialog">
                <a class="uk-modal-close uk-close"></a>
                <p>{{ $app->description }}</p>
            </div>
        </div>
        <div class="uk-clearfix"></div>
        {!! Breadcrumbs::render('admin.'. $app->name .'.index') !!}
        <div class="uk-clearfix"></div>
    </div>

    @if(count($data) === 0)
        <div class="uk-alert uk-alert-warning">Скидок еще нет</div>
    @else
        <div class="uk-margin-large-bottom">
            <table class="uk-table uk-table-striped uk-form">
                <thead>
                <tr>
                    <th>Скидка</th>
                    <th>Сумма активации</th>
                    <th>Сумма скидки</th>
                    <th>Осталось</th>
                    <th>Даты</th>
                    @include('larrock::admin.admin-builder.additional-rows-th')
                </tr>
                </thead>
                <tbody>
                @foreach($data as $value)
                    @include('larrock::admin.discount.discount-item', ['data' => $value])
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection