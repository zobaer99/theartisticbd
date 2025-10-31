@foreach ($datas as $data)
{{--
 @Author: Anwarul
 @Date: 2025-08-13 15:10:28
 @LastEditors: Anwarul
 @LastEditTime: 2025-08-17 12:21:40
 @Description: Innova IT
 --}}
    <tr id="order-bulk-delete">
        <td><input type="checkbox" class="bulk-item" value="{{ $data->id }}"></td>

        <td>
            <strong>{{ $data->transaction_number }}</strong>
        </td>
        <td>
            <small>{{ $data->created_at ? $data->created_at->format('Y-m-d H:i') : '' }}</small>
        </td>
        
        <td>
            @php
                $shipping = json_decode($data->shipping_info, true) ?? [];
            @endphp
            @if($data->user->first_name != null)
                <div>
                    <strong>{{ trim($data->user->first_name.' '.$data->user->last_name) }}</strong><br>
                    <small class="text-muted">{{ $data->user->email }}</small>
                </div>
            @else
                @php
                    $shipName = '';
                    if(!empty($shipping)){
                        foreach(['ship_first_name','ship_first','name','ship_name'] as $key){
                            if(!empty($shipping[$key])){ $shipName = $shipping[$key]; break; }
                        }
                    }
                    $shipEmail = $shipping['ship_email'] ?? '';
                @endphp
                <div>
                    <strong>{{ $shipName !== '' ? $shipName : 'Guest User' }}</strong><br>
                    <small class="text-muted">{{ $shipEmail !== '' ? $shipEmail : 'No email' }}</small>
                </div>
            @endif
        </td>
        
        <td>
            @if($data->user && $data->user->phone)
                {{ $data->user->phone }}
            @elseif(!empty($shipping['ship_phone']))
                {{ $shipping['ship_phone'] }}
            @else
                <span class="text-muted">N/A</span>
            @endif
        </td>

        <td>
            @if ($setting->currency_direction == 1)
                <strong>{{ '৳' }}{{ PriceHelper::OrderTotal($data) }}</strong>
            @else
                <strong>{{ PriceHelper::OrderTotal($data) }}{{ '৳' }}</strong>
            @endif
        </td>
        
        <td>
            @if($data->payment_method)
                <span class="badge badge-info">{{ $data->payment_method }}</span>
            @else
                <span class="text-muted">N/A</span>
            @endif
        </td>
        
        <td>
            @if($data->tranaction && $data->tranaction->txn_id)
                <code>{{ $data->tranaction->txn_id }}</code>
            @elseif($data->tranaction && $data->tranaction->id)
                <code>{{ $data->tranaction->id }}</code>
            @elseif($data->txnid)
                <code>{{ $data->txnid }}</code>
            @else
                <span class="text-muted">N/A</span>
            @endif
        </td>

        <td>
            <div class="dropdown">
                <button
                    class="btn btn-{{ $data->payment_status == 'Paid' ? 'success' : 'danger' }} btn-sm dropdown-toggle"
                    type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                          {{ $data->payment_status == 'Paid' ? 'Paid' : 'Unpaid' }}
                </button>
                <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                                data-href="{{ route('back.order.status', [$data->id, 'payment_status', 'Paid']) }}">Paid</a>
                    <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                                data-href="{{ route('back.order.status', [$data->id, 'payment_status', 'Unpaid']) }}">Unpaid</a>
                </div>
            </div>
        </td>
        <td>
            <div class="dropdown">
                <button class="btn {{ $data->order_status }}  btn-sm dropdown-toggle" type="button"
                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          {{ $data->order_status }}
                </button>
                <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                                data-href="{{ route('back.order.status', [$data->id, 'order_status', 'Pending']) }}">Pending</a>
                    <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                                data-href="{{ route('back.order.status', [$data->id, 'order_status', 'In Progress']) }}">In Progress</a>
                    <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                                data-href="{{ route('back.order.status', [$data->id, 'order_status', 'Delivered']) }}">Delivered</a>
                    <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                                data-href="{{ route('back.order.status', [$data->id, 'order_status', 'Canceled']) }}">Canceled</a>
                </div>
            </div>
        </td>
        <td>
            @if($data->pathao_consignment_id)
                <div class="pathao-status-container" data-order-id="{{ $data->id }}" data-consignment-id="{{ $data->pathao_consignment_id }}">
                    <span class="pathao-status-loading">
                        <i class="fas fa-spinner fa-spin"></i> Loading...
                    </span>
                </div>
                <br><small class="text-muted">ID: {{ $data->pathao_consignment_id }}</small>
            @else
                <span class="badge badge-secondary">
                    <i class="fas fa-clock"></i> Not Sent
                </span>
            @endif
        </td>
        <td>
            <div class="action-list">
                     <a class="btn btn-secondary btn-sm" href="{{ route('back.order.show', $data->id) }}" 
                         title="View Details">
                    <i class="fas fa-eye"></i>
                </a>
                     <a class="btn btn-info btn-sm" href="{{ route('back.order.invoice', $data->id) }}" 
                         title="View Invoice">
                    <i class="fas fa-file-invoice"></i>
                </a>
                     <a class="btn btn-warning btn-sm" href="{{ route('back.order.edit', $data->id) }}"
                         title="Edit Order">
                    <i class="fas fa-edit"></i>
                </a>
                     <a class="btn btn-success btn-sm" href="{{ route('back.order.print', $data->id) }}" 
                         target="_blank" title="Print Order">
                    <i class="fas fa-print"></i>
                </a>
                     <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" href="javascript:;"
                         data-href="{{ route('back.order.delete', $data->id) }}" title="Delete Order">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </div>
        </td>
    </tr>
@endforeach
