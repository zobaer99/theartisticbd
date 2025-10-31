@extends('master.back')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h3 class="mb-0 bc-title"><b>{{ __('Order Details') }} - {{ $order->transaction_number }}</b></h3>
                    <div>
                        <a class="btn btn-info btn-sm" href="{{ route('back.order.edit', $order->id) }}">
                            <i class="fas fa-edit"></i> {{ __('Edit Order') }}
                        </a>
                        <a class="btn btn-success btn-sm" href="{{ route('back.order.print', $order->id) }}" target="_blank">
                            <i class="fas fa-print"></i> {{ __('Print') }}
                        </a>
                        <a class="btn btn-primary btn-sm" href="{{ route('back.order.index') }}">
                            <i class="fas fa-chevron-left"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Order Information -->
            <div class="col-lg-8">
                <!-- Order Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Order Status') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <span class="badge badge-{{ $order->order_status == 'Pending' ? 'warning' : ($order->order_status == 'In Progress' ? 'info' : ($order->order_status == 'Delivered' ? 'success' : 'danger')) }} p-2">
                                        {{ $order->order_status }}
                                    </span>
                                    <p class="mt-2 mb-0 small">{{ __('Order Status') }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <span class="badge badge-{{ $order->payment_status == 'Paid' ? 'success' : 'danger' }} p-2">
                                        {{ $order->payment_status }}
                                    </span>
                                    <p class="mt-2 mb-0 small">{{ __('Payment Status') }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <span class="badge badge-primary p-2">
                                        {{ $order->payment_method }}
                                    </span>
                                    <p class="mt-2 mb-0 small">{{ __('Payment Method') }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <span class="badge badge-secondary p-2">
                                        {{ $order->created_at->format('M d, Y') }}
                                    </span>
                                    <p class="mt-2 mb-0 small">{{ __('Order Date') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Order Items') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $item)
                                        @php
                                            // Handle different cart data structures
                                            $item_name = isset($item['name']) ? $item['name'] : (isset($item['item']['name']) ? $item['item']['name'] : 'Product');
                                            $item_photo = isset($item['photo']) ? $item['photo'] : (isset($item['item']['photo']) ? $item['item']['photo'] : null);
                                            
                                            // Handle price, quantity, and total calculations
                                            $main_price = isset($item['main_price']) ? $item['main_price'] : (isset($item['price']) ? $item['price'] : 0);
                                            $attribute_price = isset($item['attribute_price']) ? $item['attribute_price'] : 0;
                                            $qty = isset($item['qty']) ? $item['qty'] : 1;
                                            $unit_price = $main_price + $attribute_price;
                                            $line_total = isset($item['total']) ? $item['total'] : ($unit_price * $qty);
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $item_photo ? asset('storage/images/'.$item_photo) : asset('assets/images/placeholder.png') }}" 
                                                         alt="{{ $item_name }}" 
                                                         style="width: 50px; height: 50px; object-fit: cover;" 
                                                         class="rounded me-3">
                                                    <div>
                                                        <h6 class="mb-0">{{ $item_name }}</h6>
                                                        @if(isset($item['size']) && $item['size'])
                                                            <small class="text-muted">{{ __('Size') }}: {{ $item['size'] }}</small>
                                                        @endif
                                                        @if(isset($item['color']) && $item['color'])
                                                            <small class="text-muted">{{ __('Color') }}: {{ $item['color'] }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>৳{{ number_format($unit_price, 2) }}</td>
                                            <td>{{ $qty }}</td>
                                            <td>৳{{ number_format($line_total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Customer Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>{{ __('Name') }}:</strong> {{ $order->user->first_name ?? 'N/A' }} {{ $order->user->last_name ?? '' }}</p>
                                <p><strong>{{ __('Email') }}:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>{{ __('Phone') }}:</strong> {{ $order->user->phone ?? 'N/A' }}</p>
                                <p><strong>{{ __('Customer ID') }}:</strong> #{{ $order->user_id }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Shipping Information') }}</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $shipping = json_decode($order->shipping_info, true);
                            $shipping_service = json_decode($order->shipping, true);
                        @endphp
                        
                        <!-- Shipping Method Information -->
                        @if($shipping_service && isset($shipping_service['title']))
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="bg-light p-3 rounded">
                                    <h6 class="text-primary mb-2">{{ __('Shipping Method') }}</h6>
                                    <p class="mb-1"><strong>{{ __('Service') }}:</strong> {{ $shipping_service['title'] }}</p>
                                    <p class="mb-0"><strong>{{ __('Cost') }}:</strong> ৳{{ number_format($shipping_service['price'] ?? 0, 2) }}</p>
                                    @if(isset($shipping_service['is_condition']) && $shipping_service['is_condition'] == 1)
                                        <p class="mb-0 text-muted"><small>{{ __('Conditional Shipping') }} (Min: ৳{{ number_format($shipping_service['minimum_price'] ?? 0, 2) }})</small></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>{{ __('Name') }}:</strong> {{ $shipping['ship_first_name'] ?? 'N/A' }}</p>
                                <p><strong>{{ __('Email') }}:</strong> {{ $shipping['ship_email'] ?? 'N/A' }}</p>
                                <p><strong>{{ __('Phone') }}:</strong> {{ $shipping['ship_phone'] ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>{{ __('Address') }}:</strong><br>
                                    {{ $shipping['ship_address1'] ?? '' }}<br>
                                    {{ $shipping['ship_address2'] ?? '' }}<br>
                                    {{ $shipping['ship_city'] ?? '' }} {{ $shipping['ship_zip'] ?? '' }}<br>
                                    {{ $shipping['ship_country'] ?? '' }}
                                </p>
                                @if($shipping['ship_company'] ?? '')
                                    <p><strong>{{ __('Company') }}:</strong> {{ $shipping['ship_company'] }}</p>
                                @endif
                            </div>
                        </div>
                        
                        @if(isset($shipping['pathao_city_id']) || isset($shipping['pathao_zone_id']) || isset($shipping['pathao_area_id']))
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-primary">{{ __('Pathao Delivery Information') }}</h6>
                                @php
                                    $pathaoNames = \App\Http\Controllers\Front\CheckoutController::getPathaoNames($shipping);
                                @endphp
                                <div class="row">
                                    <div class="col-md-4">
                                        @if(isset($shipping['pathao_city_id']))
                                        <p><strong>{{ __('City') }}:</strong> {{ $pathaoNames['city_name'] }} (ID: {{ $shipping['pathao_city_id'] }})</p>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        @if(isset($shipping['pathao_zone_id']))
                                        <p><strong>{{ __('Zone') }}:</strong> {{ $pathaoNames['zone_name'] }} (ID: {{ $shipping['pathao_zone_id'] }})</p>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        @if(isset($shipping['pathao_area_id']))
                                        <p><strong>{{ __('Area') }}:</strong> {{ $pathaoNames['area_name'] }} (ID: {{ $shipping['pathao_area_id'] }})</p>
                                        @endif
                                    </div>
                                </div>
                                @if(isset($shipping['pathao_shipping_cost']))
                                <p><strong>{{ __('Pathao Shipping Cost') }}:</strong> {{ '৳' }}{{($shipping['pathao_shipping_cost']) }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Order Summary') }}</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $subtotal = 0;
                            foreach($cart as $item) {
                                // Use the same calculation logic as in the table
                                $main_price = isset($item['main_price']) ? $item['main_price'] : (isset($item['price']) ? $item['price'] : 0);
                                $attribute_price = isset($item['attribute_price']) ? $item['attribute_price'] : 0;
                                $qty = isset($item['qty']) ? $item['qty'] : 1;
                                $unit_price = $main_price + $attribute_price;
                                $line_total = isset($item['total']) ? $item['total'] : ($unit_price * $qty);
                                $subtotal += $line_total;
                            }
                            $shipping_cost = json_decode($order->shipping, true)['cost'] ?? 0;
                            $tax = $order->tax ?? 0;
                            $total = $subtotal + $shipping_cost + $tax;
                        @endphp
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Subtotal') }}:</span>
                            <span>৳{{ number_format($subtotal, 2) }}</span>
                        </div>

                        @if($ship = json_decode($order->shipping_info, true))
                            @php
                                // Get shipping service details
                                $shipping_service = json_decode($order->shipping, true);
                                $address_shipping_cost = $ship['address_shipping_cost'] ?? 0;
                                $pathao_shipping_cost = $ship['pathao_shipping_cost'] ?? 0;
                                $service_shipping_cost = 0;
                                
                                if($shipping_service && isset($shipping_service['price'])) {
                                    $service_shipping_cost = $shipping_service['price'];
                                }
                                
                                $total_shipping = $service_shipping_cost + $address_shipping_cost + $pathao_shipping_cost;
                            @endphp
                            
                            @if($shipping_service && isset($shipping_service['title']))
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Shipping Method') }}:</span>
                                    <span>{{ $shipping_service['title'] }} (৳{{ number_format($service_shipping_cost, 2) }})</span>
                                </div>
                            @endif
                            
                            @if($address_shipping_cost > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Address Shipping') }}:</span>
                                    <span>৳{{ number_format($address_shipping_cost, 2) }}</span>
                                </div>
                            @endif
                            
                            @if($pathao_shipping_cost > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Pathao Shipping') }}:</span>
                                    <span>৳{{ number_format($pathao_shipping_cost, 2) }}</span>
                                </div>
                            @endif
                            
                            @if($total_shipping > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span><strong>{{ __('Total Shipping') }}:</strong></span>
                                    <span><strong>৳{{ number_format($total_shipping, 2) }}</strong></span>
                                </div>
                            @endif
                        @endif
                        
                        @if($tax > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('Tax') }}:</span>
                                <span>৳{{ number_format($tax, 2) }}</span>
                            </div>
                        @endif
                        
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>{{ __('Total') }}:</strong>
                            <strong> @if ($setting->currency_direction == 1)
                            {{ '৳' }}{{ PriceHelper::OrderTotal($order) }}
                        @else
                            {{ PriceHelper::OrderTotal($order) }}{{ '৳' }}
                        @endif</strong>
                        </div>
                        
                        @if($order->txnid)
                            <div class="mt-3">
                                <small class="text-muted">{{ __('Transaction ID') }}: {{ $order->txnid }}</small>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Quick Actions') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>{{ __('Update Order Status') }}</label>
                            <select class="form-control" onchange="updateStatus(this.value, 'order_status')">
                                <option value="">{{ __('Select Status') }}</option>
                                <option value="Pending" {{ $order->order_status == 'Pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="In Progress" {{ $order->order_status == 'In Progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                                <option value="Delivered" {{ $order->order_status == 'Delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                                <option value="Canceled" {{ $order->order_status == 'Canceled' ? 'selected' : '' }}>{{ __('Canceled') }}</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>{{ __('Update Payment Status') }}</label>
                            <select class="form-control" onchange="updateStatus(this.value, 'payment_status')">
                                <option value="">{{ __('Select Status') }}</option>
                                <option value="Paid" {{ $order->payment_status == 'Paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                                <option value="Unpaid" {{ $order->payment_status == 'Unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Tracking -->
        @if(count($order->tracks_data) > 0)
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Order Tracking') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @foreach($order->tracks_data->sortBy('created_at') as $track)
                                    <div class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">{{ $track->title }}</h6>
                                            <p class="timeline-text">{{ $track->created_at->format('M d, Y - h:i A') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline-marker {
            position: absolute;
            left: -35px;
            top: 5px;
            width: 12px;
            height: 12px;
            background: #007bff;
            border-radius: 50%;
        }
        .timeline-marker::before {
            content: '';
            position: absolute;
            left: 5px;
            top: 12px;
            width: 2px;
            height: 20px;
            background: #e3e6f0;
        }
        .timeline-item:last-child .timeline-marker::before {
            display: none;
        }
    </style>

    <script>
        function updateStatus(value, field) {
            if (value && confirm('{{ __("Are you sure you want to update this status?") }}')) {
                window.location.href = `{{ route('back.order.status', [$order->id, '__FIELD__', '__VALUE__']) }}`
                    .replace('__FIELD__', field)
                    .replace('__VALUE__', value);
            }
        }
    </script>
@endsection
