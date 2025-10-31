@extends('master.back')

@section('content')
    <div class="container-fluid">
        <script>
            // Ensure global stubs exist to avoid ReferenceError if other scripts call these early.
            window.updateCartSubtotal = window.updateCartSubtotal || function(){ return; };
            window.updateLineTotal = window.updateLineTotal || function(inp){ return; };
        </script>

        <!-- Page Heading -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-sm-flex align-items-center justify-content-between">
            <h3 class="mb-0 bc-title"><b>Edit Order</b></h3>
            <a class="btn btn-primary  btn-sm" href="{{ route('back.order.index') }}"><i
                class="fas fa-chevron-left"></i> Back</a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="row">
            <div class="col-lg-12">
                @include('alerts.alerts')
            </div>
        </div>

        <form class="admin-form" action="{{ route('back.order.update', $order->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <!-- Order Information -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="transaction_number">Order ID *</label>
                                <input type="text" name="transaction_number" class="form-control"
                                    id="transaction_number" placeholder="Enter Order ID"
                                    value="{{ $order->transaction_number }}" required>
                            </div>

                            <div class="form-group">
                                <label for="payment_method">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-control">
                                    <option value="Cash On Delivery" {{ $order->payment_method == 'Cash On Delivery' ? 'selected' : '' }}>Cash On Delivery</option>
                                    <option value="Stripe" {{ $order->payment_method == 'Stripe' ? 'selected' : '' }}>Stripe</option>
                                    <option value="Paypal" {{ $order->payment_method == 'Paypal' ? 'selected' : '' }}>Paypal</option>
                                    <option value="Mollie Payment" {{ $order->payment_method == 'Mollie Payment' ? 'selected' : '' }}>Mollie Payment</option>
                                    <option value="Razorpay" {{ $order->payment_method == 'Razorpay' ? 'selected' : '' }}>Razorpay</option>
                                    <option value="Instamojo" {{ $order->payment_method == 'Instamojo' ? 'selected' : '' }}>Instamojo</option>
                                    <option value="Paystack" {{ $order->payment_method == 'Paystack' ? 'selected' : '' }}>Paystack</option>
                                    <option value="Bank Transfer" {{ $order->payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="payment_status">Payment Status</label>
                                <select name="payment_status" id="payment_status" class="form-control">
                                    <option value="Paid" {{ $order->payment_status == 'Paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="Unpaid" {{ $order->payment_status == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="order_status">Order Status</label>
                                <select name="order_status" id="order_status" class="form-control">
                                    <option value="Pending" {{ $order->order_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="In Progress" {{ $order->order_status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="Delivered" {{ $order->order_status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="Canceled" {{ $order->order_status == 'Canceled' ? 'selected' : '' }}>Canceled</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="txnid">Transaction ID</label>
                                <input type="text" name="txnid" class="form-control"
                                    id="txnid" placeholder="Enter Transaction ID"
                                    value="{{ $order->txnid }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Shipping Information</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $shipping = json_decode($order->shipping_info, true) ?? [];
                            @endphp

                            <div class="form-group">
                                <label for="ship_first_name">Full Name</label>
                                <input type="text" name="ship_first_name" class="form-control"
                                    id="ship_first_name" placeholder="Enter Full Name"
                                    value="{{ $shipping['ship_first_name'] ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label for="ship_email">Email</label>
                                <input type="email" name="ship_email" class="form-control"
                                    id="ship_email" placeholder="Enter Email"
                                    value="{{ $shipping['ship_email'] ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label for="ship_phone">Phone</label>
                                <input type="text" name="ship_phone" class="form-control"
                                    id="ship_phone" placeholder="Enter Phone"
                                    value="{{ $shipping['ship_phone'] ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label for="ship_address1">Full Address *</label>
                                <textarea name="ship_address1" class="form-control" rows="3"
                                    id="ship_address1" placeholder="Enter Full Address" required>{{ $shipping['ship_address1'] ?? '' }}{{ isset($shipping['ship_address2']) && $shipping['ship_address2'] ? ', ' . $shipping['ship_address2'] : '' }}</textarea>
                            </div>

                            <!-- Shipping Method Selection -->
                            <div class="form-group">
                                <label for="shipping_id">Shipping Method</label>
                                @php
                                    $current_shipping = json_decode($order->shipping, true);
                                    $current_shipping_id = $current_shipping['id'] ?? null;
                                    $shipping_services = DB::table('shipping_services')->whereStatus(1)->get();
                                @endphp
                                <select name="shipping_id" id="shipping_id" class="form-control">
                                    <option value="">-- Select Shipping Method --</option>
                                    @foreach($shipping_services as $service)
                                        <option value="{{ $service->id }}" 
                                                data-price="{{ $service->price }}"
                                                {{ $current_shipping_id == $service->id ? 'selected' : '' }}>
                                            {{ $service->title }} - ৳{{ number_format($service->price, 2) }}
                                            @if($service->is_condition == 1)
                                                (Free for orders ≥ ৳{{ number_format($service->minimum_price, 2) }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    @if($current_shipping)
                                        Current: {{ $current_shipping['title'] ?? 'N/A' }} (৳{{ number_format($current_shipping['price'] ?? 0, 2) }})
                                    @else
                                        No shipping method selected
                                    @endif
                                </small>
                            </div>

                            {{-- Hide Address Line 2 --}}
                            <input type="hidden" name="ship_address2" value="">

                            {{-- <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pathao_city_id">Pathao City *</label>
                                            <select class="form-control select2" id="pathao_city_id" name="pathao_city_id" data-placeholder="Select City" required>
                                                <option value="">Select City</option>
                                            </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pathao_zone_id">Pathao Zone *</label>
                                        <select name="pathao_zone_id" id="pathao_zone_id" class="form-control" required>
                                            <option value="">Select Zone</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pathao_area_id">Pathao Area *</label>
                                        <select name="pathao_area_id" id="pathao_area_id" class="form-control" required>
                                            <option value="">Select Area</option>
                                        </select>
                                    </div>
                                </div>
                            </div> --}}
                            
                            {{-- <div class="form-group">
                                <label for="pathao_shipping_cost">Pathao Shipping Cost</label>
                                <input type="number" step="0.01" name="pathao_shipping_cost" class="form-control"
                                    id="pathao_shipping_cost" placeholder="Shipping cost will be calculated automatically"
                                    value="{{ $shipping['pathao_shipping_cost'] ?? '' }}" readonly>
                            </div> --}}

                            {{-- Hide Country and Company - Set Bangladesh as default --}}
                            <input type="hidden" name="ship_country" value="Bangladesh">
                            <input type="hidden" name="ship_company" value="">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing Information -->
            {{-- <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Billing Information') }}</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $billing = json_decode($order->billing_info, true) ?? [];
                            @endphp

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bill_first_name">{{ __('First Name') }}</label>
                                        <input type="text" name="bill_first_name" class="form-control"
                                            id="bill_first_name" placeholder="{{ __('Enter First Name') }}"
                                            value="{{ $billing['bill_first_name'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bill_last_name">{{ __('Last Name') }}</label>
                                        <input type="text" name="bill_last_name" class="form-control"
                                            id="bill_last_name" placeholder="{{ __('Enter Last Name') }}"
                                            value="{{ $billing['bill_last_name'] ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bill_email">{{ __('Email') }}</label>
                                        <input type="email" name="bill_email" class="form-control"
                                            id="bill_email" placeholder="{{ __('Enter Email') }}"
                                            value="{{ $billing['bill_email'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bill_phone">{{ __('Phone') }}</label>
                                        <input type="text" name="bill_phone" class="form-control"
                                            id="bill_phone" placeholder="{{ __('Enter Phone') }}"
                                            value="{{ $billing['bill_phone'] ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="bill_address1">{{ __('Address Line 1') }}</label>
                                <input type="text" name="bill_address1" class="form-control"
                                    id="bill_address1" placeholder="{{ __('Enter Address Line 1') }}"
                                    value="{{ $billing['bill_address1'] ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label for="bill_address2">{{ __('Address Line 2') }}</label>
                                <input type="text" name="bill_address2" class="form-control"
                                    id="bill_address2" placeholder="{{ __('Enter Address Line 2') }}"
                                    value="{{ $billing['bill_address2'] ?? '' }}">
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bill_city">{{ __('City') }}</label>
                                        <input type="text" name="bill_city" class="form-control"
                                            id="bill_city" placeholder="{{ __('Enter City') }}"
                                            value="{{ $billing['bill_city'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bill_zip">{{ __('ZIP Code') }}</label>
                                        <input type="text" name="bill_zip" class="form-control"
                                            id="bill_zip" placeholder="{{ __('Enter ZIP Code') }}"
                                            value="{{ $billing['bill_zip'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bill_country">{{ __('Country') }}</label>
                                        <input type="text" name="bill_country" class="form-control"
                                            id="bill_country" placeholder="{{ __('Enter Country') }}"
                                            value="{{ $billing['bill_country'] ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="bill_company">{{ __('Company') }}</label>
                                <input type="text" name="bill_company" class="form-control"
                                    id="bill_company" placeholder="{{ __('Enter Company') }}"
                                    value="{{ $billing['bill_company'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- Order Products Section -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Products</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $cart = json_decode($order->cart, true) ?? [];
                                $total = 0;
                                $option_price = 0;
                            @endphp
                            
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="30%">Product</th>
                                            <th width="25%">Attributes</th>
                                            <th width="10%">Quantity</th>
                                            <th width="15%">Unit Price</th>
                                            <th width="10%">Total</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cart as $index => $item)
                                            @php
                                                // Handle different cart structures for backward compatibility
                                                $main_price = isset($item['main_price']) ? $item['main_price'] : (isset($item['price']) ? $item['price'] : 0);
                                                $attribute_price = isset($item['attribute_price']) ? $item['attribute_price'] : 0;
                                                $qty = isset($item['qty']) ? $item['qty'] : 1;
                                                $item_name = isset($item['name']) ? $item['name'] : (isset($item['item']['name']) ? $item['item']['name'] : 'Product');
                                                
                                                $unit_price = $main_price + $attribute_price;
                                                $line_total = $unit_price * $qty;
                                                $total += $main_price * $qty;
                                                $option_price += $attribute_price * $qty;
                                                
                                                // Debug cart structure in development
                                                if(config('app.debug')) {
                                                    echo "<!-- Cart Item Structure: " . json_encode($item, JSON_PRETTY_PRINT) . " -->";
                                                }
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if(isset($item['photo']))
                                                            <img src="{{ asset('storage/images/' . $item['photo']) }}" 
                                                                 alt="{{ $item_name }}" 
                                                                 class="img-thumbnail me-3" 
                                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                                        @endif
                                                        <div>
                                                            <strong>{{ $item_name }}</strong>
                                                            @if(isset($item['sku']))
                                                                <br><small class="text-muted">SKU: {{ $item['sku'] }}</small>
                                                            @endif
                                                            <br>
                                                            <button type="button" class="btn btn-sm btn-outline-primary change-product mt-1" 
                                                                    data-index="{{ $index }}" 
                                                                    data-current-name="{{ $item_name }}">
                                                                <i class="fas fa-edit"></i> Change Product
                                                            </button>
                                                            <!-- Hidden product selector -->
                                                            <div class="product-selector mt-2" style="display: none;">
                                                                <select class="form-control form-control-sm product-change-select select2" data-index="{{ $index }}" style="width: 100%;">
                                                                    <option value="">Select a product...</option>
                                                                    @if(isset($items))
                                                                        @foreach($items as $product)
                                                                            <option value="{{ $product->id }}" 
                                                                                    data-name="{{ $product->name }}"
                                                                                    data-price="{{ $product->discount_price }}"
                                                                                    data-photo="{{ $product->photo ? asset('storage/images/'.$product->photo) : asset('assets/images/placeholder.png') }}"
                                                                                    {{ $product->name == $item_name ? 'selected' : '' }}>
                                                                                {{ $product->name }} - ৳{{ number_format($product->discount_price, 2) }}
                                                                            </option>
                                                                        @endforeach
                                                                    @else
                                                                        @foreach(\App\Models\Item::where('status', 1)->get() as $product)
                                                                            <option value="{{ $product->id }}" 
                                                                                    data-name="{{ $product->name }}"
                                                                                    data-price="{{ $product->discount_price }}"
                                                                                    data-photo="{{ $product->photo ? asset('storage/images/'.$product->photo) : asset('assets/images/placeholder.png') }}"
                                                                                    {{ $product->name == $item_name ? 'selected' : '' }}>
                                                                                {{ $product->name }} - ৳{{ number_format($product->discount_price, 2) }}
                                                                            </option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                                <div class="mt-1">
                                                                    <button type="button" class="btn btn-sm btn-success confirm-product-change" data-index="{{ $index }}">
                                                                        <i class="fas fa-check"></i> Confirm
                                                                    </button>
                                                                    <button type="button" class="btn btn-sm btn-secondary cancel-product-change" data-index="{{ $index }}">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        // Resolve original item id from various cart formats
                                                        $currentAttributes = isset($item['attribute']) ? $item['attribute'] : [];
                                                        $itemId = $item['item']['id'] ?? null;
                                                        if(!$itemId && isset($item['slug'])){
                                                            $itemId = \App\Models\Item::where('slug',$item['slug'])->value('id');
                                                        }
                                                        if(!$itemId && isset($item['name'])){
                                                            $itemId = \App\Models\Item::where('name',$item['name'])->value('id');
                                                        }
                                                        $originalItem = $itemId ? \App\Models\Item::with('attributes.options')->find($itemId) : null;
                                                        $hasCartAttributes = !empty($currentAttributes);
                                                        $hasAttributes = $originalItem && $originalItem->attributes->count() > 0;
                                                    @endphp
                                                    @if($hasAttributes)
                                                        <div class="attribute-selection">
                                                            @if($hasCartAttributes)
                                                                <div class="current-attributes mb-2 p-2 bg-light border rounded">
                                                                    <strong class="text-primary d-block mb-1">Current Selection:</strong>
                                                                    <div class="current-attributes-display">
                                                                        @if(isset($currentAttributes['names']))
                                                                            @foreach($currentAttributes['names'] as $ai => $nm)
                                                                                @if(isset($currentAttributes['option_name'][$ai]))
                                                                                    <span class="badge badge-success me-1">{{ $nm }}: {{ $currentAttributes['option_name'][$ai] }}</span>
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="row">
                                                                @foreach($originalItem->attributes as $attr)
                                                                    @if($attr->options->count())
                                                                        @php
                                                                            $selectedVal = '';
                                                                            if(isset($currentAttributes['names'])){
                                                                                foreach($currentAttributes['names'] as $ai => $nm){
                                                                                    if(strtolower($nm) === strtolower($attr->name) && isset($currentAttributes['option_name'][$ai])){
                                                                                        $selectedVal = $currentAttributes['option_name'][$ai];
                                                                                        break;
                                                                                    }
                                                                                }
                                                                            }
                                                                        @endphp
                                                                        <div class="col-sm-6">
                                                                            <div class="form-group">
                                                                                <label class="small mb-1" for="order_attr_{{ $attr->id }}_{{ $index }}">{{ $attr->name }}</label>
                                                                                <select class="form-control form-control-sm attribute_option" id="order_attr_{{ $attr->id }}_{{ $index }}" name="products[{{ $index }}][attributes][{{ strtolower($attr->name) }}]" data-type="{{ $attr->id }}" data-index="{{ $index }}">
                                                                                    <option value="">Select {{ $attr->name }}</option>
                                                                                    @foreach($attr->options->where('stock','!=','0') as $opt)
                                                                                        <option value="{{ $opt->name }}" data-type="{{ $attr->id }}" data-href="{{ $opt->id }}" data-target="{{ PriceHelper::setConvertPrice($opt->price) }}" {{ $selectedVal === $opt->name ? 'selected' : '' }}>{{ $opt->name }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">No attributes available</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <input type="number" 
                                                           name="products[{{ $index }}][qty]" 
                                                           class="form-control text-center product-qty" 
                                                           value="{{ $qty }}" 
                                                           min="1" 
                                                           data-price="{{ $unit_price }}"
                                                           style="width: 80px;">
                                                    @if(isset($itemId) && $itemId)
                                                        <input type="hidden" name="products[{{ $index }}][id]" value="{{ $itemId }}">
                                                    @endif
                                                    <input type="hidden" name="products[{{ $index }}][name]" value="{{ $item_name }}">
                                                    <input type="hidden" name="products[{{ $index }}][main_price]" value="{{ $main_price }}">
                                                    <input type="hidden" name="products[{{ $index }}][attribute_price]" value="{{ $attribute_price }}">
                                                    @if(isset($item['attribute']))
                                                        <input type="hidden" name="products[{{ $index }}][attribute]" value="{{ json_encode($item['attribute']) }}">
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    <strong>৳{{ number_format($unit_price, 2) }}</strong>
                                                </td>
                                                <td class="text-right">
                                                    <strong class="line-total">৳{{ number_format($line_total, 2) }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm remove-product" 
                                                            data-index="{{ $index }}"
                                                            title="Remove Product">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="thead-light">
                                        <tr>
                                            <td colspan="5" class="text-right"><strong>Subtotal:</strong></td>
                                            <td class="text-right"><strong id="cart-subtotal">৳{{ number_format($total + $option_price, 2) }}</strong></td>
                                        </tr>
                                        @if($order->tax > 0)
                                            <tr>
                                                <td colspan="5" class="text-right"><strong>Tax:</strong></td>
                                                <td class="text-right"><strong id="cart-tax">৳{{ number_format($order->tax, 2) }}</strong></td>
                                            </tr>
                                        @endif
                                        @if(json_decode($order->shipping_info, true))
                                            @php $shipping = json_decode($order->shipping_info, true); @endphp
                                            <tr>
                                                <td colspan="5" class="text-right"><strong>Shipping:</strong></td>
                                                <td class="text-right">
                                                    <strong id="pathao_shipping_display">৳{{ number_format($shipping['pathao_shipping_cost'] ?? 0, 2) }}</strong>
                                                    <input type="hidden" name="pathao_shipping_cost" id="pathao_shipping_cost" value="{{ $shipping['pathao_shipping_cost'] ?? 0 }}">
                                                </td>
                                            </tr>
                                        @endif
                                        <tr class="table-active">
                                            <td colspan="5" class="text-right"><strong>Total:</strong></td>
                                            <td class="text-right"><strong id="cart-total">৳{{ \App\Helpers\PriceHelper::OrderTotal($order) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <script>
                                    // Helper: parse a formatted price string like "৳1,234.56" into number
                                    function parsePrice(str) {
                                        if(typeof str === 'number') return str;
                                        if(!str) return 0;
                                        return parseFloat(String(str).replace(/[^0-9.-]+/g, '')) || 0;
                                    }

                                    // Make the function globally available so other scripts can call it
                                    window.updateCartSubtotal = function() {
                                        try {
                                            var subtotal = 0;
                                            // Sum all line totals
                                            document.querySelectorAll('.line-total').forEach(function(el) {
                                                subtotal += parsePrice(el.textContent || el.innerText);
                                            });

                                            // Update subtotal display
                                            var subtotalEl = document.getElementById('cart-subtotal');
                                            if(subtotalEl) {
                                                subtotalEl.textContent = '৳' + subtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                            }

                                            // Read tax (if present)
                                            var tax = 0;
                                            var taxEl = document.getElementById('cart-tax');
                                            if(taxEl) tax = parsePrice(taxEl.textContent || taxEl.innerText);

                                            // Read Pathao shipping cost hidden input
                                            var shipping = 0;
                                            var shipInput = document.getElementById('pathao_shipping_cost');
                                            if(shipInput) shipping = parseFloat(shipInput.value) || 0;

                                            var total = subtotal + tax + shipping;

                                            var totalEl = document.getElementById('cart-total');
                                            if(totalEl) {
                                                totalEl.textContent = '৳' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                            }
                                        } catch (e) {
                                            console.error('updateCartSubtotal error', e);
                                        }
                                    };

                                    // Recalculate a single line total when qty changes
                                    function updateLineTotal(input) {
                                        var row = input.closest('tr');
                                        if(!row) return;
                                        var price = parseFloat(input.getAttribute('data-price')) || 0;
                                        var qty = parseFloat(input.value) || 0;
                                        var lineTotal = price * qty;
                                        var display = row.querySelector('.line-total');
                                        if(display) {
                                            display.textContent = '৳' + lineTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                        }
                                    }

                                    // Attach listeners after DOM is ready
                                    (function() {
                                        // qty inputs
                                        document.querySelectorAll('.product-qty').forEach(function(inp) {
                                            inp.addEventListener('change', function(e){
                                                updateLineTotal(e.target);
                                                window.updateCartSubtotal();
                                            });
                                            inp.addEventListener('input', function(e){
                                                updateLineTotal(e.target);
                                                window.updateCartSubtotal();
                                            });
                                        });

                                        // Remove product buttons should also recalc totals
                                        document.querySelectorAll('.remove-product').forEach(function(btn){
                                            btn.addEventListener('click', function(){
                                                // give time for row removal logic elsewhere, then recalc
                                                setTimeout(function(){ window.updateCartSubtotal(); }, 50);
                                            });
                                        });

                                        // If pathao shipping input changes elsewhere (eg. calculatePathaoPrice), listen for changes
                                        var shipInput = document.getElementById('pathao_shipping_cost');
                                        if(shipInput) {
                                            shipInput.addEventListener('change', function(){ window.updateCartSubtotal(); });
                                        }

                                        // Initial calculation
                                        window.updateCartSubtotal();
                                    })();
                                </script>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-success" id="add-new-product"><i class="fas fa-plus"></i> Add Product</button>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    You can adjust product quantities. Prices will be recalculated automatically.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="submit" class="btn btn-primary btn-lg">Update Order</button>
                            <a href="{{ route('back.order.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for existing selects
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            width: '100%'
        });
    }
    
    // Handle quantity changes
    const qtyInputs = document.querySelectorAll('.product-qty');
    const attributeSelectors = document.querySelectorAll('.attribute-selector, .attribute_option');
    
    qtyInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateLineTotal(this);
            updateCartSubtotal();
        });
    });
    
    // Handle attribute changes
    attributeSelectors.forEach(selector => {
        selector.addEventListener('change', function() {
            updateProductAttributes(this);
            updateProductPrice(this);
        });
    });
    
    // Initialize totals on page load
    updateCartSubtotal();
    
    // Initialize shipping cost display on page load
    const shippingInput = document.getElementById('pathao_shipping_cost');
    const shippingDisplay = document.getElementById('pathao_shipping_display');
    if (shippingInput && shippingDisplay && shippingInput.value) {
        const currentShippingCost = parseFloat(shippingInput.value) || 0;
        shippingDisplay.textContent = '৳' + currentShippingCost.toFixed(2);
    }

    // Add new product handler
    const addBtn = document.getElementById('add-new-product');
    if(addBtn){
        addBtn.addEventListener('click', function(){
            const tbody = document.querySelector('table tbody');
            const newIndex = tbody.querySelectorAll('tr').length; // next index
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('storage/images/'.$order->cart_image_placeholder ?? 'placeholder.png') }}" class="img-thumbnail me-3" style="width:50px;height:50px;object-fit:cover;" alt="Placeholder">
                        <div>
                            <strong>New Product</strong><br>
                            <button type="button" class="btn btn-sm btn-outline-primary change-product mt-1" data-index="${newIndex}"><i class="fas fa-edit"></i> Select Product</button>
                            <div class="product-selector mt-2" style="display:none;">
                                <select class="form-control form-control-sm product-change-select select2" data-index="${newIndex}" style="width:100%;">
                                    <option value="">Select a product...</option>
                                    @foreach($items as $product)
                                        <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->discount_price }}" data-photo="{{ $product->photo ? asset('storage/images/'.$product->photo) : asset('assets/images/placeholder.png') }}">{{ $product->name }} - ৳{{ number_format($product->discount_price,2) }}</option>
                                    @endforeach
                                </select>
                                <div class="mt-1">
                                    <button type="button" class="btn btn-sm btn-success confirm-product-change" data-index="${newIndex}"><i class="fas fa-check"></i> Confirm</button>
                                    <button type="button" class="btn btn-sm btn-secondary cancel-product-change" data-index="${newIndex}"><i class="fas fa-times"></i> Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td><span class="text-muted small">No attributes available</span></td>
                <td class="text-center">
                    <input type="number" name="products[${newIndex}][qty]" class="form-control text-center product-qty" value="1" min="1" data-price="0" style="width:80px;">
                    <input type="hidden" name="products[${newIndex}][id]" value="">
                    <input type="hidden" name="products[${newIndex}][name]" value="">
                    <input type="hidden" name="products[${newIndex}][main_price]" value="0">
                    <input type="hidden" name="products[${newIndex}][attribute_price]" value="0">
                </td>
                <td class="text-right"><strong>৳0.00</strong></td>
                <td class="text-right"><strong class="line-total">৳0.00</strong></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-product" title="Remove Product"><i class="fas fa-trash"></i></button></td>`;
            tbody.appendChild(row);

            // Bind qty change
            row.querySelector('.product-qty').addEventListener('change', function(){ updateLineTotal(this); updateCartSubtotal(); });
        });
    }
    
    function updateProductAttributes(selector) {
        const index = selector.dataset.index;
        const row = selector.closest('tr');
        const currentDisplay = row.querySelector('.current-attributes-display');
        const currentSelectionBox = row.querySelector('.current-attributes');
        
        // Get all selected attributes for this product
        const productSelectors = row.querySelectorAll('.attribute-selector, .attribute_option');
        let attributesHtml = '';
        let currentSelectionHtml = '';
        
        productSelectors.forEach(sel => {
            if (sel.value) {
                const type = sel.dataset.type;
                const value = sel.value;
                const label = sel.closest('.form-group')?.querySelector('label')?.textContent || type;
                
                // For current display (bottom)
                attributesHtml += `<span class="badge badge-info">${label}: ${value}</span> `;
                
                // For current selection box (top - green badges)
                currentSelectionHtml += `<span class="badge badge-success me-1">${label}: ${value}</span> `;
            }
        });
        
        // Update current display if exists
        if (currentDisplay) {
            currentDisplay.innerHTML = attributesHtml || 'No attributes selected';
        }
        
        // Update current selection box if exists
        if (currentSelectionBox) {
            const selectionDiv = currentSelectionBox.querySelector('div:last-child');
            if (selectionDiv) {
                selectionDiv.innerHTML = currentSelectionHtml || '<span class="text-muted small">No attributes selected</span>';
            }
        }
    }
    
    function updateProductPrice(selector) {
        const index = selector.dataset.index;
        const row = selector.closest('tr');
        const qtyInput = row.querySelector('.product-qty');
        
        // Calculate new attribute price
        let attributePrice = 0;
        const productSelectors = row.querySelectorAll('.attribute-selector, .attribute_option');
        
        productSelectors.forEach(sel => {
            if (sel.value) {
                const option = sel.querySelector(`option[value="${sel.value}"]`);
                if (option) {
                    // Check for both data-price and data-target (from product page style)
                    const price = option.dataset.price || option.dataset.target || 0;
                    attributePrice += parseFloat(price);
                }
            }
        });
        
        // Get main price from hidden input
        const mainPriceInput = row.querySelector('input[name*="[main_price]"]');
        const mainPrice = mainPriceInput ? parseFloat(mainPriceInput.value) : 0;
        
        // Update unit price
        const newUnitPrice = mainPrice + attributePrice;
        qtyInput.dataset.price = newUnitPrice;
        
        // Update price display
        const unitPriceDisplay = row.querySelector('td:nth-child(4) strong');
        if (unitPriceDisplay) {
            unitPriceDisplay.textContent = '৳' + newUnitPrice.toFixed(2);
        }
        
        // Update hidden attribute price input
        const attributePriceInput = row.querySelector('input[name*="[attribute_price]"]');
        if (attributePriceInput) {
            attributePriceInput.value = attributePrice;
        }
        
        // Update line total
        updateLineTotal(qtyInput);
        updateCartSubtotal();
    }
    
    function updateLineTotal(qtyInput) {
        const qty = parseInt(qtyInput.value) || 1;
        const price = parseFloat(qtyInput.dataset.price) || 0;
        const lineTotal = qty * price;
        
        const row = qtyInput.closest('tr');
        const lineTotalElement = row.querySelector('.line-total');
        lineTotalElement.textContent = '৳' + lineTotal.toFixed(2);
    }
    // Expose to global so other scripts can call it (override any stub)
    window.updateLineTotal = updateLineTotal;
    
    function updateCartSubtotal() {
        let subtotal = 0;
        
        // Get all product rows that are still in the table
        const productRows = document.querySelectorAll('tbody tr');
        
        productRows.forEach(row => {
            const qtyInput = row.querySelector('.product-qty');
            if (qtyInput) {
                const qty = parseInt(qtyInput.value) || 1;
                const price = parseFloat(qtyInput.dataset.price) || 0;
                subtotal += qty * price;
            }
        });
        
        // Update subtotal
        const subtotalElement = document.getElementById('cart-subtotal');
        if (subtotalElement) {
            subtotalElement.textContent = '৳' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
        
        // Calculate and update total (subtotal + tax + shipping)
        let tax = 0;
        let shipping = 0;
        
        // Get tax amount (if exists)
        const taxRows = document.querySelectorAll('tfoot tr');
        taxRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 2) {
                const labelCell = cells[cells.length - 2];
                const valueCell = cells[cells.length - 1];
                
                if (labelCell.textContent.includes('Tax')) {
                    const taxText = valueCell.querySelector('strong').textContent;
                    tax = parseFloat(taxText.replace('৳', '').replace(',', '')) || 0;
                }
            }
        });
        
        // Get shipping cost from hidden input (more accurate than parsing display text)
        const shippingInput = document.getElementById('pathao_shipping_cost');
        if (shippingInput) {
            shipping = parseFloat(shippingInput.value) || 0;
        }
        
        const total = subtotal + tax + shipping;
        
        // Update total
        const totalElement = document.getElementById('cart-total');
        if (totalElement) {
            totalElement.textContent = '৳' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
        
        console.log('Subtotal:', subtotal, 'Tax:', tax, 'Shipping:', shipping, 'Total:', total);
    }
    // Expose to global so other scripts (Pathao callbacks) call the same implementation (override stub)
    window.updateCartSubtotal = updateCartSubtotal;
    
    // Handle product removal
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-product')) {
            const button = e.target.closest('.remove-product');
            const row = button.closest('tr');
            
            // Show confirmation dialog
            if (confirm('Are you sure you want to remove this product from the order?')) {
                // Remove the row from the table
                row.remove();
                
                // Update subtotal after removal
                updateCartSubtotal();
                
                // Show success message
                alert('Product removed successfully!');
            }
        }
        
        // Handle product change button
        if (e.target.closest('.change-product')) {
            const button = e.target.closest('.change-product');
            const productSelector = button.closest('td').querySelector('.product-selector');
            const select = productSelector.querySelector('.product-change-select');
            
            // Hide button and show selector
            button.style.display = 'none';
            productSelector.style.display = 'block';
            
            // Initialize Select2 for the newly shown select
            if (typeof $.fn.select2 !== 'undefined') {
                $(select).select2({
                    width: '100%',
                    placeholder: 'Search for a product...'
                });
            }
        }
        
        // Handle product change confirmation
        if (e.target.closest('.confirm-product-change')) {
            const button = e.target.closest('.confirm-product-change');
            const index = button.dataset.index;
            const row = button.closest('tr');
            const select = row.querySelector('.product-change-select');
            
            // Get selected option value - handle both regular select and Select2
            let selectedValue = select.value;
            if (typeof $.fn.select2 !== 'undefined' && $(select).hasClass('select2-hidden-accessible')) {
                selectedValue = $(select).val();
            }
            
            const selectedOption = select.querySelector(`option[value="${selectedValue}"]`);
            
            if (!selectedValue || !selectedOption) {
                alert('Please select a product first!');
                return;
            }

            // Fetch updated product details (price + attributes)
            fetch(`${window.location.origin}/admin/order/product-details/${selectedValue}`)
                .then(r => r.json())
                .then(data => {
                    if(data.error){
                        alert(data.error);
                        return;
                    }
                    const newName = data.name;
                    const newPrice = parseFloat(data.price);
                    const newPhoto = data.photo;
                    const attrs = data.attributes || [];

                    // Update display
                    const nameElement = row.querySelector('strong');
                    const imageElement = row.querySelector('img');
                    const qtyInput = row.querySelector('.product-qty');
                    nameElement.textContent = newName;
                    if(imageElement){ imageElement.src=newPhoto; imageElement.alt=newName; }
                    qtyInput.dataset.price = newPrice;

                    // Ensure hidden id input exists and set value
                    let idInput = row.querySelector('input[name^="products"][name$="[id]"]');
                    if(!idInput){
                        idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = `products[${index}][id]`;
                        row.querySelector('.product-qty').insertAdjacentElement('afterend', idInput);
                    }
                    idInput.value = data.id;

                    // Update unit price cell immediately
                    const unitPriceCell = row.querySelector('td:nth-child(4) strong');
                    if(unitPriceCell){ unitPriceCell.textContent = '৳'+ newPrice.toFixed(2); }

                    // Reset attribute price hidden input
                    const attrPriceInput = row.querySelector('input[name*="[attribute_price]"]');
                    if(attrPriceInput) attrPriceInput.value = 0;

                    // Update hidden base price
                    const priceField = row.querySelector('input[name*="[main_price]"]');
                    if(priceField) priceField.value = newPrice;
                    const nameInput = row.querySelector('input[name*="[name]"]');
                    if(nameInput) nameInput.value = newName;

                    // Rebuild attributes cell selects
                    const attrCell = row.querySelector('td:nth-child(2)');
                    if(attrCell){
                        if(attrs.length){
                            let html = '<div class="attribute-selection">';
                            html += '<div class="current-attributes mb-2 p-2 bg-light border rounded"><strong class="text-primary d-block mb-1">Current Selection:</strong><div class="current-attributes-display text-muted small">None selected</div></div>';
                            html += '<div class="row">';
                            attrs.forEach(a => {
                                html += `<div class="col-sm-6"><div class="form-group"><label class="small mb-1" for="order_attr_${a.id}_${index}">${a.name}</label><select class="form-control form-control-sm attribute_option" id="order_attr_${a.id}_${index}" name="products[${index}][attributes][${a.name.toLowerCase()}]" data-type="${a.id}" data-index="${index}"><option value="">Select ${a.name}</option>`;
                                a.options.forEach(o => {
                                    html += `<option value="${o.name}" data-type="${a.id}" data-href="${o.id}" data-target="${o.price}">${o.name}</option>`;
                                });
                                html += '</select></div></div>';
                            });
                            html += '</div></div>';
                            attrCell.innerHTML = html;
                        } else {
                            attrCell.innerHTML = '<span class="text-muted small">No attributes available</span>';
                        }
                    }

                    // Re-bind events for new selects
                    attrCell.querySelectorAll('.attribute_option').forEach(sel => {
                        sel.addEventListener('change', function(){
                            updateProductAttributes(this); updateProductPrice(this);
                        });
                    });

                    // Update totals
                    updateLineTotal(qtyInput);
                    updateCartSubtotal();

                    // Cleanup selector UI
                    if (typeof $.fn.select2 !== 'undefined' && $(select).hasClass('select2-hidden-accessible')) {
                        $(select).select2('destroy');
                    }
                    const productSelector = button.closest('.product-selector');
                    const changeButton = row.querySelector('.change-product');
                    productSelector.style.display='none';
                    changeButton.style.display='inline-block';
                    alert('Product changed successfully!');
                })
                .catch(()=> alert('Failed to load product details'));
        }
        
        // Handle product change cancellation
        if (e.target.closest('.cancel-product-change')) {
            const button = e.target.closest('.cancel-product-change');
            const row = button.closest('tr');
            const productSelector = button.closest('.product-selector');
            const changeButton = row.querySelector('.change-product');
            const select = productSelector.querySelector('.product-change-select');
            
            // Destroy Select2 if it exists
            if (typeof $.fn.select2 !== 'undefined' && $(select).hasClass('select2-hidden-accessible')) {
                $(select).select2('destroy');
            }
            
            // Hide selector and show button
            productSelector.style.display = 'none';
            changeButton.style.display = 'inline-block';
        }
    });
});

