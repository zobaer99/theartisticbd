@php
    $cart = Session::has('cart') ? Session::get('cart') : [];
    $total = 0;
    $option_price = 0;
    $cartTotal = 0;
    
@endphp

{{-- Cart responsive styles moved to styles.min.css --}}
<style>
/* Cart attribute change styling */
.attribute-row, .mobile-attribute-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 5px;
    margin-bottom: 8px;
}

.attribute-label, .mobile-attr-label {
    min-width: 50px;
    font-weight: 500;
    font-size: 0.9em;
}

.attribute-change-select, .mobile-attribute-change-select {
    flex: 1;
    min-width: 100px;
    max-width: 150px;
    font-size: 0.85em;
}

.change-attribute-btn, .mobile-change-attribute-btn {
    white-space: nowrap;
    font-size: 0.75em;
}

.product-attributes {
    margin-top: 8px;
}

@media (max-width: 768px) {
    .mobile-attribute-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 3px;
    }
    
    .mobile-attribute-change-select {
        width: 100% !important;
        max-width: none !important;
    }
}
</style>

<div class="card border-0">
    <div class="card-body">
    <div class="table-responsive shopping-cart">
            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>{{ __('Product Name') }}</th>
                        <th>{{ __('Product Price') }}</th>
                        <th class="text-center">{{ __('Quantity') }}</th>
                        <th class="text-center">{{ __('Subtotal') }}</th>
                        <th class="text-center"><a class="btn btn-sm btn-primary"
                                href="{{ route('front.cart.clear') }}"><span>{{ __('Clear Cart') }}</span></a></th>
                    </tr>
                </thead>

                <tbody id="cart_view_load" data-target="{{ route('cart.get.load') }}">

                    @foreach ($cart as $key => $item)
                        @php
                            $itemSubtotal = ($item['main_price'] + $item['attribute_price']) * $item['qty'];
                            $cartTotal += $itemSubtotal;
                        @endphp
                        <tr>
                            <td>
                                <div class="product-item">
                                    <a class="product-thumb" href="{{ route('front.product', $item['slug']) }}">
                                        <img src="{{ url('/storage/images/' . $item['photo']) }}" alt="Product">
                                    </a>
                                    <div class="product-info">
                                        <h4 class="product-title"><a href="{{ route('front.product', $item['slug']) }}">
                                                {{ Str::limit($item['name'], 45) }}

                                            </a></h4>

                                        {{-- Current attributes display --}}
                                        <div class="product-attributes">
                                            @foreach ($item['attribute']['option_name'] as $optionkey => $option_name)
                                                <div class="attribute-row mb-2">
                                                    <span class="attribute-label"><em>{{ $item['attribute']['names'][$optionkey] }}:</em></span>
                                                    
                                                    {{-- Change attribute dropdown --}}
                                                    <select class="form-control form-control-sm d-inline-block attribute-change-select" 
                                                            data-cart-key="{{ $key }}" 
                                                            data-attribute-name="{{ $item['attribute']['names'][$optionkey] }}"
                                                            data-current-option="{{ $option_name }}"
                                                            style="width: auto; max-width: 150px;">
                                                        <option value="{{ $option_name }}" selected>{{ $option_name }}</option>
                                                        {{-- Additional options will be loaded via AJAX --}}
                                                    </select>
                                                    
                                                    {{-- Change button --}}
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-primary ml-1 change-attribute-btn"
                                                            data-cart-key="{{ $key }}" 
                                                            data-product-id="{{ $item['id'] ?? $item['item_id'] ?? $item['product_id'] ?? '' }}"
                                                            data-attribute-index="{{ $optionkey }}"
                                                            style="padding: 2px 8px; font-size: 11px;">
                                                        <i class="fas fa-edit"></i> Change
                                                    </button>
                                                    
                                                    {{-- Debug button - remove after testing --}}
                                                    {{-- <button type="button" 
                                                            class="btn btn-sm btn-outline-info ml-1 test-load-options"
                                                            data-cart-key="{{ $key }}" 
                                                            data-attribute-name="{{ $item['attribute']['names'][$optionkey] }}"
                                                            style="padding: 2px 8px; font-size: 11px;">
                                                        <i class="fas fa-bug"></i> Test
                                                    </button> --}}
                                                    
                                                    {{-- Debug info --}}
                                                    {{-- <small class="text-muted d-block">
                                                        @php
                                                            $available_keys = array_keys($item);
                                                            echo 'Keys: ' . implode(', ', $available_keys);
                                                        @endphp
                                                    </small> --}}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center text-lg" data-label="{{ __('Product Price') }}">
                                @php
                                    $totalAttributePrice = 0;
                                    foreach ($item['attribute']['option_price'] as $option_price) {
                                        $totalAttributePrice += $option_price;
                                    }
                                    $price = $item['main_price'] + $totalAttributePrice;
                                @endphp

                                
                                {{ PriceHelper::setCurrencyPrice($price) }}
                            </td>

                            <td class="text-center" data-label="{{ __('Quantity') }}">
                                @if ($item['item_type'] == 'normal')
                                    <div class="qtySelector product-quantity">
                                        <span class="decreaseQtycart cartsubclick" data-id="{{ $key }}"
                                            data-target="{{ PriceHelper::GetItemId($key) }}"><i
                                                class="fas fa-minus"></i></span>
                                        <input type="text" disabled class="qtyValue cartcart-amount"
                                            value="{{ $item['qty'] }}">
                                        <span class="increaseQtycart cartaddclick" data-id="{{ $key }}"
                                            data-target="{{ PriceHelper::GetItemId($key) }}"
                                            data-item="{{ implode(',', $item['options_id']) }}"><i
                                                class="fas fa-plus"></i></span>
                                        <input type="hidden" value="3333" id="current_stock">
                                    </div>
                                @endif

                            </td>
                            <td class="text-center text-lg" data-label="{{ __('Subtotal') }}">
                                {{ PriceHelper::setCurrencyPrice($price * $item['qty']) }}</td>

                            <td class="text-center" data-label="{{ __('Remove') }}"><a class="remove-from-cart"
                                    href="{{ route('front.cart.destroy', $key) }}" data-toggle="tooltip"
                                    title="Remove item"><i class="icon-x"></i></a></td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Mobile Card Layout --}}
