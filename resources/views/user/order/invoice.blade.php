@extends('master.front')
@section('title')
    {{ __('Invoice') }}
@endsection
@section('content')

    <!-- Page Title-->
    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('user.order.index') }}"><i class="icon-home"></i> {{ __('Orders') }}</a></li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li>{{ __('Order Invoice') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @php
        $state = $order->state ? json_decode($order->state, true) : [];
        $bill = json_decode($order->billing_info, true);
        // Handle missing billing info by falling back to shipping info
        if (!$bill || empty($bill)) {
            $ship_fallback = json_decode($order->shipping_info, true);
            $bill = $ship_fallback ? [
                'bill_first_name' => $ship_fallback['ship_first_name'] ?? '',
                'bill_last_name' => $ship_fallback['ship_last_name'] ?? '',
                'bill_email' => $ship_fallback['ship_email'] ?? '',
                'bill_phone' => $ship_fallback['ship_phone'] ?? '',
                'bill_address1' => $ship_fallback['ship_address1'] ?? '',
                'bill_address2' => $ship_fallback['ship_address2'] ?? '',
                'bill_city' => $ship_fallback['ship_city'] ?? '',
                'bill_zip' => $ship_fallback['ship_zip'] ?? '',
                'bill_country' => $ship_fallback['ship_country'] ?? '',
                'bill_company' => $ship_fallback['ship_company'] ?? ''
            ] : [];
        }
        $ship = json_decode($order->shipping_info, true);
        // Ensure shipping info exists with proper fallbacks
        if (!$ship || empty($ship)) {
            $ship = [];
        }
        $discount = json_decode($order->discount, true);
        $shipping = json_decode($order->shipping, true);
    @endphp

    <!-- Page Content-->
    <div class="container padding-bottom-3x mb-1">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="icon-file-text"></i> {{ __('Order Invoice') }}</h4>
                <div>
                    <a href="{{ route('user.order.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="icon-arrow-left"></i> {{ __('Back') }}
                    </a>
                    <a href="{{ route('user.order.print', $order->id) }}" target="_blank" class="btn btn-primary btn-sm">
                        <i class="icon-printer"></i> {{ __('Print') }}
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Order Summary Header -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ url('/storage/images/' . $setting->logo) }}" alt="Logo" class="img-fluid"
                                style="max-height: 60px;">
                        </div>
                        <div class="text-muted">
                            <p><i class="icon-calendar"></i> <strong>{{ __('Order Date') }}:</strong>
                                {{ $order->created_at->format('M d, Y h:i A') }}</p>
                            <p><i class="icon-credit-card"></i> <strong>{{ __('Order Number') }}:</strong>
                                {{ $order->transaction_number }}</p>
                            <p><i class="icon-hash"></i> <strong>{{ __('Transaction ID') }}:</strong>
                                {{ $order->txnid ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <div
                            class="badge {{ $order->payment_status == 'Paid' ? 'badge-success' : 'badge-danger' }} p-2 mb-2">
                            <i class="icon-{{ $order->payment_status == 'Paid' ? 'check-circle' : 'alert-circle' }}"></i>
                            {{ $order->payment_status }}
                        </div>
                        <div class="text-muted">
                            <p><i class="icon-credit-card"></i> <strong>{{ __('Payment Method') }}:</strong>
                                {{ $order->payment_method }}</p>
                            @if ($order->payment_status == 'Paid')
                                <p><i class="icon-dollar-sign"></i> <strong>{{ __('Paid Amount') }}:</strong>
                                    @if ($setting->currency_direction == 1)
                                        {{ $order->currency_sign }}{{ PriceHelper::OrderTotal($order) }}
                                    @else
                                        {{ PriceHelper::OrderTotal($order) }}{{ $order->currency_sign }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="row">
                    <!-- Left Column - Order Details -->
                    <div class="col-lg-7">
                        <!-- Addresses -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="card h-100 border">
                                    <div class="card-header bg-light d-flex align-items-center">
                                        <i class="icon-user mr-2"></i>
                                        <h6 class="mb-0">{{ __('Billing Address') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <address class="mb-0">
                                            <strong>{{ ($bill['bill_first_name'] ?? '') }}
                                                {{ ($bill['bill_last_name'] ?? '') }}</strong><br>
                                            <i class="icon-mail"></i> {{ $bill['bill_email'] ?? 'N/A' }}<br>
                                            <i class="icon-phone"></i> {{ $bill['bill_phone'] ?? 'N/A' }}<br>
                                            @if (isset($bill['bill_company']))
                                                <i class="icon-briefcase"></i> {{ $bill['bill_company'] }}<br>
                                            @endif
                                            @if (isset($bill['bill_address1']))
                                                <i class="icon-map-pin"></i> {{ $bill['bill_address1'] }}<br>
                                            @endif
                                            @if (isset($bill['bill_address2']))
                                                {{ $bill['bill_address2'] }}<br>
                                            @endif
                                            @if (isset($bill['bill_city']))
                                                {{ $bill['bill_city'] }},
                                            @endif
                                            @if (isset($state['name']))
                                                {{ $state['name'] }},
                                            @endif
                                            @if (isset($bill['bill_zip']))
                                                {{ $bill['bill_zip'] }}<br>
                                            @endif
                                            @if (isset($bill['bill_country']))
                                                {{ $bill['bill_country'] }}
                                            @endif
                                        </address>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border">
                                    <div class="card-header bg-light d-flex align-items-center">
                                        <i class="icon-truck mr-2"></i>
                                        <h6 class="mb-0">{{ __('Shipping Address') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <address class="mb-0">
                                            <strong>{{ ($ship['ship_first_name'] ?? '') }}
                                                {{ ($ship['ship_last_name'] ?? '') }}</strong><br>
                                            <i class="icon-mail"></i> {{ $ship['ship_email'] ?? 'N/A' }}<br>
                                            <i class="icon-phone"></i> {{ $ship['ship_phone'] ?? 'N/A' }}<br>
                                            @if (isset($ship['ship_company']))
                                                <i class="icon-briefcase"></i> {{ $ship['ship_company'] }}<br>
                                            @endif
                                            @if (isset($ship['ship_address1']))
                                                <i class="icon-map-pin"></i> {{ $ship['ship_address1'] }}<br>
                                            @endif
                                            @if (isset($ship['ship_address2']))
                                                {{ $ship['ship_address2'] }}<br>
                                            @endif
                                            @if (isset($ship['ship_city']))
                                                {{ $ship['ship_city'] }},
                                            @endif
                                            @if (isset($state['name']))
                                                {{ $state['name'] }},
                                            @endif
                                            @if (isset($ship['ship_zip']))
                                                {{ $ship['ship_zip'] }}<br>
                                            @endif
                                            @if (isset($ship['ship_country']))
                                                {{ $ship['ship_country'] }}
                                            @endif
                                        </address>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="card border mb-4">
                            <div class="card-header bg-light d-flex align-items-center">
                                <i class="icon-shopping-cart mr-2"></i>
                                <h6 class="mb-0">{{ __('Order Items') }}</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="border-top-0">{{ __('Product') }}</th>
                                                <th class="border-top-0 text-center">{{ __('Qty') }}</th>
                                                <th class="border-top-0 text-right">{{ __('Price') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $subtotal = 0;
                                            @endphp
                                            @foreach (json_decode($order->cart, true) as $key => $item)
                                                @php
                                                    // Handle different cart structures for backward compatibility
                                                    $main_price = isset($item['main_price']) ? $item['main_price'] : (isset($item['price']) ? $item['price'] : 0);
                                                    $attribute_price = isset($item['attribute_price']) ? $item['attribute_price'] : 0;
                                                    $qty = isset($item['qty']) ? $item['qty'] : 1;
                                                    $item_name = isset($item['name']) ? $item['name'] : (isset($item['item']['name']) ? $item['item']['name'] : 'Product');
                                                    
                                                    $total_price = ($main_price + $attribute_price) * $qty;
                                                    $subtotal += $total_price;
                                                    $main_item = App\Models\Item::find($key);
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if ($main_item && $main_item->thumbnail)
                                                                <img src="{{ asset('storage/images/' . $main_item->thumbnail) }}" 
                                                                    alt="{{ $item_name }}" width="60" class="mr-3">
                                                            @endif
                                                            <div>
                                                                <h6 class="mb-1">{{ $item_name }}</h6>
                                                                @if (isset($item['attribute']['names']))
                                                                    <div class="product-attributes mt-2">
                                                                        @foreach ($item['attribute']['names'] as $index => $name)
                                                                            <div class="d-inline-flex align-items-center mb-1 mr-2">
                                                                                <span class="attribute-name font-weight-bold">{{ $name }}:</span>
                                                                                <span class="attribute-value">{{ $item['attribute']['option_name'][$index] }}</span>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                                @if ($main_item)
                                                                    @if (($item['item_type'] == 'digital' || $item['item_type'] == 'license') && $order->payment_status == 'Paid')
                                                                        <div class="mt-2">
                                                                            @if ($main_item->file_type == 'link')
                                                                                <a href="{{ $main_item->link }}" target="_blank"
                                                                                    class="btn btn-sm btn-outline-success">
                                                                                    <i class="icon-link"></i>
                                                                                    {{ __('Access') }}
                                                                                </a>
                                                                            @else
                                                                                <a href="{{ asset('assets/files/' . $main_item->file) }}"
                                                                                    class="btn btn-sm btn-outline-success">
                                                                                    <i class="icon-download"></i>
                                                                                    {{ __('Download') }}
                                                                                </a>
                                                                            @endif
                                                                            @if ($item['item_type'] == 'license')
                                                                                <div class="text-muted small mt-1">
                                                                                    <i class="icon-key"></i>
                                                                                    {{ $item['item_l_n'] }}:
                                                                                    {{ $item['item_l_k'] }}
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">{{ $item['qty'] }}</td>
                                                    <td class="text-right align-middle">
                                                        @if ($setting->currency_direction == 1)
                                                            {{ $order->currency_sign }}{{ round($total_price * $order->currency_value, 2) }}
                                                        @else
                                                            {{ round($total_price * $order->currency_value, 2) }}{{ $order->currency_sign }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Order Summary -->
                    <div class="col-lg-5">
                        <div class="card border sticky-top" style="top: 20px;">
                            <div class="card-header bg-light d-flex align-items-center">
                                <i class="icon-file-text mr-2"></i>
                                <h6 class="mb-0">{{ __('Order Summary') }}</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span><i class="icon-shopping-bag mr-2"></i> {{ __('Subtotal') }}</span>
                                        <span>
                                            @if ($setting->currency_direction == 1)
                                                {{ $order->currency_sign }}{{ round($subtotal * $order->currency_value, 2) }}
                                            @else
                                                {{ round($subtotal * $order->currency_value, 2) }}{{ $order->currency_sign }}
                                            @endif
                                        </span>
                                    </li>
                                    @if ($order->tax != 0)
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="icon-percent mr-2"></i> {{ __('Tax') }}</span>
                                            <span>
                                                @if ($setting->currency_direction == 1)
                                                    {{ $order->currency_sign }}{{ round($order->tax * $order->currency_value, 2) }}
                                                @else
                                                    {{ round($order->tax * $order->currency_value, 2) }}{{ $order->currency_sign }}
                                                @endif
                                            </span>
                                        </li>
                                    @endif
                                    @if ($discount)
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="icon-tag mr-2"></i> {{ __('Discount') }}
                                                ({{ $discount['code']['code_name'] }})</span>
                                            <span class="text-danger">
                                                @if ($setting->currency_direction == 1)
                                                    -{{ $order->currency_sign }}{{ round($discount['discount'] * $order->currency_value, 2) }}
                                                @else
                                                    -{{ round($discount['discount'] * $order->currency_value, 2) }}{{ $order->currency_sign }}
                                                @endif
                                            </span>
                                        </li>
                                    @endif
                                    @if ($shipping)
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="icon-truck mr-2"></i> {{ __('Shipping') }}</span>
                                            <span>
                                                @if ($setting->currency_direction == 1)
                                                    {{ $order->currency_sign }}{{ round($shipping['price'] * $order->currency_value, 2) }}
                                                @else
                                                    {{ round($shipping['price'] * $order->currency_value, 2) }}{{ $order->currency_sign }}
                                                @endif
                                            </span>
                                        </li>
                                    @endif
                                    @if ($order->state_price)
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="icon-map-pin mr-2"></i> {{ __('State Tax') }}</span>
                                            <span>
                                                @if ($setting->currency_direction == 1)
                                                    {{ $order->currency_sign }}{{ round($order->state_price * $order->currency_value, 2) }}
                                                @else
                                                    {{ round($order->state_price * $order->currency_value, 2) }}{{ $order->currency_sign }}
                                                @endif
                                            </span>
                                        </li>
                                    @endif
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center px-0 font-weight-bold bg-light">
                                        <span class="text-dark"><i class="icon-dollar-sign mr-2"></i>
                                            {{ __('Total Amount') }}</span>
                                        <span class="text-dark">
                                            @if ($setting->currency_direction == 1)
                                                {{ $order->currency_sign }}{{ PriceHelper::OrderTotal($order) }}
                                            @else
                                                {{ PriceHelper::OrderTotal($order) }}{{ $order->currency_sign }}
                                            @endif
                                        </span>
                                    </li>
                                </ul>

                                <!-- Order Status Timeline -->
                                <div class="mt-4">
                                    <h6 class="mb-3"><i class="icon-clock mr-2"></i> {{ __('Order Status') }}</h6>
                                    <div class="steps">
                                        @php
                                            $statuses = [
                                                'Pending' => ['icon' => 'icon-clock', 'active' => true],
                                                'Processing' => [
                                                    'icon' => 'icon-refresh-cw',
                                                    'active' =>
                                                        $order->order_status == 'Processing' ||
                                                        $order->order_status == 'Delivered',
                                                ],
                                                'Delivered' => [
                                                    'icon' => 'icon-check-circle',
                                                    'active' => $order->order_status == 'Delivered',
                                                ],
                                            ];
                                        @endphp

                                        @foreach ($statuses as $status => $data)
                                            <div class="step {{ $data['active'] ? 'active' : '' }}">
                                                <div class="step-icon">
                                                    <i class="{{ $data['icon'] }}"></i>
                                                </div>
                                                <div class="step-label">{{ __($status) }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            border-radius: 0.5rem 0.5rem 0 0 !important;
            padding: 1rem 1.25rem;
        }

        .badge {
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.35rem 0.75rem;
        }

        address {
            line-height: 1.6;
        }

        .table th {
            border-top: none;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .list-group-item {
            border-left: none;
            border-right: none;
            padding: 0.75rem 0;
            background: transparent;
        }

        .steps {
            display: flex;
            justify-content: space-between;
            position: relative;
        }

        .steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }

        .step {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }

        .step-icon {
            width: 40px;
            height: 40px;
            margin: 0 auto 10px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        .step.active .step-icon {
            background: #4e54c8;
            color: white;
        }

        .step-label {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .step.active .step-label {
            color: #4e54c8;
            font-weight: 500;
        }

        .sticky-top {
            position: sticky;
        }

        /* Improved attribute styles */
        .product-attributes {
            line-height: 1.6;
        }

        .attribute-name {
            font-size: 13px;
            color: #495057;
            margin-right: 5px;
            white-space: nowrap;
        }

        .attribute-value {
            font-size: 13px;
            color: #fff;
            background-color: #4e54c8;
            padding: 2px 8px;
            border-radius: 4px;
            white-space: nowrap;
        }

        @media (max-width: 576px) {
            .product-attributes {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }
            
            .attribute-name {
                font-size: 12px;
            }
            
            .attribute-value {
                font-size: 12px;
                padding: 2px 6px;
            }
        }
    </style>

@endsection