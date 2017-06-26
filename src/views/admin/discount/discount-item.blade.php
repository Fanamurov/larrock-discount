<tr>
    <td>
        <a class="uk-h4" href="/admin/{{ $app->name }}/{{ $data->id }}/edit">{{ $data->title }} <small class="uk-text-muted">[{{ $data->type }}]</small></a>
        @if( !empty($data->word))
            <p>Слово для активации: <strong>{{ $data->word }}</strong></p>
        @endif

        @if(count($data->get_category_discount) > 0)
            <p>Прикреплено к разделам:</p>
            <ul>
                @foreach($data->get_category_discount as $category)
                    <li>
                        <a href="/admin/category/{{ $category->id }}/edit">{{ $category->title }}</a>
                        <span class="uk-text-muted">[<a href="{{ $category->full_url }}" target="_blank" class="uk-text-muted"><i class="uk-icon-link"></i> на сайте</a>]</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </td>
    <td>@if($data->cost_max > 0){{ $data->cost_min }} - {{ $data->cost_max }}@endif</td>
    <td>
        @if($data->num > 0)
            {{ $data->num }} руб.
        @else
            {{ $data->percent }}%
        @endif
    </td>
    <td>
        @if($data->d_count > 0)
            {{ $data->d_count }}
        @else
            <span class="uk-alert uk-alert-danger" style="padding: 2px;">
                {{ $data->d_count }}
            </span>
        @endif
    </td>
    <td>
        @if($data->date_end < \Carbon\Carbon::now())
            <span class="uk-alert uk-alert-danger" style="padding: 2px;">
                до&nbsp;{{ $data->date_end->format('d/m/Y') }}
            </span>
        @else
            c&nbsp;{{ $data->date_start->format('d/m/Y') }} <br/>до&nbsp;{{ $data->date_end->format('d/m/Y') }}
        @endif
    </td>
    @include('larrock::admin.admin-builder.additional-rows-td', ['data_value' => $data])
</tr>