<div class="cart-mobile d-md-none">
    <div class="cart-mobile-header">{{ __('My Cart') }} ({{ count($cart) }})</div>
    @foreach($cart as $key => $item)
        @php
            $totalAttributePrice = 0;
            foreach ($item['attribute']['option_price'] as $option_price) { $totalAttributePrice += $option_price; }
            $price = $item['main_price'] + $totalAttributePrice;
            $oldPrice = isset($item['previous_price']) && $item['previous_price'] > $price ? $item['previous_price'] : null;
            $discountPercent = $oldPrice ? round((($oldPrice - $price)/$oldPrice)*100) : null;
        @endphp
        <div class="cart-mobile-item">
            <div class="cm-left">
                <h5 class="cm-title"><a href="{{ route('front.product',$item['slug']) }}">{{ Str::limit($item['name'],60) }}</a></h5>
                <div class="cm-prices">
                    <span class="cm-price">{{ PriceHelper::setCurrencyPrice($price) }}</span>
                    @if($oldPrice)
                        <span class="cm-old">{{ PriceHelper::setCurrencyPrice($oldPrice) }}</span>
                        <span class="cm-badge">-{{ $discountPercent }}%</span>
                    @endif
                </div>
                <div class="cm-attr">
                    @foreach ($item['attribute']['option_name'] as $optionkey => $option_name)
                        <div class="mobile-attribute-row mb-1">
                            <span class="mobile-attr-label"><em>{{ $item['attribute']['names'][$optionkey] }}:</em></span>
                            
                            {{-- Mobile attribute change dropdown --}}
                            <select class="form-control form-control-sm d-inline-block mobile-attribute-change-select" 
                                    data-cart-key="{{ $key }}" 
                                    data-attribute-name="{{ $item['attribute']['names'][$optionkey] }}"
                                    data-current-option="{{ $option_name }}"
                                    style="width: auto; max-width: 120px; margin-left: 5px;">
                                <option value="{{ $option_name }}" selected>{{ $option_name }}</option>
                            </select>
                            
                            {{-- Mobile change button --}}
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary ml-1 mobile-change-attribute-btn"
                                    data-cart-key="{{ $key }}" 
                                    data-product-id="{{ $item['id'] ?? '' }}"
                                    data-attribute-index="{{ $optionkey }}"
                                    style="padding: 1px 6px; font-size: 10px;">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
                <div class="cm-actions">
                    <a href="{{ route('front.cart.destroy',$key) }}" class="btn btn-outline-danger btn-sm">{{ __('Remove') }}</a>
                </div>
            </div>
            <div class="cm-right">
                <a class="cm-thumb" href="{{ route('front.product',$item['slug']) }}">
                    <img src="{{ url('/storage/images/' . $item['photo']) }}" alt="{{ $item['name'] }}">
                </a>
                @if ($item['item_type'] == 'normal')
                    <label class="cm-qty-label">{{ __('Quantity') }}:</label>
                    <div class="qtySelector product-quantity cm-qty">
                        <span class="decreaseQtycart cartsubclick" data-id="{{ $key }}" data-target="{{ PriceHelper::GetItemId($key) }}"><i class="fas fa-minus"></i></span>
                        <input type="text" disabled class="qtyValue cartcart-amount" value="{{ $item['qty'] }}">
                        <span class="increaseQtycart cartaddclick" data-id="{{ $key }}" data-target="{{ PriceHelper::GetItemId($key) }}" data-item="{{ implode(',', $item['options_id']) }}"><i class="fas fa-plus"></i></span>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    {{-- Order Summary Accordion --}}
    {{-- <div class="cart-mobile-sections">
        <div class="cms-item" data-toggle="cms-collapse">
            <div class="cms-head">{{ __('Order Summary') }}<span class="chevron"></span></div>
            <div class="cms-body">
                <div class="cms-row"><span>{{ __('Subtotal') }} ({{ count($cart) }} {{ __('item') }})</span><span>{{ PriceHelper::setCurrencyPrice($cartTotal) }}</span></div>
                @php $discount = Session::has('coupon') ? Session::get('coupon')['discount'] : 0; @endphp
                @if($discount > 0)
                    <div class="cms-row"><span>{{ __('Discount') }}</span><span>-{{ PriceHelper::setCurrencyPrice($discount) }}</span></div>
                @endif
                <div class="cms-row"><span>{{ __('Shipping') }}</span><span>—</span></div>
                <div class="cms-row cms-total"><span>{{ __('Total') }}</span><span>{{ PriceHelper::setCurrencyPrice($cartTotal - $discount) }}</span></div>
            </div>
        </div>
        <div class="cms-item" data-toggle="cms-collapse">
            <div class="cms-head">{{ __('Apply Discount Code') }}<span class="chevron"></span></div>
            <div class="cms-body">
                <form class="coupon-form" method="post" id="coupon_form_mobile" action="{{ route('front.promo.submit') }}">
                    @csrf
                    <div class="input-group input-group-sm mb-2">
                        <input class="form-control" name="code" type="text" placeholder="{{ __('Coupon code') }}" required>
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">{{ __('Apply') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="cms-item" data-toggle="cms-collapse">
            <div class="cms-head">{{ __('Use Club Points') }}<span class="chevron"></span></div>
            <div class="cms-body small text-muted">{{ __('Feature coming soon.') }}</div>
        </div>
    </div> --}}

    <div class="cart-mobile-sticky">
        <div class="cms-total-price">{{ PriceHelper::setCurrencyPrice($cartTotal - (Session::has('coupon') ? Session::get('coupon')['discount'] : 0)) }}</div>
        <a class="btn btn-primary btn-block" href="{{ route('front.checkout.billing') }}">{{ __('Proceed To Checkout') }}</a>
    </div>
