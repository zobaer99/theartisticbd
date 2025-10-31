<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="{{ url('/storage/images/' . $setting->favicon) }}" />
    <title>{{ $setting->title }} - Order Invoice</title>
    
    <style>
        /* Print-specific styles */
        @media print {
            @page {
                size: A4;
                margin: 0.3in 0.4in;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            body { 
                margin: 0; 
                padding: 0;
                font-size: 12px !important;
                line-height: 1.3 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            .no-print { display: none !important; }
            .invoice-container {
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                height: auto !important;
                page-break-inside: avoid;
            }
            .invoice-header {
                padding: 5px !important;
                margin-bottom: 8px !important;
                text-align: left !important;
            }
            .logo {
                margin-bottom: 0 !important;
            }
            .logo img {
                max-height: 32px !important;
                padding: 0 !important;
                display: block !important;
            }
            .invoice-body {
                padding: 8px !important;
            }
            .order-details {
                padding: 8px !important;
                margin-bottom: 10px !important;
            }
            .order-details h3 {
                font-size: 16px !important;
                margin-bottom: 6px !important;
                font-weight: bold !important;
            }
            .detail-row {
                margin-bottom: 4px !important;
                padding-bottom: 2px !important;
            }
            .address-section {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 20px !important;
                margin-bottom: 8px !important;
                width: 100% !important;
            }
            .address-box {
                padding: 8px !important;
                border: 1px solid #333 !important;
                break-inside: avoid !important;
                box-sizing: border-box !important;
            }
            .address-box h4 {
                font-size: 13px !important;
                margin-bottom: 6px !important;
                padding-bottom: 3px !important;
                border-bottom: 1px solid #333 !important;
                font-weight: bold !important;
                background-color: #f5f5f5 !important;
                padding: 4px 6px !important;
                margin: -8px -8px 6px -8px !important;
            }
            .address-line {
                margin-bottom: 1px !important;
                font-size: 11px !important;
            }
            .products-table {
                margin-bottom: 8px !important;
            }
            .table th {
                padding: 6px 4px !important;
                font-size: 11px !important;
                border: 1px solid #333 !important;
                background-color: #e9ecef !important;
                background: #e9ecef !important;
                font-weight: bold !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .table td {
                padding: 3px !important;
                font-size: 11px !important;
                line-height: 1.2 !important;
                border: 1px solid #333 !important;
            }
            .total-section {
                padding: 6px !important;
                margin-top: 6px !important;
                border: 1px solid #333 !important;
            }
            .total-row {
                padding: 3px 0 !important;
                font-size: 10px !important;
            }
            .total-row:last-child {
                font-size: 12px !important;
                padding-top: 6px !important;
            }
            .print-date {
                margin-top: 10px !important;
                padding-top: 8px !important;
                font-size: 9px !important;
            }
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background: #fff;
            margin: 10px;
        }
        
        .invoice-container {
            max-width: 900px;
            margin: 10px auto;
            background: #fff;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
            width: 95%;
        }
        
        .invoice-header {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            color: rgb(0, 0, 0);
            padding: 10px;
            text-align: center;
            align-items: center
        }
        
        .logo {
            margin: 0;
        }
        
        .logo img {
            max-height: 35px;
            background: white;
            padding: 5px;
            border-radius: 4px;
            align-content: center;
        }
        
        .invoice-body {
            padding: 18px;
        }
        
        .order-details {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 18px;
            border-left: 3px solid #667eea;
        }
        
        .order-details h3 {
            margin-top: 0;
            color: #667eea;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            border-bottom: 1px dotted #ddd;
            padding-bottom: 3px;
        }
        
        .detail-label {
            font-weight: 600;
            color: #666;
            min-width: 120px;
            font-size: 11px;
        }
        
        .detail-value {
            color: #333;
            flex: 1;
            text-align: right;
            font-size: 11px;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        
        .address-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
            width: 100%;
        }
        
        .address-box {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            border: 1px solid #e9ecef;
            box-sizing: border-box;
        }
        
        .address-box h4 {
            margin-top: 0;
            color: #667eea;
            font-size: 13px;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 2px solid #667eea;
        }
        
        .address-line {
            margin-bottom: 4px;
            display: flex;
            font-size: 11px;
            flex-wrap: wrap;
        }
        
        .address-label {
            font-weight: 600;
            color: #666;
            min-width: 60px;
            flex-shrink: 0;
        }
        
        .address-value {
            flex: 1;
            word-break: break-word;
        }
        
        .products-table {
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        
        .table thead {
            background: #667eea;
            background-color: #667eea;
            color: white;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .table th {
            padding: 12px 10px;
            font-size: 12px;
            font-weight: 600;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
            background: #667eea;
            background-color: #667eea;
            color: white;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
            print-color-adjust: exact;
        }
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table th:last-child {
            text-align: right;
        }
        
        .table td {
            padding: 8px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
            font-size: 11px;
        }
        
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .total-section {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            margin-top: 15px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        
        .total-row:last-child {
            border-bottom: 2px solid #667eea;
            font-weight: bold;
            font-size: 16px;
            margin-top: 8px;
            padding-top: 10px;
        }
        
        .total-label {
            font-weight: 600;
            color: #666;
        }
        
        .total-value {
            color: #333;
            font-weight: 600;
        }
        
        .print-date {
            text-align: center;
            color: #666;
            font-size: 10px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
        }
        
        @media (max-width: 768px) {
            body {
                margin: 5px;
                font-size: 10px;
            }
            
            .invoice-container {
                max-width: 100%;
                border-radius: 0;
                box-shadow: none;
            }
            
            .invoice-header {
                padding: 8px;
            }
            
            .logo img {
                max-height: 30px;
                padding: 3px;
            }
            
            .invoice-body {
                padding: 10px;
            }
            
            .order-details {
                padding: 8px;
                margin-bottom: 10px;
            }
            
            .order-details h3 {
                font-size: 12px;
                margin-bottom: 6px;
            }
            
            .address-section {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                margin-bottom: 12px;
            }
            
            .address-box {
                padding: 8px;
            }
            
            .address-box h4 {
                font-size: 11px;
                margin-bottom: 6px;
                padding-bottom: 3px;
            }
            
            .address-line {
                font-size: 9px;
                flex-direction: column;
                margin-bottom: 2px;
            }
            
            .address-label {
                min-width: auto;
                margin-bottom: 1px;
            }
            
            .detail-row {
                flex-direction: column;
                margin-bottom: 4px;
                padding-bottom: 2px;
            }
            
            .detail-label {
                min-width: auto;
                margin-bottom: 2px;
                font-size: 10px;
            }
            
            .detail-value {
                text-align: left;
                font-size: 10px;
            }
            
            .table th {
                padding: 4px 2px;
                font-size: 9px;
            }
            
            .table td {
                padding: 3px 2px;
                font-size: 9px;
                line-height: 1.2;
            }
            
            .products-table {
                margin-bottom: 10px;
            }
            
            .total-section {
                padding: 8px;
                margin-top: 8px;
            }
            
            .total-row {
                flex-direction: column;
                padding: 2px 0;
                font-size: 9px;
            }
            
            .total-row:last-child {
                font-size: 11px;
                padding-top: 4px;
            }
            
            .total-label {
                margin-bottom: 1px;
            }
            
            .print-date {
                margin-top: 8px;
                padding-top: 6px;
                font-size: 8px;
            }
        }
        
        @media (max-width: 480px) {
            body {
                font-size: 9px;
            }
            
            .invoice-header {
                padding: 5px;
            }
            
            .logo img {
                max-height: 25px;
                padding: 2px;
            }
            
            .invoice-body {
                padding: 8px;
            }
            
            .table th, .table td {
                padding: 2px 1px;
                font-size: 8px;
            }
            
            .address-line, .detail-row, .total-row {
                font-size: 8px;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="logo">
                <img src="{{ url('/storage/images/' . $setting->logo) }}" alt="Logo">
            </div>
            <h1>Order Invoice</h1>
        </div>

        <div class="invoice-body">
            @php
                if ($order->state) {
                    $state = json_decode($order->state, true);
                } else {
                    $state = [];
                }
            @endphp

          

            <!-- Address Section -->
            <div class="address-section">
                <!-- Order Details -->
                <div class="address-box">
                    <h4>Order Information</h4>
                    <div class="address-line">
                        <span class="address-label">Transaction ID:</span>
                        <span>{{ $order->txnid ?: 'N/A' }}</span>
                    </div>
                    <div class="address-line">
                        <span class="address-label">Order ID:</span>
                        <span>#{{ $order->transaction_number }}</span>
                    </div>
                    <div class="address-line">
                        <span class="address-label">Order Date:</span>
                        <span>{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="address-line">
                        <span class="address-label">Payment Status:</span>
                        <span>
                            @if ($order->payment_status == 'Paid')
                                <span class="badge badge-success">Paid</span>
                            @else
                                <span class="badge badge-danger">Unpaid</span>
                            @endif
                        </span>
                    </div>
                    <div class="address-line">
                        <span class="address-label">Payment Method:</span>
                        <span>{{ $order->payment_method }}</span>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="address-box">
                    <h4>Shipping Address</h4>
                    @php
                        $ship = json_decode($order->shipping_info, true);
                        $shipping_service = json_decode($order->shipping, true);
                        // Ensure shipping info exists with proper fallbacks
                  
                        if (!$ship || empty($ship)) {
                            $ship = [];
                        }
                    @endphp
                    
                    <!-- Shipping Method Information -->
                    @if($shipping_service && isset($shipping_service['title']))
                    <div class="address-line">
                        <span class="address-label">Shipping Method:</span>
                        <span>{{ $shipping_service['title'] }} (৳{{ number_format($shipping_service['price'] ?? 0, 2) }})</span>
                    </div>
                    @endif
                    
                    <div class="address-line">
                        <span class="address-label">Name:</span>
                        <span>{{ ($ship['ship_first_name'] ?? '') }}</span>
                    </div>
                    <div class="address-line">
                        <span class="address-label">Email:</span>
                        <span>{{ $ship['ship_email'] ?? 'N/A' }}</span>
                    </div>
                    <div class="address-line">
                        <span class="address-label">Phone:</span>
                        <span>{{ $ship['ship_phone'] ?? 'N/A' }}</span>
                    </div>
                    @if (isset($ship['ship_address1']))
                        <div class="address-line">
                            <span class="address-label">Address:</span>
                            <span>{{ $ship['ship_address1'] }}</span>
                        </div>
                    @endif
                    @if (isset($ship['ship_city']))
                        <div class="address-line">
                            <span class="address-label">City:</span>
                            <span>{{ $ship['ship_city'] }}{{ isset($ship['ship_zip']) ? ', ' . $ship['ship_zip'] : '' }}</span>
                        </div>
                    @endif
                    @if (isset($state['name']))
                        <div class="address-line">
                            <span class="address-label">State:</span>
                            <span>{{ $state['name'] }}</span>
                        </div>
                    @endif
                    @if (isset($ship['ship_country']))
                        {{-- <div class="address-line">
                            <span class="address-label">Country:</span>
                            <span>{{ $ship['ship_country'] }}</span>
                        </div> --}}
                    @endif
                    
                    <!-- Pathao Delivery Information -->
                    @if(isset($ship['pathao_city_id']) || isset($ship['pathao_zone_id']) || isset($ship['pathao_area_id']))
                        <div class="address-line" style="margin-top: 10px; border-top: 1px solid #eee; padding-top: 8px;">
                            <span class="address-label" style="font-weight: bold; color: #007bff;"> Delivery Details:</span>
                            <span></span>
                        </div>
                        @php
                            $pathaoNames = \App\Http\Controllers\Front\CheckoutController::getPathaoNames($ship);
                        @endphp
                        @if(isset($ship['pathao_city_id']))
                        <div class="address-line">
                            <span class="address-label">City:</span>
                            <span>{{ $pathaoNames['city_name'] }}
                                 {{-- (ID: {{ $ship['pathao_city_id'] }}) --}}
                                </span>
                        </div>
                        @endif
                        @if(isset($ship['pathao_zone_id']))
                        <div class="address-line">
                            <span class="address-label">Zone:</span>
                            <span>{{ $pathaoNames['zone_name'] }} 
                                {{-- (ID: {{ $ship['pathao_zone_id'] }}) --}}
                            </span>
                        </div>
                        @endif
                        @if(isset($ship['pathao_area_id']))
                        <div class="address-line">
                            <span class="address-label">Area:</span>
                            <span>{{ $pathaoNames['area_name'] }} {{-- (ID: {{ $ship['pathao_area_id'] }}) --}}</span>
                        </div>
                        @endif
                        @if(isset($ship['pathao_shipping_cost']))
                        <div class="address-line">
                            <span class="address-label">Shipping Cost:</span>
                            <span>{{ '৳' }}{{($ship['pathao_shipping_cost']) }}</span>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
            <!-- Products Table -->
            <div class="products-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="45%">Products</th>
                            <th width="20%">Attributes</th>
                            <th width="15%">Quantity</th>
                            <th width="20%">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $option_price = 0;
                            $total = 0;
                            $subtotal = 0;
                        @endphp
                        @foreach (json_decode($order->cart, true) as $item)
                            @php
                                // Handle different cart structures for backward compatibility
                                $main_price = isset($item['main_price']) ? $item['main_price'] : (isset($item['price']) ? $item['price'] : 0);
                                $attribute_price = isset($item['attribute_price']) ? $item['attribute_price'] : 0;
                                $qty = isset($item['qty']) ? $item['qty'] : 1;
                                $item_name = isset($item['name']) ? $item['name'] : (isset($item['item']['name']) ? $item['item']['name'] : 'Product');
                                $total += $main_price * $qty;
                                $option_price += $attribute_price;
                                $grandSubtotal = $total + $option_price;
                                $unit_price = $main_price + $attribute_price;
                                $line_total = isset($item['total']) ? $item['total'] : ($unit_price * $qty);
                                $subtotal += $line_total;
                            @endphp
                            {{-- @dd($grandSubtotal) --}}
                            <tr>
                                <td><strong>{{ $item_name }}</strong></td>
                                <td>
                                    @if (isset($item['attribute']['names']))
                                        @foreach ($item['attribute']['names'] as $index => $name)
                                            <small>{{ $name }}: {{ $item['attribute']['option_name'][$index] }}</small><br>
                                        @endforeach
                                    @else
                                        <span style="color: #666;">--</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $qty }}</td>
                                <td class="text-right">
                                    @php
                                        $total_price = $main_price + $attribute_price;
                                    @endphp
                                    <strong>
                                        @if ($setting->currency_direction == 1)
                                            {{ '৳' }}{{ round($total_price * 1, 2) }}
                                        @else
                                            {{ round($total_price * 1, 2) }}{{ '৳' }}
                                        @endif
                                    </strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals Section -->
            <div class="total-section">
                <div class="total-row">
                        <span class="total-label"> Sub Total:</span>
                        <span class="total-value">
                            @if ($setting->currency_direction == 1)
                                 {{ '৳' }}{{ round($subtotal * 1, 2) }}
                                @else
                                {{ round($subtotal * 1, 2) }}{{ '৳' }}
                            @endif
                        </span>
                    </div>
                @if ($order->tax != 0)
                    <div class="total-row">
                        <span class="total-label">Tax:</span>
                        <span class="total-value">
                            @if ($setting->currency_direction == 1)
                                {{ '৳' }}{{ round($order->tax * 1, 2) }}
                            @else
                                {{ round($order->tax * 1, 2) }}{{ '৳' }}
                            @endif
                        </span>
                    </div>
                @endif

                @if (json_decode($order->discount, true))
                    @php
                        $discount = json_decode($order->discount, true);
                    @endphp
                    
                    <div class="total-row">
                        <span class="total-label">Coupon Discount ({{ $discount['code']['code_name'] }}):</span>
                        <span class="total-value" style="color: #dc3545;">
                            @if ($setting->currency_direction == 1)
                                -{{ '৳' }}{{ round($discount['discount'] * 1, 2) }}
                            @else
                                -{{ round($discount['discount'] * 1, 2) }}{{ '৳' }}
                            @endif
                        </span>
                    </div>
                @endif

                @if (json_decode($order->shipping_info, true) || json_decode($order->shipping, true))
                    @php
                        $shipping_info = json_decode($order->shipping_info, true);
                        $shipping_service = json_decode($order->shipping, true);
                        
                        $service_cost = 0;
                        $pathao_cost = 0;
                        $address_cost = 0;
                        
                        if($shipping_service && isset($shipping_service['price'])) {
                            $service_cost = $shipping_service['price'];
                        }
                        
                        if($shipping_info) {
                            $pathao_cost = $shipping_info['pathao_shipping_cost'] ?? 0;
                            $address_cost = $shipping_info['address_shipping_cost'] ?? 0;
                        }
                        
                        $total_shipping = $service_cost + $pathao_cost + $address_cost;
                    @endphp
                    
                    @if($service_cost > 0)
                    <div class="total-row">
                        <span class="total-label">Shipping Method ({{ $shipping_service['title'] ?? 'N/A' }}):</span>
                        <span class="total-value">
                            @if ($setting->currency_direction == 1)
                                {{ '৳' }}{{ number_format($service_cost, 2) }}
                            @else
                                {{ number_format($service_cost, 2) }}{{ '৳' }}
                            @endif
                        </span>
                    </div>
                    @endif
                    
                    @if($pathao_cost > 0)
                    <div class="total-row">
                        <span class="total-label">Pathao Shipping:</span>
                        <span class="total-value">
                            @if ($setting->currency_direction == 1)
                                {{ '৳' }}{{ number_format($pathao_cost, 2) }}
                            @else
                                {{ number_format($pathao_cost, 2) }}{{ '৳' }}
                            @endif
                        </span>
                    </div>
                    @endif
                    
                    @if($address_cost > 0)
                    <div class="total-row">
                        <span class="total-label">Address Shipping:</span>
                        <span class="total-value">
                            @if ($setting->currency_direction == 1)
                                {{ '৳' }}{{ number_format($address_cost, 2) }}
                            @else
                                {{ number_format($address_cost, 2) }}{{ '৳' }}
                            @endif
                        </span>
                    </div>
                    @endif
                    
                    @if($total_shipping > 0)
                    <div class="total-row">
                        <span class="total-label"><strong>Total Shipping:</strong></span>
                        <span class="total-value">
                            <strong>
                            @if ($setting->currency_direction == 1)
                                {{ '৳' }}{{ number_format($total_shipping, 2) }}
                            @else
                                {{ number_format($total_shipping, 2) }}{{ '৳' }}
                            @endif
                            </strong>
                        </span>
                    </div>
                    @endif
                @endif

                @if (json_decode($order->state_price, true))
                    <div class="total-row">
                        <span class="total-label">
                            State Tax
                            @if(isset($state['type']) && $state['type'] == 'percentage')
                                ({{ $state['price'] }}%)
                            @endif:
                        </span>
                        <span class="total-value">
                            @if ($setting->currency_direction == 1)
                                {{ '৳' }}{{ round($order['state_price'] * 1, 2) }}
                            @else
                                {{ round($order['state_price'] * 1, 2) }}{{ '৳' }}
                            @endif
                        </span>
                    </div>
                @endif
                
                <!-- Grand Total -->
                <div class="total-row">
                    <span class="total-label">
                        @if ($order->payment_method == 'Cash On Delivery')
                            Total Amount:
                        @else
                            Total Amount Due:
                        @endif
                    </span>
                    <span class="total-value" style="color: #667eea; font-size: 20px;">
                        @if ($setting->currency_direction == 1)
                            {{ '৳' }}{{ PriceHelper::OrderTotal($order) }}
                        @else
                            {{ PriceHelper::OrderTotal($order) }}{{ '৳' }}
                        @endif
                    </span>
                </div>
            </div>

            <!-- Print Date -->
            <div class="print-date">
                Printed on: {{ date('F d, Y \a\t h:i A') }}
            </div>
        </div>
    </div>
</body>

</html>