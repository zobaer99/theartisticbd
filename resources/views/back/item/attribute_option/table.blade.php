@foreach($datas as $data)
    <tr>
        <td>
            {{ $data->name }}
        </td>
        <td>
            {{ $data->attribute }}
        </td>
        <td>
            @if($data->color_image && strtolower($data->attribute) === 'color')
                <img src="{{ url('/storage/images/color_options/'.$data->color_image) }}" 
                     alt="{{ $data->name }}" 
                     class="admin-img"
                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
            @else
                <span class="text-muted">{{ __('No Image') }}</span>
            @endif
        </td>
        <td>
            {{ $data->price == 0 ? __('Free') : PriceHelper::adminCurrencyPrice($data->price) }}
        </td>
        <td class="{{$data->stock < 10 && $data->stock != 'unlimited' ? 'bg-danger text-white'  :''}} ">
            @if ($data->stock == '0')
            {{__('Out of Stock')}}
            @else
            {{$data->stock}}
            @endif
        </td>
        <td>
            <div class="action-list">
                <a class="btn btn-secondary btn-sm "
                    href="{{ route('back.option.edit',[$item->id, $data->id]) }}">
                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                </a>
                <a class="btn btn-danger btn-sm " data-toggle="modal"
                    data-target="#confirm-delete" href="javascript:;"
                    data-href="{{ route('back.option.destroy',[$item->id, $data->id]) }}">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </div>
        </td>
    </tr>
@endforeach
