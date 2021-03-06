@extends('larrock::admin.main')
@section('title') {{ $package->name }} admin @endsection

@section('content')
    <div class="container-head uk-margin-bottom">
        <div class="uk-grid">
            <div class="uk-width-expand">
                {!! Breadcrumbs::render('admin.'. $package->name .'.index') !!}
            </div>
            <div class="uk-width-auto">
                @if(isset($allowCreate))
                    <a class="uk-button uk-button-primary" href="/admin/{{ $package->name }}/create">Добавить скидку</a>
                @endif
            </div>
        </div>
    </div>

    @if(count($data) === 0)
        <div class="uk-alert uk-alert-warning">Скидок еще нет</div>
    @else
        <div class="uk-margin-large-bottom ibox-content">
            <table class="uk-table uk-table-striped uk-form">
                <thead>
                <tr>
                    <th>Скидка</th>
                    <th class="uk-visible@s">Сумма активации</th>
                    <th class="uk-visible@s">Сумма скидки</th>
                    <th class="uk-visible@s">Осталось</th>
                    <th class="uk-visible@s">Даты</th>
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