</div>


<div class="card border-0 mt-4">
    <div class="card-body">
        <div class="shopping-cart-footer">
            <div class="column">
                <form class="coupon-form" method="post" id="coupon_form" action="{{ route('front.promo.submit') }}">
                    @csrf
                    <input class="form-control form-control-sm" name="code" type="text"
                        placeholder="{{ __('Coupon code') }}" required>
                    <button class="btn btn-primary btn-sm"
                        type="submit"><span>{{ __('Apply Coupon') }}</span></button>
                </form>
            </div>

            <div class="text-right text-lg column {{ Session::has('coupon') ? '' : 'd-none' }}"><span
                    class="text-muted">{{ __('Discount') }}
                    ({{ Session::has('coupon') ? Session::get('coupon')['code']['title'] : '' }}) : </span><span
                    class="text-gray-dark">{{ PriceHelper::setCurrencyPrice(Session::has('coupon') ? Session::get('coupon')['discount'] : 0) }}</span>
                    <a class="remove-from-cart btn btn-danger btn-sm "
                                    href="{{ route('front.promo.destroy') }}" data-toggle="tooltip"
                                    title="Remove item"><i class="icon-x"></i></a>
            </div>

            <div class="text-right column text-lg"><span class="text-muted">{{ __('Subtotal') }}: </span><span
                    class="text-gray-dark">{{ PriceHelper::setCurrencyPrice($cartTotal - (Session::has('coupon') ? Session::get('coupon')['discount'] : 0)) }}</span>
            </div>


        </div>
        <div class="shopping-cart-footer">
            <div class="column"><a class="btn btn-primary " href="{{ route('front.catalog') }}"><span><i
                            class="icon-arrow-left"></i> {{ __('Back to Shopping') }}</span></a></div>
            <div class="column"><a class="btn btn-primary"
                    href="{{ route('front.checkout.billing') }}"><span>{{ __('Checkout') }}</span></a></div>
        </div>
    </div>
