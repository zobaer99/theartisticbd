@foreach($datas as $data)
<tr>
    <td>{{ $data->title }}</td>
    <td>
        {{ optional($data->division)->name }}
        @if($data->district) / {{ $data->district->name }} @endif
        @if($data->thana) / {{ $data->thana->name }} @endif
    </td>
    <td>{{ PriceHelper::adminCurrencyPrice($data->price) }}</td>
    <td>
        <span class="badge badge-{{ $data->status ? 'success' : 'danger' }}">{{ $data->status ? __('Enabled') : __('Disabled') }}</span>
    </td>
    <td class="text-right">
        <div class="action-list">
            <a class="btn btn-secondary btn-sm" href="{{ route('back.shipping-location.edit', $data->id) }}">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('back.shipping-location.destroy', $data->id) }}" method="POST" style="display:inline-block">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this shipping price?')">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@endforeach
{{-- Removed --}}
