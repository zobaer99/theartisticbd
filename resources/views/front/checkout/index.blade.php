@extends('master.front')

@section('title')
    {{ __('Billing') }}
@endsection

@section('css')
<style>
/* Enhanced Bootstrap select styling */
.form-control.enhanced-select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

.form-control.enhanced-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    outline: 0;
}

/* Attribute change styling */
.checkout-attribute-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.checkout-attr-label {
    min-width: 60px;
    font-weight: 500;
}

.checkout-attribute-change-select {
    flex: 1;
    min-width: 120px;
}

.checkout-change-attribute-btn {
    white-space: nowrap;
}

/* Cart attribute styling */
.attribute-row, .mobile-attribute-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 5px;
}

.attribute-label, .mobile-attr-label {
    min-width: 50px;
    font-weight: 500;
}

@media (max-width: 768px) {
    .checkout-attribute-row {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .checkout-attribute-change-select {
        width: 100% !important;
        max-width: none !important;
    }
}
</style>
@endsection

@section('content')
    <!-- Page Title-->
    <div class="page-title">
        <div class="container">
            <div class="column">
                <ul class="breadcrumbs">
                    <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a> </li>
                    <li class="separator"></li>
                    <li>{{ __('Billing address') }}</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Page Content-->
    <div class="container padding-bottom-3x mb-1 checkut-page">
        <div class="row">
            <div class="col-xl-8 col-lg-8">
                <div class="row">
                    <div class="col-12">
                        <!-- Cart Items Section -->
                        <section class="card widget widget-featured-posts widget-featured-products p-4">
                            <h3 class="widget-title">{{ __('Items In Your Cart') }}</h3>
                            @foreach ($cart as $key => $item)
                                <div class="entry">
                                    <div class="entry-thumb">
                                        <a href="{{ route('front.product', $item['slug']) }}">
                                            <img src="{{ url('/storage/images/' . $item['photo']) }}" alt="Product">
                                        </a>
                                    </div>
                                    <div class="entry-content">
                                        <h4 class="entry-title">
                                            <a href="{{ route('front.product', $item['slug']) }}">
                                                {{ Str::limit($item['name'], 45) }}
                                            </a>
                                        </h4>
                                        <span class="entry-meta">{{ $item['qty'] }} x
                                            @php
                                                $totalAttributePrice = 0;
                                                foreach ($item['attribute']['option_price'] as $option_price) {
                                                    $totalAttributePrice += $option_price;
                                                }
                                                $price = $item['main_price'] + $totalAttributePrice;
                                            @endphp
                                            {{ PriceHelper::setCurrencyPrice($price) }}
                                        </span>

                                        @foreach ($item['attribute']['option_name'] as $optionkey => $option_name)
                                            <div class="entry-meta checkout-attribute-row mb-2">
                                                <span class="entry-meta d-inline checkout-attr-label">{{ $item['attribute']['names'][$optionkey] }}:</span>
                                                
                                                {{-- Checkout attribute change dropdown --}}
                                                <select class="form-control form-control-sm d-inline-block checkout-attribute-change-select ml-2" 
                                                        data-cart-key="{{ $key }}" 
                                                        data-attribute-name="{{ $item['attribute']['names'][$optionkey] }}"
                                                        data-current-option="{{ $option_name }}"
                                                        style="width: auto; max-width: 150px;">
                                                    <option value="{{ $option_name }}" selected>{{ $option_name }}</option>
                                                </select>
                                                
                                                {{-- Checkout change button --}}
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary ml-1 checkout-change-attribute-btn"
                                                        data-cart-key="{{ $key }}" 
                                                        data-product-id="{{ $item['id'] ?? $item['item_id'] ?? $item['product_id'] ?? '' }}"
                                                        data-attribute-index="{{ $optionkey }}"
                                                        style="padding: 2px 8px; font-size: 11px;">
                                                    <i class="fas fa-edit"></i> Change
                                                </button>
                                                
                                                {{-- Debug info - remove after testing --}}
                                                {{-- <small class="text-muted d-block">
                                                    Cart Key: {{ $key }} | 
                                                    @php
                                                        $available_keys = array_keys($item);
                                                        echo 'Available keys: ' . implode(', ', $available_keys);
                                                    @endphp
                                                </small> --}}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </section>

                        <!-- Shipping Address Section -->
                        <div class="card">
                            <div class="card-body">
                                <h6>{{ __('অর্ডার করতে ফর্মটি পূরণ করুন।') }}</h6>
                                <form id="checkoutShipping">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="checkout-name">{{ __('পুরো নাম') }}*</label>
                                                <input class="form-control" name="ship_full_name" type="text" 
                                                       id="checkout-name" value="{{ $user->first_name?? '' }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="checkout-email">{{ __('ই-মেইল ঠিকানা') }}</label>
                                                <input class="form-control" name="ship_email" type="email" 
                                                       id="checkout-email" value="{{ $user->email ?? '' }}" >
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="checkout-phone">{{ __('ফোন নাম্বার') }}*</label>
                                                <input class="form-control number" name="ship_phone" type="text" 
                                                       id="checkout-phone" value="{{ $user->phone ?? '' }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="checkout-address">{{ __('পূর্ণ ঠিকানা') }}(বাড়ি নং/গ্রাম, থানা, জেলা)*</label>
                                                <input class="form-control" name="ship_address" type="text" 
                                                       id="checkout-address" placeholder="Start typing your address" required>
                                            </div>
                                        </div>
                                    </div>
                                        <input type="hidden" name="address_shipping_price" id="address_shipping_amount" value="0">

                                    <!-- Pathao Address Selection -->
                                    {{-- <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>City</label>
                                                <select class="form-control enhanced-select" id="pathao_city_id" name="pathao_city_id" data-placeholder="Select City" required>
                                                    <option value="">Select</option>
                                                    @foreach($pathao_cities as $city)
                                                        <option value="{{ $city['city_id'] }}">{{ $city['city_name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Zone</label>
                                                <select class="form-control select2" id="pathao_zone_id" name="pathao_zone_id" disabled required>
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Area</label>
                                                <select class="form-control select2" id="pathao_area_id" name="pathao_area_id" disabled>
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div> --}}
                                    

                                    {{-- <div class="row d-none">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="checkout-city">{{ __('City') }}</label>
                                                <input class="form-control" name="ship_city" type="text" 
                                                       id="checkout-city" value="{{ $user->ship_city ?? '' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="checkout-zip">{{ __('Zip Code') }}</label>
                                                <input class="form-control" name="ship_zip" type="text" 
                                                       id="checkout-zip" value="{{ $user->ship_zip ?? '' }}" required>
                                            </div>
                                        </div>
                                    </div> --}}
                                </form>
                            </div>
                        </div>

                       
                    </div>
                </div>
            </div>
            
            <!-- Order Summary Sidebar -->
            <div class="col-xl-4 col-lg-4">
                @include('includes.single_checkout_sidebar', $cart)
                @include('includes.single_checkout_modal')
                 <!-- Payment Method Section -->
                        <div class="card mt-5">
                            @include('includes.checkout_payment_section', $cart)
                        </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
$(document).ready(function() {
    console.log('checkout index ready', { path: window.location.pathname });
    
    // Initialize shipping method on page load and calculate initial totals
    if ($('input[name="shipping_id"]:checked').length) {
        $('input[name="shipping_id"]:checked').trigger('change');
    } else {
        // If no shipping method is selected, select the first one by default
        $('input[name="shipping_id"]:first').prop('checked', true).trigger('change');
    }
    
    // Initial total calculation
    recalcTotals();

    // Centralized totals recompute using base + selected shipping + address fee
    function recalcTotals(){
        const baseTotal = {{ $grand_total }};
        const $sel = $('input[name="shipping_id"]:checked');
        const methodPrice = $sel.length ? (parseFloat($sel.data('price')) || 0) : 0;
        const addrAdd = parseFloat($('#address_shipping_amount').val()||'0') || 0;
        const newTotal = baseTotal + methodPrice + addrAdd;
        
        // Format prices properly with currency symbol
        const currencySymbol = '{{ PriceHelper::adminCurrency() }}';
        $('.set__shipping_price').text(currencySymbol + methodPrice.toFixed(2));
        $('.set__shipping_price_tr').removeClass('d-none');
        $('.grand_total_set').text(currencySymbol + newTotal.toFixed(2));
    }

    // Shipping method change handler
    $(document).on('change', 'input[name="shipping_id"]', function() {
        let shippingId = $(this).val();
        recalcTotals();
        let url = $(this).data('href');
        $('.shipping_id_setup').val(shippingId);
    console.log('shipping change ajax', { url: url, data: { shipping_id: shippingId } });
    $.ajax({ url: url, type: 'POST', data: { shipping_id: shippingId, _token: '{{ csrf_token() }}' } }).done(function(resp){ console.log('shipping change response', resp); }).fail(function(err){ console.error('shipping change failed', err); });
    });

    // Terms checkbox handler
    $(document).on("click", "#trams__condition_single", function() {
        if ($(this).is(':checked')) {
            $('.single_checkout_payment').attr('id', "single_checkout_payment").prop('disabled', false);
        } else {
            $('.single_checkout_payment').removeAttr('id').prop('disabled', true);
        }
    });

    // Payment method selection handler - require Division, District and Address
    $(document).on("click", "#single_checkout_payment", function(e) {
        e.preventDefault();
        let keyword = $('input[name="payment_gateway"]:checked').val();
        let shipping_id = $('input[name="shipping_id"]:checked').val();
        let addree = $('#checkout-address').val();

        if (!$('#checkout-name').val()) { 
            toastr.error('নাম প্রয়োজন', 'তথ্য অনুপস্থিত');
            $('#checkout-name').focus();
            return; 
        }
        if (!$('#checkout-phone').val()) { 
            toastr.error('ফোন নাম্বার প্রয়োজন', 'তথ্য অনুপস্থিত');
            $('#checkout-phone').focus();
            return; 
        }
       /* if (!$('#pathao_city_id').val()) { 
            toastr.error('শহর নির্বাচন করুন', 'তথ্য অনুপস্থিত');
            $('#pathao_city_id').focus();
            return; 
        }
        if (!$('#pathao_zone_id').val()) { 
            toastr.error('জোন নির্বাচন করুন', 'তথ্য অনুপস্থিত');
            $('#pathao_zone_id').focus();
            return; 
        }
        if (!addree) { 
            toastr.error('সম্পূর্ণ ঠিকানা লিখুন', 'তথ্য অনুপস্থিত');
            $('#checkout-address').focus();
            return; 
        }*/

        if (!keyword) { 
            toastr.error('পেমেন্ট পদ্ধতি নির্বাচন করুন', 'তথ্য অনুপস্থিত');
            return; 
        }

        // Shipping method required
        if (!shipping_id) {
            toastr.error('শিপিং পদ্ধতি নির্বাচন করুন', 'তথ্য অনুপস্থিত');
            return;
        }

        let modalElement = document.getElementById(keyword);
        if (modalElement) {
            let formData = $('#checkoutShipping').serializeArray();
            console.log('preparing modal form data', formData);
            console.log('preparing modal form data', formData);
            $(modalElement).find('form').append(formData.map(function(input) {
                return $('<input>').attr({ type: 'hidden', name: input.name, value: input.value });
            }));
            let shippingId = $('input[name="shipping_id"]:checked').val();
            $(modalElement).find('.shipping_id_setup').val(shippingId);
            let modal = new bootstrap.Modal(modalElement);
            modal.show();
        }
    });

    // Pathao City change: populate zones and enable zone select
   /* $(document).on('change', '#pathao_city_id', function(){
        const cityId = $(this).val();
        const $z = $('#pathao_zone_id');
        $z.val('').trigger('change');
        $('#pathao_area_id').val('').trigger('change').prop('disabled', true);
        $('.address_shipping_tr').addClass('d-none');
        $('.address_shipping_price').text('0.00');
        $('#address_shipping_amount').val('0');
        recalcTotals();

        if(!cityId){ $z.prop('disabled', true); $('.payment-shipping-option-wrap .shipping-group').show(); return; }
        $.get("{{ route('front.pathao.zones') }}", { city_id: cityId })
            .done(function(zones){
                $z.empty().append('<option value="">Select</option>');
                zones.forEach(function(zone){ $z.append('<option value="'+zone.zone_id+'">'+zone.zone_name+'</option>'); });
                $z.prop('disabled', false).trigger('change');
            });
    });

    // Pathao Zone change: populate areas and compute shipping (area optional)
    $(document).on('change', '#pathao_zone_id', function(){
        const zoneId = $(this).val();
        const cityId = $('#pathao_city_id').val();
        const $a = $('#pathao_area_id');
        $a.prop('disabled', !zoneId).val('').trigger('change');
        $('.address_shipping_tr').addClass('d-none');
        $('.address_shipping_price').text('0.00');
        $('#address_shipping_amount').val('0');
        recalcTotals();

        if(!zoneId) return;
        
        // Get areas
        $.get("{{ route('front.pathao.areas') }}", { zone_id: zoneId })
            .done(function(areas){
                $a.empty().append('<option value="">Select</option>');
                areas.forEach(function(area){ $a.append('<option value="'+area.area_id+'">'+area.area_name+'</option>'); });
                $a.trigger('change');
            });

        // Calculate Pathao shipping price
        if(cityId && zoneId) {
            $.get("{{ route('front.pathao.price') }}", { city_id: cityId, zone_id: zoneId })
                .done(function(resp){
                    console.log('Pathao price response:', resp);
                    if(resp.status === 'success' && resp.price) {
                        $('.address_shipping_price').text(resp.formatted || '৳' + resp.price);
                        $('#address_shipping_amount').val(resp.price);
                        $('.address_shipping_tr').removeClass('d-none');
                        $('.payment-shipping-option-wrap .shipping-group').hide();
                        $('input[name="shipping_id"]').prop('checked', false);
                        recalcTotals();
                    } else {
                        console.error('Pathao price calculation failed:', resp);
                        // Show error message in Bengali
                        if(typeof toastr !== 'undefined') {
                            toastr.error('ডেলিভারি চার্জ গণনা করতে সমস্যা হয়েছে', 'ত্রুটি');
                        }
                        // Fallback to shipping methods if price calculation fails
                        $('.payment-shipping-option-wrap .shipping-group').show();
                    }
                })
                .fail(function(xhr, status, error){
                    console.error('Pathao price calculation error:', { xhr, status, error });
                    // Show error message in Bengali
                    if(typeof toastr !== 'undefined') {
                        toastr.error('ডেলিভারি চার্জ গণনা করতে সমস্যা হয়েছে', 'ত্রুটি');
                    }
                    // Fallback to shipping methods if price calculation fails
                    $('.payment-shipping-option-wrap .shipping-group').show();
                });
        }
    });

    // Pathao Area change: optional shipping calculation
    $(document).on('change', '#pathao_area_id', function(){
        // For now, just enable shipping methods since Pathao doesn't provide shipping rates via API
        $('.payment-shipping-option-wrap .shipping-group').show();
    });

    // Log modal submit clicks and modal form submissions
    $(document).on('click', '.modal button[type="submit"], .modal input[type="submit"]', function(e){
        try {
            var $form = $(this).closest('form');
            
            // Validate Pathao fields before submission
            const cityId = $('#pathao_city_id').val();
            const zoneId = $('#pathao_zone_id').val();
            
            if (!cityId) {
                e.preventDefault();
                toastr.error('অনুগ্রহ করে শহর নির্বাচন করুন', 'শহর প্রয়োজন');
                return false;
            }
            
            if (!zoneId) {
                e.preventDefault();
                toastr.error('অনুগ্রহ করে অঞ্চল নির্বাচন করুন', 'অঞ্চল প্রয়োজন');
                return false;
            }
            
            console.log('modal submit clicked (index)', { action: $form.attr('action'), dataArray: $form.serializeArray(), serialized: $form.serialize() });
        } catch(err) { console.error('modal submit log error (index)', err); }
    });

    $(document).on('submit', '.modal form', function(e){
        try {
            var $form = $(this);
            
            // Validate Pathao fields before submission
            const cityId = $('#pathao_city_id').val();
            const zoneId = $('#pathao_zone_id').val();
            
            if (!cityId) {
                e.preventDefault();
                toastr.error('অনুগ্রহ করে শহর নির্বাচন করুন', 'শহর প্রয়োজন');
                return false;
            }
            
            if (!zoneId) {
                e.preventDefault();
                toastr.error('অনুগ্রহ করে অঞ্চল নির্বাচন করুন', 'অঞ্চল প্রয়োজন');
                return false;
            }
            
            console.log('modal form submitting (index)', { action: $form.attr('action'), dataArray: $form.serializeArray(), serialized: $form.serialize() });
        } catch(err) { console.error('modal form submit log error (index)', err); }
    });
*/
    // Checkout attribute change functionality
    
    // Handle checkout attribute change button clicks
    $(document).on('click', '.checkout-change-attribute-btn', function(e) {
        e.preventDefault();
        
        const cartKey = $(this).data('cart-key');
        const productId = $(this).data('product-id');
        const attributeIndex = $(this).data('attribute-index');
        const $select = $(this).siblings('.checkout-attribute-change-select');
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
                console.error('Checkout attribute update error:', error);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to update attribute. Please try again.', 'Error');
                }
            },
            complete: function() {
                // Reset button state
                $('.checkout-change-attribute-btn').html('<i class="fas fa-edit"></i> Change').prop('disabled', false);
            }
        });
    });
    
    // Load available attribute options when checkout dropdown is focused
    $(document).on('focus', '.checkout-attribute-change-select', function() {
        const $select = $(this);
        const cartKey = $select.data('cart-key');
        const attributeName = $select.data('attribute-name');
        const currentValue = $select.val();
        
        // Only load options if not already loaded (check if only one option exists)
        if ($select.find('option').length <= 1) {
            console.log('Loading checkout attribute options for:', attributeName);
            
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
                    console.log('Checkout attribute options response:', response);
                    if (response.success && response.options) {
                        // Clear existing options
                        $select.empty();
                        
                        // Add new options
                        response.options.forEach(function(option) {
                            const isSelected = option.name === currentValue ? 'selected' : '';
                            const priceText = option.price > 0 ? ` (+${option.price})` : '';
                            $select.append(`<option value="${option.name}" ${isSelected}>${option.name}${priceText}</option>`);
                        });
                        
                        console.log('Loaded', response.options.length, 'checkout options for', attributeName);
                    } else {
                        console.error('Failed to load checkout options:', response.message);
                        // Restore original option if loading fails
                        $select.empty().append(`<option value="${currentValue}" selected>${currentValue}</option>`);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load checkout attribute options:', error);
                    // Restore original option if loading fails
                    $select.empty().append(`<option value="${currentValue}" selected>${currentValue}</option>`);
                }
            });
        }
    });
    
    // Pre-load checkout attribute options when page loads
    $('.checkout-attribute-change-select').each(function() {
        const $select = $(this);
        const cartKey = $select.data('cart-key');
        const attributeName = $select.data('attribute-name');
        const currentValue = $select.val();
        
        console.log('Pre-loading checkout options for:', attributeName);
        
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
                console.error('Failed to pre-load checkout attribute options:', error);
            }
        });
    });

});
</script>
@endsection