</div>
</div>

<script>
// Wait for jQuery to be available and then initialize
(function() {
    function initializeCartFunctionality() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            setTimeout(initializeCartFunctionality, 100);
            return;
        }
        
        $(document).ready(function() {
            console.log('Cart include script loaded', { path: window.location.pathname });
            
            // Cart attribute change functionality
            
            // Handle attribute change button clicks
            $(document).on('click', '.change-attribute-btn, .mobile-change-attribute-btn', function(e) {
        e.preventDefault();
        
        const cartKey = $(this).data('cart-key');
        const productId = $(this).data('product-id');
        const attributeIndex = $(this).data('attribute-index');
        const $select = $(this).siblings('.attribute-change-select, .mobile-attribute-change-select');
        const newValue = $select.val();
        const currentValue = $select.data('current-option');
        
        if (newValue === currentValue) {
            if (typeof toastr !== 'undefined') {
                toastr.info('Same option selected', 'No changes made');
            }
            return;
        }
        
        // Show loading state
        $(this).html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        
        // Make AJAX request to update cart attribute
        $.ajax({
            url: '{{ route("cart.update.attribute") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                cart_key: cartKey,
                attribute_index: attributeIndex,
                new_option: newValue,
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Attribute updated successfully!', 'Cart Updated');
                    }
                    
                    // Reload the page to update totals
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    // Show error message
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Failed to update attribute', 'Update Failed');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Cart attribute update error:', error);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to update attribute. Please try again.', 'Error');
                }
            },
            complete: function() {
                // Reset button state
                $('.change-attribute-btn, .mobile-change-attribute-btn').html('<i class="fas fa-edit"></i> Change').prop('disabled', false);
                $('.mobile-change-attribute-btn').html('<i class="fas fa-edit"></i>');
            }
        });
    });
    
    // Load available attribute options when dropdown is focused
    $(document).on('focus', '.attribute-change-select, .mobile-attribute-change-select', function() {
        const $select = $(this);
        const cartKey = $select.data('cart-key');
        const attributeName = $select.data('attribute-name');
        const currentValue = $select.val();
        
        // Only load options if not already loaded (check if only one option exists)
        if ($select.find('option').length <= 1) {
            console.log('Loading cart attribute options for:', attributeName);
            
            // Show loading state
            $select.append('<option value="">Loading...</option>');
            
            // Make AJAX request to get available options
            $.ajax({
                url: '{{ route("cart.get.attribute.options") }}',
                type: 'GET',
                data: {
                    cart_key: cartKey,
                    attribute_name: attributeName
                },
                success: function(response) {
                    console.log('Cart attribute options response:', response);
                    if (response.success && response.options) {
                        // Clear existing options
                        $select.empty();
                        
                        // Add new options
                        response.options.forEach(function(option) {
                            const isSelected = option.name === currentValue ? 'selected' : '';
                            const priceText = option.price > 0 ? ` (+${option.price})` : '';
                            $select.append(`<option value="${option.name}" ${isSelected}>${option.name}${priceText}</option>`);
                        });
                        
                        console.log('Loaded', response.options.length, 'cart options for', attributeName);
                    } else {
                        console.error('Failed to load cart options:', response.message);
                        // Restore original option if loading fails
                        $select.empty().append(`<option value="${currentValue}" selected>${currentValue}</option>`);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load cart attribute options:', error);
                    // Restore original option if loading fails
                    $select.empty().append(`<option value="${currentValue}" selected>${currentValue}</option>`);
                }
            });
        }
    });
    
    // Pre-load cart attribute options when page loads
    $('.attribute-change-select, .mobile-attribute-change-select').each(function() {
        const $select = $(this);
        const cartKey = $select.data('cart-key');
        const attributeName = $select.data('attribute-name');
        const currentValue = $select.val();
        
        console.log('Pre-loading cart options for:', attributeName);
        
        $.ajax({
            url: '{{ route("cart.get.attribute.options") }}',
            type: 'GET',
            data: {
                cart_key: cartKey,
                attribute_name: attributeName
            },
            success: function(response) {
                if (response.success && response.options) {
                    // Clear existing options
                    $select.empty();
                    
                    // Add new options
                    response.options.forEach(function(option) {
                        const isSelected = option.name === currentValue ? 'selected' : '';
                        const priceText = option.price > 0 ? ` (+${option.price})` : '';
                        $select.append(`<option value="${option.name}" ${isSelected}>${option.name}${priceText}</option>`);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to pre-load cart attribute options:', error);
            }
        });
    });
    
    // Test button to manually load options for debugging
    $(document).on('click', '.test-load-options', function(e) {
        e.preventDefault();
        
        const cartKey = $(this).data('cart-key');
        const attributeName = $(this).data('attribute-name');
        
        console.log('Testing cart attribute loading:', { cartKey, attributeName });
        
        $.ajax({
            url: '{{ route("cart.get.attribute.options") }}',
            type: 'GET',
            data: {
                cart_key: cartKey,
                attribute_name: attributeName
            },
            success: function(response) {
                console.log('Cart test response received:', response);
                if (typeof toastr !== 'undefined') {
                    toastr.success('Found ' + (response.options ? response.options.length : 0) + ' options. Check console!', 'Cart Test Success');
                } else {
                    alert('Success! Found ' + (response.options ? response.options.length : 0) + ' options. Check console for details.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Cart test failed:', { xhr, status, error });
                if (typeof toastr !== 'undefined') {
                    toastr.error('Test failed: ' + error, 'Cart Test Error');
                } else {
                    alert('Test failed: ' + error + '. Check console for details.');
                }
            }
        });
    });

        }); // End of $(document).ready
    } // End of initializeCartFunctionality
    
    // Start the initialization
    initializeCartFunctionality();
})(); // End of IIFE
</script>