// Pathao Integration for Order Edit
document.addEventListener('DOMContentLoaded', function() {
    // Wait for jQuery to be available
    function waitForjQuery(callback) {
        if (typeof $ !== 'undefined' && $.fn) {
            callback();
        } else {
            setTimeout(function() { waitForjQuery(callback); }, 100);
        }
    }
    
    waitForjQuery(function() {
        const shipping = @json($shipping);
        console.log('Shipping data:', shipping);
        
        // Initialize Select2 for Pathao dropdowns
        initializeSelect2();
        
        // Load cities and set up the cascade
        loadPathaoCities();
        
        // Set up event handlers
        setupPathaoEventHandlers();
        
        // function initializeSelect2() {
        //     if ($.fn.select2) {
        //         $('#pathao_city_id, #pathao_zone_id, #pathao_area_id').select2({ 
        //             width: '100%',
        //             placeholder: function() {
        //                 return $(this).data('placeholder') || 'Select';
        //             },
        //             allowClear: true,
        //             minimumResultsForSearch: Infinity,
        //             language: {
        //                 noResults: function() {
        //                     return "কোনো ফলাফল পাওয়া যায়নি";
        //                 }
        //             }
        //         });
        //     }
        // }
        
        // function setupPathaoEventHandlers() {
        //     // Load zones when city changes
        //     $('#pathao_city_id').change(function() {
        //         const cityId = $(this).val();
        //         $('#pathao_zone_id').html('<option value="">Select Zone</option>');
        //         $('#pathao_area_id').html('<option value="">Select Area</option>');
        //         $('#pathao_shipping_cost').val('');
        //         if(typeof updateCartSubtotal === 'function') updateCartSubtotal();
                
        //         if(cityId) {
        //             loadPathaoZones(cityId);
        //         }
        //     });
            
        //     // Load areas when zone changes
        //     $('#pathao_zone_id').change(function() {
        //         const zoneId = $(this).val();
        //         $('#pathao_area_id').html('<option value="">Select Area</option>');
        //         $('#pathao_shipping_cost').val('');
        //         if(typeof updateCartSubtotal === 'function') updateCartSubtotal();
                
        //         if(zoneId) {
        //             loadPathaoAreas(zoneId);
        //             calculatePathaoPrice();
        //         }
        //     });
            
        //     // Calculate price when area changes
        //     $('#pathao_area_id').change(function() {
        //         calculatePathaoPrice();
        //     });
        // }
        
        // function loadPathaoCities() {
        //     $.get('{{ route("front.pathao.cities") }}')
        //     .done(function(cities) {
        //         console.log('Cities response:', cities);
        //         if(Array.isArray(cities) && cities.length > 0) {
        //             let options = '<option value="">Select City</option>';
        //             cities.forEach(city => {
        //                 const selected = shipping.pathao_city_id == city.city_id ? 'selected' : '';
        //                 options += `<option value="${city.city_id}" ${selected}>${city.city_name}</option>`;
        //             });
        //             $('#pathao_city_id').html(options);
                    
        //             // Load zones if city is pre-selected
        //             if(shipping.pathao_city_id) {
        //                 loadPathaoZones(shipping.pathao_city_id);
        //             }
        //         } else {
        //             console.error('Invalid cities response:', cities);
        //         }
        //     })
        //     .fail(function(xhr, status, error) {
        //         console.error('Cities API error:', { xhr, status, error });
        //         if (typeof toastr !== 'undefined') {
        //             toastr.error('Failed to load cities');
        //         }
        //     });
        // }
        
        // function loadPathaoZones(cityId) {
        //     $.get('{{ route("front.pathao.zones") }}', {city_id: cityId})
        //     .done(function(zones) {
        //         console.log('Zones response:', zones);
        //         if(Array.isArray(zones) && zones.length > 0) {
        //             let options = '<option value="">Select Zone</option>';
        //             zones.forEach(zone => {
        //                 const selected = shipping.pathao_zone_id == zone.zone_id ? 'selected' : '';
        //                 options += `<option value="${zone.zone_id}" ${selected}>${zone.zone_name}</option>`;
        //             });
        //             $('#pathao_zone_id').html(options);
                    
        //             // Load areas if zone is pre-selected
        //             if(shipping.pathao_zone_id) {
        //                 loadPathaoAreas(shipping.pathao_zone_id);
        //             }
        //         } else {
        //             console.error('Invalid zones response:', zones);
        //         }
        //     })
        //     .fail(function(xhr, status, error) {
        //         console.error('Zones API error:', { xhr, status, error });
        //         if (typeof toastr !== 'undefined') {
        //             toastr.error('Failed to load zones');
        //         }
        //     });
        // }
        
        // function loadPathaoAreas(zoneId) {
        //     $.get('{{ route("front.pathao.areas") }}', {zone_id: zoneId})
        //     .done(function(areas) {
        //         console.log('Areas response:', areas);
        //         if(Array.isArray(areas) && areas.length > 0) {
        //             let options = '<option value="">Select Area</option>';
        //             areas.forEach(area => {
        //                 const selected = shipping.pathao_area_id == area.area_id ? 'selected' : '';
        //                 options += `<option value="${area.area_id}" ${selected}>${area.area_name}</option>`;
        //             });
        //             $('#pathao_area_id').html(options);
                    
        //             // Calculate price if area is pre-selected
        //             if(shipping.pathao_area_id) {
        //                 calculatePathaoPrice();
        //             }
        //         } else {
        //             console.error('Invalid areas response:', areas);
        //         }
        //     })
        //     .fail(function(xhr, status, error) {
        //         console.error('Areas API error:', { xhr, status, error });
        //         if (typeof toastr !== 'undefined') {
        //             toastr.error('Failed to load areas');
        //         }
        //     });
        // }
        
        // function calculatePathaoPrice() {
        //     const cityId = $('#pathao_city_id').val();
        //     const zoneId = $('#pathao_zone_id').val();
        //     const areaId = $('#pathao_area_id').val();

        //     if(cityId && zoneId) {
        //         const params = { city_id: cityId, zone_id: zoneId };
        //         if(areaId) params.area_id = areaId;

        //         $.get('{{ route("front.pathao.price") }}', params)
        //         .done(function(response) {
        //             console.log('Pathao price response (edit):', response);
        //             // Normalize a few possible response shapes to extract a numeric price
        //             let price = null;
        //             if(response === null || response === undefined) {
        //                 price = null;
        //             } else if(typeof response === 'number') {
        //                 price = response;
        //             } else if(response.price !== undefined) {
        //                 price = response.price;
        //             } else if(response.data && response.data.price !== undefined) {
        //                 price = response.data.price;
        //             } else if(response.amount !== undefined) {
        //                 price = response.amount;
        //             }

        //             price = parseFloat(price) || 0;
        //                 // Set hidden input value
        //                 $('#pathao_shipping_cost').val(price);
        //                 // Trigger change in case other handlers listen for it
        //                 try { $('#pathao_shipping_cost').trigger('change'); } catch(e){}
        //                 // Update display text
        //                 $('#pathao_shipping_display').text('৳' + price.toFixed(2));
        //                 // Recalculate total - call the global explicitly to avoid scoping issues
        //                 if(typeof window.updateCartSubtotal === 'function') {
        //                     window.updateCartSubtotal();
        //                 } else if(typeof updateCartSubtotal === 'function') {
        //                     updateCartSubtotal();
        //                 }
        //                 console.log('Pathao shipping cost set to:', price);
        //         })
        //         .fail(function(xhr, status, error) {
        //             console.error('Pathao price calculation error:', { xhr, status, error });
        //             if (typeof toastr !== 'undefined') {
        //                 toastr.error('Failed to calculate shipping price');
        //             }
        //         });
        //     }
        // }
    });
});
</script>
@endsection
