@extends('master.front')
{{--
 @Author: Anwarul
 @Date: 2025-08-13 17:13:49
 @LastEditors: Anwarul
 @LastEditTime: 2025-08-19 13:15:11
 @Description: Innova IT
 --}}

@section('title')
    {{ __('Billing') }}
@endsection

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
/* Simple Select2 styling to match Bootstrap */
.select2-container {
    width: 100% !important;
}

.select2-container .select2-selection--single {
    height: 38px;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

.select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 12px;
}

.select2-container .select2-selection--single .select2-selection__arrow {
    height: 36px;
    right: 10px;
}

.select2-dropdown {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

/* Payment Gateway Styling */
.payment-shipping-option-wrap .option-group label {
    display: block;
    padding: 12px 16px;
    margin-bottom: 8px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
}

.payment-shipping-option-wrap .option-group label:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.payment-shipping-option-wrap .option-group label.active {
    border-color: #007bff;
    background-color: #e7f3ff;
}

.payment-shipping-option-wrap .option-group input[type="radio"]:checked + .option {
    background-color: transparent;
}

.payment-shipping-option-wrap .option-group label.active .option::after {
    content: "✓ Selected";
    float: right;
    color: #007bff;
    font-weight: bold;
    font-size: 14px;
    padding: 2px 8px;
    background: #007bff;
    color: white;
    border-radius: 12px;
    font-size: 11px;
}

.payment-shipping-option-wrap .option-group input[type="radio"] {
    display: none;
}

.payment-shipping-option-wrap .option-group .option {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
}

.payment-shipping-option-wrap .option-group .option img {
    margin-right: 8px;
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
                    <li>{{ $item->name }}</li>
                </ul>
            </div>
        </div>
    </div>



    <!-- Page Content-->
    <div class="container padding-bottom-3x mb-1 landing-checkout-page">
        <style>
            .for-boader{cursor:pointer;border:1px solid #eee !important;border-radius:8px;padding:8px 10px;margin-bottom:8px;background:#fff}
            /* Color list */
            .color-option-item{cursor:pointer;border:1px solid #eee;border-radius:8px;padding:8px 10px;margin-bottom:8px;background:#fff}
            .color-option-item.selected{border-color:#0ea45b;box-shadow:0 6px 18px rgba(14,164,91,0.06);background:#fff}
            .color-option-item .color-radio{width:18px;height:18px;margin-right:10px;appearance:none;-webkit-appearance:none;border-radius:50%;border:2px solid #ccc;display:inline-block;position:relative}
            .color-option-item.selected .color-radio{border-color:#0ea45b;background:#0ea45b}
            .color-option-item img{width:44px;height:44px;border-radius:6px;object-fit:cover}

            /* Sizes */
            .size-item{display:flex;align-items:center;margin-right:10px;margin-bottom:12px}
            .size-box{border:1px solid #ccc;background:#fff !important;color:#111 !important;padding:6px 10px;border-radius:6px;min-width:40px;text-align:center;font-weight:700}
            .size-box.active{background-color:#0ea45b !important;color:#fff !important;border-color:#0ea45b !important;box-shadow:0 6px 18px rgba(14,164,91,0.08) !important;}
            .size-box + .size-box{margin-left:8px}
            .input-group.quick{display:flex;flex-direction:column;margin-left:8px}
            .input-group.quick .btn{width:36px;height:28px;padding:0}
            .quick-qty{width:80px;text-align:center;margin:6px 0}
            #sizesForColor .ms-2{margin-left:8px}

            /* Inline quantity small styling when shown under sizes */
            #sizesForColor .product-quantity{display:inline-flex;align-items:center}
            #sizesForColor .product-quantity .decreaseQty, #sizesForColor .product-quantity .increaseQty{width:32px;height:28px;line-height:28px;border:1px solid #ddd;display:inline-flex;align-items:center;justify-content:center;background:#fff;border-radius:4px}
            /* Using absolute URL from server; no client-side basePrefix needed */
            .size-toggle{width:44px;height:64px}
        </style>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="product-gallery">

                                    <div class="gallery-wrapper">
                                        <div class="gallery-item video-btn text-center">
                                            <a href="{{ $item->video }}" title="Watch video"></a>
                                        </div>
                                    </div>
                                    @if ($item->is_stock())
                        <span
                            class="product-badge
                        @if ($item->is_type == 'feature') bg-warning
                        @elseif($item->is_type == 'new')
                        bg-success
                        @elseif($item->is_type == 'top')
                        bg-info
                        @elseif($item->is_type == 'best')
                        bg-dark
                        @elseif($item->is_type == 'flash_deal')
                            bg-success @endif
                        ">{{ __($item->is_type != 'undefine' ? ucfirst(str_replace('_', ' ', $item->is_type)) : '') }}</span>
                    @else
                        <span class="product-badge bg-secondary border-default text-body">{{ __('out of stock') }}</span>
                    @endif

                                    {{-- <div class="product-badge bg-goldenrod  ppp-t"> -{{ PriceHelper::DiscountPercentage($item) }}</div> --}}

                                    <div class="product-thumbnails insize">
                                        <div class="product-details-slider owl-carousel">
                                            <div class="item"><img
                                                    src="{{ url('/storage/images/' . $item->photo) }}"
                                                    alt="zoom" /></div>
                                              @foreach ($item->galleries as $key => $gallery)
                                                    <div class="item"><img src="{{ url('/storage/images/' . $gallery->photo) }}"
                                                            alt="zoom" /></div>
                                                @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Summary Sidebar -->

                            <div class="col-xl-6">
                                <div class="h-100 speacifications-card-wrap">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="specification-tab" data-bs-toggle="tab"
                                                data-bs-target="#specification" type="button" role="tab"
                                                aria-controls="specification"
                                                aria-selected="false">{{ __('Specifications') }}</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                                data-bs-target="#description" type="button" role="tab"
                                                aria-controls="description"
                                                aria-selected="true">{{ __('Descriptions') }}</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content card">
                                        <div class="tab-pane fade show active" id="specification" role="tabpanel"
                                            aria-labelledby="specification-tab">
                                            <div class="comparison-table speacifications-table">
                                                <table class="table table-bordered mb-0">
                                                    <tbody>
                                                        <tr class="bg-secondary">
                                                            <th class="text-uppercase">{{ __('Specifications') }}</th>
                                                            <td><span class="text-medium">{{ __('Descriptions') }}</span></td>
                                                        </tr>
                                                        @php $sec_name =isset($item->specification_name) ? json_decode($item->specification_name, true) : [];
                                                        $sec_details =isset($item->specification_description) ? json_decode($item->specification_description, true) : [];
                                                        @endphp
                                                         @if ($sec_name)
                                                        @foreach (array_combine($sec_name, $sec_details) as $sname => $sdetail)
                                                            <tr>
                                                                <th>{{ $sname }}</th>
                                                                <td>{{ $sdetail }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr class="text-center">
                                                            <td colspan="2">{{ __('No Specifications') }}</td>
                                                        </tr>
                                                    @endif
                                                      
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade show" id="description" role="tabpanel"
                                            aria-labelledby="description-tab">
                                            <div class="card-body text-area-card">
                                                <div class="d-page-content">
                                                    {!! $item->details !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address Section -->
                <div class="card mb-4 ">
                    <div class="landing-checkoutshipping-card">
                        <div class="card-body">
                            <h2 class="title">অর্ডার করতে ফর্মটি পূরণ করুন।</h2>
                             <form id="checkoutShipping">
                                        @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="checkout-name">{{ __('পুরো নাম') }}*</label>
                                                    <input class="form-control" name="ship_full_name" type="text"
                                                        id="checkout-name" value="{{ $user->first_name ?? '' }}" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="checkout-email">{{ __('ই-মেইল ঠিকানা') }}</label>
                                                    <input class="form-control" name="ship_email" type="email"
                                                        id="checkout-email" value="{{ $user->email ?? '' }}" >
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
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
                                                        id="checkout-address" placeholder="Start typing your address"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="address_shipping_price" id="address_shipping_amount" value="0">
                                          <!-- Pathao Address Selection -->
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>City</label>
                                                <select class="form-control select2" id="pathao_city_id" name="pathao_city_id" data-placeholder="Select City" required>
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
                                    </div>
                                        
                                </div>
                                <div class="col-lg-6">
                                    <div class="land-form-group-wrap">

                                        <div class="row ">
                                            @php
                                                // Find common attributes by name (case-insensitive)
                                                $attrs = $item->attributes;
                                                $colorAttr = null;
                                                $sizeAttr = null;
                                                foreach($attrs as $a){
                                                    $n = strtolower($a->keyword);
                                                    if(strpos($n,'color') !== false || strpos($n,'colour') !== false || strpos($n,'বডি-কালার') !== false) $colorAttr = $a;
                                                    if(strpos($n,'size') !== false || strpos($n,'বডি-সাইজ') !== false) $sizeAttr = $a;
                                                    // echo $a;
                                                }
                                            @endphp
                                            @if(isset($cart) && count($cart) > 0)
                                                @php
                                                    $currentProductItems = [];
                                                    $otherProductItems = [];
                                                    foreach($cart as $key => $cartItem) {
                                                        if(strpos($key, $item->id . '-') === 0) {
                                                            $currentProductItems[$key] = $cartItem;
                                                        } else {
                                                            $otherProductItems[$key] = $cartItem;
                                                        }
                                                    }
                                                    // Add debugging info for cart structure
                                                    if(app()->environment('local')) {
                                                        \Log::info('Cart Debug - Current Product Items:', $currentProductItems);
                                                        \Log::info('Cart Debug - Other Product Items:', $otherProductItems);
                                                    }
                                                @endphp
                                                
                                                {{-- Show other products in cart first --}}
                                                @if(count($otherProductItems) > 0)
                                                    <div class="col-12 mb-4 other-products-cart">
                                                        <h4>{{ __('Other Items in Cart') }}</h4>
                                                        <div class="card">
                                                            <div class="card-body">
                                                                @foreach($otherProductItems as $key => $cartItem)
                                                                    @php
                                                                        $attributes = $cartItem['attribute'] ?? [];
                                                                        $optionNames = isset($attributes['option_name']) ? implode(', ', $attributes['option_name']) : '';
                                                                        $subtotal = ($cartItem['main_price'] + ($cartItem['attribute_price'] ?? 0)) * $cartItem['qty'];
                                                                    @endphp
                                                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                                                        <div>
                                                                            <strong>{{ $cartItem['name'] }}</strong>
                                                                            @if($optionNames)
                                                                                <br><small class="text-muted">{{ $optionNames }}</small>
                                                                            @endif
                                                                        </div>
                                                                        <div class="text-end">
                                                                            <div>{{ __('Qty') }}: {{ $cartItem['qty'] }}</div>
                                                                            <div class="text-primary">{{ PriceHelper::setCurrencyPrice($subtotal) }}</div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                {{-- Show current product items in cart --}}
                                                @if(count($currentProductItems) > 0)
                                                    <div class="col-12 mb-4 current-product-cart">
                                                        <h4>{{ $item->name }} - {{ __('Already in Cart') }}</h4>
                                                        <div class="card">
                                                            <div class="card-body">
                                                                @foreach($currentProductItems as $key => $cartItem)
                                                                    @php
                                                                        $attributes = $cartItem['attribute'] ?? [];
                                                                        $optionNames = isset($attributes['option_name']) ? implode(', ', $attributes['option_name']) : '';
                                                                        $subtotal = ($cartItem['main_price'] + ($cartItem['attribute_price'] ?? 0)) * $cartItem['qty'];
                                                                    @endphp
                                                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                                                        <div>
                                                                            <strong>{{ $cartItem['name'] }}</strong>
                                                                            @if($optionNames)
                                                                                <br><small class="text-muted">{{ $optionNames }}</small>
                                                                            @endif
                                                                        </div>
                                                                        <div class="text-end">
                                                                            <div>{{ __('Qty') }}: {{ $cartItem['qty'] }}</div>
                                                                            <div class="text-primary">{{ PriceHelper::setCurrencyPrice($subtotal) }}</div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif

                                            @if($colorAttr && $sizeAttr)
                                                <div class="col-12">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="list-group" id="colorList">
                                                                
                                                                @foreach($colorAttr->options->where('stock','!=',0) as $copt)
                                                                {{-- @dd( $copt) --}}
                                                                <div class="for-boader">
                                                                    <label class="list-group-item align-items-center color-option-item mb-2" data-color-option="{{ $copt->id }}" data-color-attr="{{ $colorAttr->id }}">
                                                                        <div class="flex-grow-1 d-flex justify-content-between align-items-center">
                                                                            <div class="d-flex align-items-center">
                                                                                <input class="form-check-input me-2 color-checkbox" type="checkbox" value="{{ $copt->id }}">
                                                                                <strong class="me-3">{{ $copt->name }}</strong>
                                                                            </div>
                                                                            <div>
                                                                                @if ($item->discount_price != null)
                                                                                  <del><sub class="text-danger">(৳ {{ $item->previous_price }}) </sub> </del> <strong> ৳ {{ $item->discount_price }}</strong>
                                                                                @else
                                                                                    <strong> ৳ {{ $item->previous_price }}</strong>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        @if($copt->color_image != null)
                                                                        <div class="d-flex justify-content-end mt-2"><img src="{{ url('/storage/images/color_options/' . $copt->color_image) }}" alt="" style="width:40px;height:40px;border-radius:4px;object-fit:cover"></div>
                                                                        @else
                                                                        <div class="d-flex justify-content-end mt-2"><img src="{{ url('/storage/images/' . $item->photo) }}" alt="" style="width:40px;height:40px;border-radius:4px;object-fit:cover"></div>
                                                                        @endif
                                                                    </label>
                                                                    <div class="color-sizes-container" data-color-option="{{ $copt->id }}" style="display:none;margin-bottom:12px"></div>
                                                                </div>
                                                                    @endforeach
                                                            </div>
                                                        {{-- </div>
                                                        <div class="col-md-7"> --}}
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                {{-- Fallback to original single-attribute selects when color/size not both present --}}
                                                @foreach ($item->attributes as $attribute)
                                                    @if ($attribute->options->count() != 0)
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label for="{{ $attribute->name }}">{{ $attribute->name }}</label>
                                                                <select class="form-control attribute_option" id="{{ $attribute->name }}">
                                                                    @foreach ($attribute->options->where('stock', '!=', '0') as $option)
                                                                        <option value="{{ $option->name }}" data-type="{{ $attribute->id }}" data-href="{{ $option->id }}" data-target="{{ ($option->price) }}">{{ $option->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                        {{-- Size --}}
                                        {{-- <div class="form-group product-dt-form-group">
                                            <h3 class="d-block title-lable">Size:</h3>

                                            <label class="form-check">
                                                <input class="form-check-input attribute_option" type="radio"
                                                    name="attribute_size" id="option_1" value="Small" data-type="size"
                                                    data-href="1" data-target="">
                                                <div class="form-check-label form-check-button" for="option_1">
                                                    Small
                                                </div>
                                            </label>

                                            <label class="form-check">
                                                <input class="form-check-input attribute_option" type="radio"
                                                    name="attribute_size" id="option_2" value="Medium" data-type="size"
                                                    data-href="2" data-target="">
                                                <div class="form-check-label form-check-button" for="option_2">
                                                    Medium
                                                </div>
                                            </label>

                                            <label class="form-check">
                                                <input class="form-check-input attribute_option" type="radio"
                                                    name="attribute_size" id="option_3" value="Large" data-type="size"
                                                    data-href="3" data-target="">
                                                <div class="form-check-label form-check-button" for="option_3">
                                                    Large
                                                </div>
                                            </label>
                                        </div> --}}

                                        {{-- Color --}}
                                        {{-- <div class="form-group product-dt-form-group">
                                            <h3 class="d-block title-lable">Color:</h3>

                                            <label class="form-check">
                                                <input class="form-check-input attribute_option" type="radio"
                                                    name="attribute_color" id="option_4" value="Red"
                                                    data-type="color" data-href="4" data-target="">
                                                <div class="form-check-label form-check-button" for="option_4">
                                                    Red
                                                </div>
                                            </label>

                                            <label class="form-check">
                                                <input class="form-check-input attribute_option" type="radio"
                                                    name="attribute_color" id="option_5" value="Blue"
                                                    data-type="color" data-href="5" data-target="">
                                                <div class="form-check-label form-check-button" for="option_5">
                                                    Blue
                                                </div>
                                            </label>

                                            <label class="form-check">
                                                <input class="form-check-input attribute_option" type="radio"
                                                    name="attribute_color" id="option_6" value="Black"
                                                    data-type="color" data-href="6" data-target="">
                                                <div class="form-check-label form-check-button" for="option_6">
                                                    Black
                                                </div>
                                            </label>
                                        </div> --}}

                                        {{-- Quantity --}}
                                        {{-- <div class="product-dt-form-group">
                                            <h3 class="d-block title-lable">Quantity:</h3>
                                             @if ($item->item_type == 'normal')
                                                <div class="qtySelector product-quantity">
                                                    <span class="decreaseQty subclick"><i class="fas fa-minus "></i></span>
                                                    <input type="text" class="qtyValue cart-amount" value="1">
                                                    <span class="increaseQty addclick"><i class="fas fa-plus"></i></span>
                                                    <input type="hidden" value="3333" id="current_stock">
                                                </div>
                                            @endif
                                        </div> --}}
                                        
                                        {{-- Inline shipping and address rows removed — they are shown in the sidebar include --}}

                                        {{-- Total Price --}}
                                        {{-- <div class="product-dt-form-group total-price">
                                            <h3 class="d-block title-lable">Total Price:</h3>
                                            <h3 class="d-block title-lable main-price grand_total_set" id="main_price" >{{ PriceHelper::setCurrencyPrice($grand_total) }}</h3>
                                        </div> --}}
                                         @include('includes.single_checkout_sidebar', $cart)
                                            <input type="hidden" id="item_id" value="{{ $item->id }}">
                                             <input type="hidden" id="demo_price"
                                                value="{{ $item->discount_price }}">
                                            <input type="hidden" value="{{ PriceHelper::setCurrencySign() }}" id="set_currency">
                                            <input type="hidden" value="{{ PriceHelper::setCurrencyValue() }}" id="set_currency_val">
                                            <input type="hidden" value="{{ $setting->currency_direction }}" id="currency_direction">
                                                <!-- server-side totals for client calculations -->
                                                <input type="hidden" id="init_cart_total" value="{{ $cart_total ?? 0 }}">
                                                <input type="hidden" id="init_tax_total" value="{{ $tax ?? 0 }}">
                                                <input type="hidden" id="server_discount" value="{{ $discount['discount'] ?? 0 }}">
                                                <input type="hidden" id="server_shipping_price" value="{{ is_object($shipping) ? ($shipping->price ?? 0) : 0 }}">
                                                <input type="hidden" id="server_state_type" value="{{ (Auth::check() && Auth::user()->state_id) ? Auth::user()->state->type : '' }}">
                                                <input type="hidden" id="server_state_price" value="{{ (Auth::check() && Auth::user()->state_id) ? Auth::user()->state->price : 0 }}">

                                        <!-- Payment Method Section -->
                                        <section class="card widget widget-order-summary p-4 mb-0 payment-method-section mt-4">
                                            <h3 class="widget-title">{{ __('পেমেন্ট পদ্ধতি নির্বাচন করুন') }}</h3>
                                            @php
                                                $gateways = DB::table('payment_settings')->whereStatus(1)->get();
                                            @endphp
                                            
                                            @if($gateways->count() > 0)
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="payment-shipping-option-wrap">
                                                        <div class="option-group">
                                                            @foreach($gateways as $gateway)
                                                                {{-- Make COD checked by default, others unchecked --}}
                                                                <label>
                                                                    <input type="radio" name="payment_gateway" value="{{ $gateway->unique_keyword }}" 
                                                                     {{ $gateway->unique_keyword == 'cod' ? 'checked' : '' }}>
                                                                    <p class="option">
                                                                        <img src="{{ url('/storage/images/'.$gateway->photo) }}" 
                                                                             alt="{{ $gateway->name }}" style="width: 24px; height: 24px;">
                                                                        {{ $gateway->name }}
                                                                    </p>
                                                                </label>
                                                                <br>
                                                                @if( $gateway->unique_keyword === 'cod')
                                                                    <div class="cod-details">
                                                                        <p>{{$gateway->text}}</p>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                                <p class="text-muted">No payment methods available</p>
                                            @endif
                                        </section>
                                    </div>
                                </div>
                <div class="col-lg-12">
                      @if($setting->is_privacy_trams == 1)
                                <div class="form-group mt-4 text-center">
                                    <div class="custom-control d-flex custom-checkbox">
                                        <input class="custom-control-input me-2" type="checkbox" id="trams__condition_single">
                                        <label class="custom-control-label flex-1" for="trams__condition_single">
                                            {!! __('I agree to the :terms_link and :policy_link', [
                                                'terms_link' => '<a href="'.$setting->terms_link.'" target="_blank">'.__('Terms of Service').'</a>',
                                                'policy_link' => '<a href="'.$setting->policy_link.'" target="_blank">'.__('Privacy Policy').'</a>'
                                            ]) !!}
                                        </label>
                                    </div>
                                </div>
                            @endif
                    <div class="order-button-wrap">
                           
                        <button id="single_checkout_payment" disabled class="btn btn-primary single_checkout_payment" type="submit">
                            <span>{{ __('Buy now') }}</span>
                        </button>
                    </div>
                </div>                            </div>
                        </form>
                        @include('includes.single_checkout_modal')
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4">
               
            </div>
        </div>
    </div>
    @include('includes.single_checkout_modal')
@endsection

@section('script')
<script>
$(document).ready(function() {
    console.log('menualcheckout ready', { path: window.location.pathname });
    // translated labels used in dynamic HTML (avoid Blade translation inside JS strings)
    var tQuantity = "{{ __('Quantity:') }}";
    var tAddSelected = "{{ __('Add Selected') }}";
    var tNoSizes = "{{ __('No sizes available') }}";
    
    // Simple Select2 initialization
    setTimeout(function() {
        if ($.fn.select2) {
            $('.select2').select2({
                width: '100%',
                minimumResultsForSearch: Infinity // Hide search box
            });
        }
    }, 100);

    // Auto-select color checkboxes for existing cart items and move cart section if needed
    @if(isset($cart) && count($cart) > 0)
        var currentProductItems = @json($currentProductItems ?? []);
        var hasOtherProducts = @json(count($otherProductItems ?? []) > 0);
        var currentProductId = {{ $item->id }};
        
        // Move "Other Items in Cart" section above color selection if it exists
        if(hasOtherProducts) {
            var $otherCartSection = $('.other-products-cart');
            var $colorSection = $('.col-12').has('#colorList');
            if($otherCartSection.length && $colorSection.length) {
                $otherCartSection.insertBefore($colorSection.parent());
            }
        }
        
        // Auto-select color checkboxes for existing cart items of current product
        if(currentProductItems && currentProductItems.length > 0) {
            console.log('Processing current product items:', currentProductItems);
            
            currentProductItems.forEach(function(cartItem) {
                if(cartItem.attribute && cartItem.attribute.option_id) {
                    var optionIds = cartItem.attribute.option_id;
                    var attributeIds = cartItem.attribute.attribute_id || [];
                    
                    // Handle both array and single option ID
                    if(!Array.isArray(optionIds)) {
                        optionIds = [optionIds];
                    }
                    if(!Array.isArray(attributeIds)) {
                        attributeIds = [attributeIds];
                    }
                    
                    console.log('Processing cart item:', { optionIds: optionIds, attributeIds: attributeIds, qty: cartItem.qty });
                    
                    // Find color and size IDs from the cart item
                    var colorId = null, sizeId = null;
                    
                    // Check which option belongs to which attribute
                    @if(isset($colorAttr) && isset($sizeAttr))
                        var colorAttrId = {{ $colorAttr->id }};
                        var sizeAttrId = {{ $sizeAttr->id }};
                        
                        for(var i = 0; i < optionIds.length; i++) {
                            if(attributeIds[i] == colorAttrId) {
                                colorId = optionIds[i];
                            } else if(attributeIds[i] == sizeAttrId) {
                                sizeId = optionIds[i];
                            }
                        }
                    @endif
                    
                    console.log('Identified:', { colorId: colorId, sizeId: sizeId });
                    
                    if(colorId) {
                        var $colorCheckbox = $('.color-checkbox[value="' + colorId + '"]');
                        if($colorCheckbox.length && !$colorCheckbox.is(':checked')) {
                            console.log('Auto-selecting color checkbox for option ID:', colorId);
                            $colorCheckbox.prop('checked', true).trigger('change');
                            
                            // Wait for the color change to complete, then select size and set quantity
                            setTimeout(function() {
                                var $holder = $('.color-sizes-container[data-color-option="' + colorId + '"]');
                                
                                // Auto-select the size if we have sizeId
                                if(sizeId && $holder.length) {
                                    var $sizeBtn = $holder.find('.size-box[data-size-option="' + sizeId + '"]');
                                    if($sizeBtn.length) {
                                        console.log('Auto-selecting size button for size ID:', sizeId);
                                        // Clear any existing active sizes first
                                        $holder.find('.size-box').removeClass('active');
                                        // Set the selected size as active
                                        $sizeBtn.addClass('active');
                                        $holder.data('selected-size-option', sizeId);
                                        $holder.data('selected-size-attr', $sizeBtn.data('size-attr'));
                                    } else {
                                        console.log('Size button not found for size ID:', sizeId);
                                        // If exact size not found, select first available size
                                        var $firstSize = $holder.find('.size-box').first();
                                        if($firstSize.length) {
                                            $firstSize.addClass('active');
                                            $holder.data('selected-size-option', $firstSize.data('size-option'));
                                            $holder.data('selected-size-attr', $firstSize.data('size-attr'));
                                            console.log('Auto-selected first available size:', $firstSize.data('size-option'));
                                        }
                                    }
                                }
                                
                                // Set the quantity from existing cart item
                                var $qtyInput = $holder.find('.qtyValueLocal');
                                if($qtyInput.length) {
                                    $qtyInput.val(cartItem.qty || 1);
                                    console.log('Set quantity for color option', colorId, 'to', cartItem.qty);
                                }
                            }, 300);
                        }
                    }
                }
            });
        }
    @endif

    // Ensure AJAX requests include CSRF header (master layout should include meta[name="csrf-token"])
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
        }
    });

    // Initialize shipping method on page load
    $('input[name="shipping_id"]:checked').trigger('change');
    
    // Initialize Order Summary with current cart totals
    setTimeout(function() {
        console.log('Initial page load - updating totals');
        updateTotalsFromLocalQty();
    }, 1000);

    // Centralized totals recompute using base + selected shipping + address fee
    function recalcTotals(){
        const baseTotalRaw = {{ $grand_total }};
        const $sel = $('input[name="shipping_id"]:checked');
        const methodPrice = $sel.length ? (parseFloat($sel.data('price')) || 0) : 0;
        const addrAdd = parseFloat($('#address_shipping_amount').val()||'0') || 0;
        const newTotalRaw = baseTotalRaw + methodPrice + addrAdd;
        // render formatted
        console.log('recalcTotals', { baseTotalRaw: baseTotalRaw, methodPrice: methodPrice, addrAdd: addrAdd, newTotalRaw: newTotalRaw });
        $('.set__shipping_price').text(formatCurrency(methodPrice));
        $('.set__shipping_price_tr').removeClass('d-none');
        $('.address_shipping_price').text(formatCurrency(addrAdd));
        $('.grand_total_set').text(formatCurrency(newTotalRaw));
    }

    // Small client-side formatter using page currency settings
    function formatCurrency(amount){
        const val = parseFloat(amount||0);
        const dir = $('#currency_direction').val() || 'ltr';
        const sign = $('#set_currency').val() || '';
        // keep two decimals
        const formattedNumber = val.toFixed(2);
        if(dir === 'rtl'){
            return formattedNumber + ' ' + sign;
        }
        return sign + formattedNumber;
    }

    // Shipping method change handler
    $(document).on('change', 'input[name="shipping_id"]', function() {
        let shippingId = $(this).val();
        let shippingPrice = parseFloat($(this).data('price')) || 0;
        
        // Update Order Summary immediately
        updateTotalsFromLocalQty();
        
        let url = $(this).data('href');
        $('.shipping_id_setup').val(shippingId);
        console.log('shipping change ajax', { url: url, data: { shipping_id: shippingId }, price: shippingPrice });
        
        $.ajax({
            url: url,
            type: 'POST',
            data: { shipping_id: shippingId, _token: '{{ csrf_token() }}' }
        }).done(function(resp){ 
            console.log('shipping change response', resp);
            // Update Order Summary again after server response
            updateTotalsFromLocalQty();
        }).fail(function(err){ 
            console.error('shipping change failed', err); 
        });
    });

    // Payment method selection handler
    $(document).on('click', '.single_checkout_payment', function(e) {
        e.preventDefault();
        
        // Validate required fields
        if (!$('input[name="ship_full_name"]').val()) { 
            toastr.error('নাম প্রয়োজন', 'তথ্য অনুপস্থিত');
            $('input[name="ship_full_name"]').focus();
            return; 
        }
        if (!$('input[name="ship_phone"]').val()) { 
            toastr.error('ফোন নাম্বার প্রয়োজন', 'তথ্য অনুপস্থিত');
            $('input[name="ship_phone"]').focus();
            return; 
        }
        if (!$('#pathao_city_id').val()) { 
            toastr.error('শহর নির্বাচন করুন', 'তথ্য অনুপস্থিত');
            $('#pathao_city_id').focus();
            return; 
        }
        if (!$('#pathao_zone_id').val()) { 
            toastr.error('জোন নির্বাচন করুন', 'তথ্য অনুপস্থিত');
            $('#pathao_zone_id').focus();
            return; 
        }
        if (!$('input[name="ship_address"]').val()) { 
            toastr.error('সম্পূর্ণ ঠিকানা লিখুন', 'তথ্য অনুপস্থিত');
            $('input[name="ship_address"]').focus();
            return; 
        }
        
        // Check if terms and conditions are accepted
        if($('#trams__condition_single').length && !$('#trams__condition_single').is(':checked')) {
            toastr.error('নিয়ম ও শর্তাবলী গ্রহণ করুন', 'তথ্য অনুপস্থিত');
            return false;
        }
        
        // Get selected payment gateway
        var selectedGateway = $('input[name="payment_gateway"]:checked').val();
        if(!selectedGateway) {
            toastr.error('পেমেন্ট পদ্ধতি নির্বাচন করুন', 'তথ্য অনুপস্থিত');
            return false;
        }
        
        var keyword = selectedGateway;
        var modalElement = document.getElementById(keyword);
        var paymentMethodValue = (keyword === 'cod' ? 'Cash On Delivery' : keyword);
        
        if (modalElement) {
            // show payment modal and inject form values
            let formData = $('#checkoutShipping').serializeArray();
            console.log('modal branch - formData', formData);
            $(modalElement).find('form').append(formData.map(function(input) {
                return $('<input>').attr({ type: 'hidden', name: input.name, value: input.value });
            }));
            let shippingId = $('input[name="shipping_id"]:checked').val();
            $(modalElement).find('.shipping_id_setup').val(shippingId);
            // ensure server receives payment method key expected by controller
            $(modalElement).find('form').append($('<input>').attr({ type: 'hidden', name: 'payment_method', value: paymentMethodValue }));
            $(modalElement).find('form').append($('<input>').attr({ type: 'hidden', name: 'payment_gateway', value: keyword }));
            let modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else {
            // No modal for selected payment gateway — submit checkout to server
            // Only allow direct submit for Cash On Delivery (server expects 'Cash On Delivery')
            if(keyword === 'cod'){
                // If the site is served under a subpath that contains '/public' (common with XAMPP),
                // prepend that portion so generated route() paths resolve correctly.
                var basePrefix = '';
                try {
                    if (window.location.pathname.indexOf('/public/') !== -1) {
                        basePrefix = window.location.pathname.substring(0, window.location.pathname.indexOf('/public') + 7);
                    }
                } catch (e) { basePrefix = ''; }
                // Use absolute URL generated by Laravel so JS-created forms post to the correct path
                var action = '{{ url(route('front.checkout.submit')) }}';
                var $form = $('<form>').attr({ method: 'POST', action: action });
                $form.append($('<input>').attr({ type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
                // copy shipping form fields
                var formData = $('#checkoutShipping').serializeArray();
                console.log('direct submit - formData from #checkoutShipping', formData);
                formData.forEach(function(input){
                    // do not append empty shipping_id values
                    if (input.name === 'shipping_id' && !input.value) return;
                    $form.append($('<input>').attr({ type: 'hidden', name: input.name, value: input.value }));
                });
                // include selected payment gateway and shipping id
                $form.append($('<input>').attr({ type: 'hidden', name: 'payment_gateway', value: keyword }));
                // also send payment_method for server-side detection
                $form.append($('<input>').attr({ type: 'hidden', name: 'payment_method', value: paymentMethodValue }));
                var selectedShipping = $('input[name="shipping_id"]:checked').val();
                if (selectedShipping) {
                    $form.append($('<input>').attr({ type: 'hidden', name: 'shipping_id', value: selectedShipping }));
                }
                console.log('direct submit - final form action/payload', { action: action, inputs: $form.serialize() });
                // append and submit
                $(document.body).append($form);
                $form.submit();
            } else {
                alert('Selected payment gateway requires its payment modal; please choose Cash On Delivery or a gateway with a modal.');
            }
        }
    });

    // Division change: enable and populate Districts
    $(document).on('change', '#division_id', function(){
        const id = $(this).val();
        const $d = $('#district_id');
        $d.val('').trigger('change.select2');
        $('#thana_id').val('').trigger('change.select2').prop('disabled', true);
        $('.address_shipping_tr').addClass('d-none');
        $('.address_shipping_price').text('0.00');
        $('#address_shipping_amount').val('0');
        // Recompute totals from current selected local quantities (do not reset selections)
        updateTotalsFromLocalQty();

        if(!id){
            $d.prop('disabled', true);
            $('.payment-shipping-option-wrap .shipping-group').show();
            return;
        }

        $.get("{{ route('front.checkout.districts') }}", { division_id: id })
            .done(function(rows){
                $d.empty().append('<option value="">Select</option>');
                rows.forEach(function(r){ $d.append('<option value="'+r.id+'">'+r.name+'</option>'); });
                $d.prop('disabled', false).trigger('change.select2');
            });
    });

    // District change: populate Thanas and optionally compute address shipping
    $(document).on('change', '#district_id', function(){
        const id = $(this).val();
        const division_id = $('#division_id').val();
        const $t = $('#thana_id');
        $t.prop('disabled', !id).val('').trigger('change.select2');
        $('.address_shipping_tr').addClass('d-none');
        $('.address_shipping_price').text('0.00');
        $('#address_shipping_amount').val('0');
                    // Recompute totals using current selections/quantities
                    updateTotalsFromLocalQty();        if (division_id && id) {
            $.get("{{ route('front.address.shipping.setup') }}", { division_id: division_id, district_id: id, thana_id: null })
                .done(function(resp){
                    const amt = parseFloat(resp.price || 0) || 0;
                    $('.address_shipping_price').text(resp.formatted || amt.toFixed(2));
                    $('#address_shipping_amount').val(amt.toFixed(2));
                    $('.address_shipping_tr').toggleClass('d-none', amt<=0);
                    if (resp.use_address_price) {
                        $('.payment-shipping-option-wrap .shipping-group').hide();
                        $('input[name="shipping_id"]').prop('checked', false);
                    } else {
                        $('.payment-shipping-option-wrap .shipping-group').show();
                    }
                    // Recompute totals using current selections/quantities (include new address shipping)
                    updateTotalsFromLocalQty();
                });
        }

        if(!id) return;
        $.get("{{ route('front.checkout.thanas') }}", { district_id: id })
            .done(function(rows){
                $t.empty().append('<option value="">Select</option>');
                rows.forEach(function(r){ $t.append('<option value="'+r.id+'">'+r.name+'</option>'); });
                $t.trigger('change.select2');
            });
    });

    // Thana change: compute address-based shipping if thana chosen
    $(document).on('change', '#thana_id', function(){
        const division_id = $('#division_id').val();
        const district_id = $('#district_id').val();
        const thana_id = $('#thana_id').val();
        if(division_id && district_id && thana_id){
            $.get("{{ route('front.address.shipping.setup') }}", { division_id: division_id, district_id: district_id, thana_id: thana_id })
                .done(function(resp){
                    const amt = parseFloat(resp.price || 0) || 0;
                    $('.address_shipping_price').text(resp.formatted || amt.toFixed(2));
                    $('#address_shipping_amount').val(amt.toFixed(2));
                    $('.address_shipping_tr').toggleClass('d-none', amt<=0);
                    if (resp.use_address_price) {
                        $('.payment-shipping-option-wrap .shipping-group').hide();
                        $('input[name="shipping_id"]').prop('checked', false);
                    } else {
                        $('.payment-shipping-option-wrap .shipping-group').show();
                    }
                    // Recompute totals using current selections/quantities (include new address shipping)
                    updateTotalsFromLocalQty();
                });
        } else {
            $('.address_shipping_tr').addClass('d-none');
            $('.address_shipping_price').text('0.00');
            $('#address_shipping_amount').val('0');
            $('.payment-shipping-option-wrap .shipping-group').show();
            updateTotalsFromLocalQty();
        }
    });

    // Variant quantity controls (for multi color×size grid)
    $(document).on('click', '.increase-variant', function(){
        var $row = $(this).closest('.variant-qty');
        var $input = $row.find('.variant-qty-input');
        var v = parseInt($input.val()||0)+1;
        $input.val(v);
    });
    $(document).on('click', '.decrease-variant', function(){
        var $row = $(this).closest('.variant-qty');
        var $input = $row.find('.variant-qty-input');
        var v = Math.max(0, parseInt($input.val()||0)-1);
        $input.val(v);
    });

    // When a color checkbox is toggled show/hide its sizes block and render sizes into that block
    $(document).on('change', '.color-checkbox', function(){
        var $lbl = $(this).closest('.color-option-item');
        var colorOption = $lbl.data('color-option');
        var colorAttr = $lbl.data('color-attr');
        var $sizesHolder = $('.color-sizes-container[data-color-option="'+colorOption+'"]');
        if($(this).is(':checked')){
            // render sizes for this color inside its holder
            $sizesHolder.empty();
            @if(isset($sizeAttr))
                var $wrap = $('<div/>', {class: 'd-flex flex-wrap gap-2 align-items-center mb-2'});
                @foreach($sizeAttr->options->where('stock','!=',0) as $sopt)
                    var $btn = $("<button type='button' class='btn size-box' data-size-option='{{ $sopt->id }}' data-size-attr='{{ $sizeAttr->id }}'>{{ $sopt->name }}</button>");
                    $wrap.append($btn);
                @endforeach
                $sizesHolder.append($wrap);
                // build a fresh, local quantity control for this color to avoid cloning side-effects
                var $qtyClone = $("<div class='qtySelector product-quantity-local' style='display:inline-flex;align-items:center'>"+
                    "<span class='decreaseQtyLocal' role='button' style='width:32px;height:28px;display:inline-flex;align-items:center;justify-content:center;border:1px solid #ddd;border-radius:4px;background:#fff;cursor:pointer'><i class='fas fa-minus'></i></span>"+
                    "<input type='text' class='qtyValueLocal cart-amount' value='1' style='width:48px;text-align:center;margin:0 6px;padding:4px;border:1px solid #eee;border-radius:4px'/>"+
                    "<span class='increaseQtyLocal' role='button' style='width:32px;height:28px;display:inline-flex;align-items:center;justify-content:center;border:1px solid #ddd;border-radius:4px;background:#fff;cursor:pointer'><i class='fas fa-plus'></i></span>"+
                    "</div>");
                var $ctrl = $('<div class="mt-2 d-flex justify-content-between align-items-center gap-3"></div>');
                $ctrl.append($qtyClone);
                $ctrl.append('<div><button type="button" class="btn btn-primary btn-sm add-color-variants" data-color-option="'+colorOption+'" data-color-attr="'+colorAttr+'">'+ tAddSelected +'</button></div>');
                $sizesHolder.append($ctrl);
            @else
                $sizesHolder.html('<p class="text-muted">'+ tNoSizes +'</p>');
            @endif
            $sizesHolder.show();
            $lbl.addClass('selected');
            // when a color is checked/added, refresh totals
            updateTotalsFromLocalQty();
        } else {
            $sizesHolder.hide().empty();
            $lbl.removeClass('selected');
            // when unchecked, refresh totals to remove its qty
            updateTotalsFromLocalQty();
        }
    });

    // Recompute displayed subtotal and grand total based on current cart state
    function updateTotalsFromLocalQty(){
        console.log('Updating totals from cart...');
        
        // Refresh cart header first to get latest cart state
        $.get("{{ route('front.header.cart') }}").done(function(html){
            $('.cart_view_header').html(html);
            var cnt = $('.cart_view_header').find('.entry').length || 0;
            $('.cart_count').text(cnt);
            
            // Calculate cart total from individual entries since extraction isn't working reliably
            var cartTotal = 0;
            var $cartHtml = $(html);
            
            // Method 1: Extract from the subtotal paragraph (most reliable)
            var $subtotalP = $cartHtml.find('p:contains("Subtotal")');
            if($subtotalP.length) {
                var subtotalText = $subtotalP.text();
                var matches = subtotalText.match(/৳\s*([\d,]+(?:\.\d+)?)/);
                if(matches) {
                    cartTotal = parseFloat(matches[1].replace(/,/g, '')) || 0;
                    console.log('Extracted from subtotal paragraph:', cartTotal);
                }
            }
            
            // Method 2: Calculate from individual cart entries if Method 1 fails
            if(cartTotal === 0) {
                $cartHtml.find('.entry').each(function(){
                    var $entry = $(this);
                    var metaText = $entry.find('.entry-meta').text();
                    
                    // Look for quantity pattern like "2 x"
                    var qtyMatch = metaText.match(/(\d+)\s*x/i);
                    var qty = qtyMatch ? parseInt(qtyMatch[1]) : 1;
                    
                    // Look for price pattern like "৳1,365"
                    var priceMatch = metaText.match(/৳\s*([\d,]+(?:\.\d+)?)/);
                    if(priceMatch) {
                        var price = parseFloat(priceMatch[1].replace(/,/g, '')) || 0;
                        cartTotal += price * qty;
                        console.log('Entry calculation:', { qty: qty, price: price, subtotal: price * qty });
                    }
                });
                console.log('Calculated from entries:', cartTotal);
            }
            
            // Method 3: If both fail, use server-provided cart total
            if(cartTotal === 0) {
                cartTotal = parseFloat($('#init_cart_total').val() || 0) || 0;
                console.log('Using fallback cart total:', cartTotal);
            }
            
            console.log('Final calculated cart total:', cartTotal);
            updateOrderSummary(cartTotal);
            
        }).fail(function(){
            console.error('Failed to refresh cart header');
            // Fallback calculation using server-side cart data
            var cartTotal = 0;
            @if(isset($cart) && count($cart) > 0)
                @foreach($cart as $key => $cartItem)
                    cartTotal += ({{ $cartItem['main_price'] }} + {{ $cartItem['attribute_price'] ?? 0 }}) * {{ $cartItem['qty'] }};
                @endforeach
            @endif
            console.log('Fallback server-side calculation:', cartTotal);
            updateOrderSummary(cartTotal || parseFloat($('#init_cart_total').val() || 0));
        });
    }
    
    function updateOrderSummary(cartTotal) {
        // Ensure cartTotal is a valid number
        cartTotal = parseFloat(cartTotal) || 0;
        
        // server-provided values
        var serverTax = parseFloat($('#init_tax_total').val()||0) || 0;
        var serverDiscount = parseFloat($('#server_discount').val()||0) || 0;
        
        // shipping price: prefer selected shipping radio, else server default
        var $sel = $('input[name="shipping_id"]:checked');
        var methodPrice = $sel.length ? (parseFloat($sel.data('price')) || 0) : 0;
        
        // address shipping amount (computed from Division/District/Thana)
        var addressShipping = parseFloat($('#address_shipping_amount').val()||0) || 0;

        // state tax calculation: server may provide either fixed or percent
        var stateType = $('#server_state_type').val() || '';
        var statePriceCfg = parseFloat($('#server_state_price').val()||0) || 0;
        var stateTax = 0;
        if(stateType === 'fixed'){
            stateTax = statePriceCfg;
        } else if(stateType === 'percent'){
            stateTax = (cartTotal * statePriceCfg) / 100;
        }

        var grandTotal = cartTotal + serverTax + stateTax + methodPrice + addressShipping - serverDiscount;
        
        console.log('Order Summary Update:', {
            cartTotal: cartTotal,
            serverTax: serverTax,
            methodPrice: methodPrice,
            addressShipping: addressShipping,
            stateTax: stateTax,
            serverDiscount: serverDiscount,
            grandTotal: grandTotal
        });

        // Update Order Summary table
        var $summaryTable = $('.widget-order-summary table');
        if($summaryTable.length){
            // Update cart subtotal (first row) - ensure it shows the actual cart total
            var cartSubtotalFormatted = formatCurrency(cartTotal);
            $summaryTable.find('tr').first().find('td').last().text(cartSubtotalFormatted);
            console.log('Updated cart subtotal to:', cartSubtotalFormatted);
            
            // Update shipping if visible
            if(methodPrice > 0) {
                $('.set__shipping_price').text(formatCurrency(methodPrice));
                $('.set__shipping_price_tr').removeClass('d-none');
            } else {
                $('.set__shipping_price_tr').addClass('d-none');
            }
            
            // Update address shipping if visible
            if(addressShipping > 0) {
                $('.address_shipping_price').text(formatCurrency(addressShipping));
                $('.address_shipping_tr').removeClass('d-none');
            } else {
                $('.address_shipping_tr').addClass('d-none');
            }
            
            // Update grand total (last row)
            var grandTotalFormatted = formatCurrency(grandTotal);
            $('.grand_total_set').text(grandTotalFormatted);
            console.log('Updated grand total to:', grandTotalFormatted);
        }
        
        // Also update any other total displays
        $('.main-price').text(formatCurrency(grandTotal));
        
        // Update hidden input for form submission
        $('#init_cart_total').val(cartTotal);
    }

    // single-select size box behavior (exclusive) within its color container
    // Also: when a size is clicked we immediately add that color+size to cart using the local qty
    $(document).on('click', '.color-sizes-container .size-box', function(e){
        e.stopPropagation();
        var $btn = $(this);
        var $holder = $btn.closest('.color-sizes-container');
        $holder.find('.size-box').removeClass('active');
        $btn.addClass('active');
        // store selected size on holder
        $holder.data('selected-size-option', $btn.data('size-option'));
        $holder.data('selected-size-attr', $btn.data('size-attr'));

        // Prepare add-to-cart params using this holder's quantity
        var colorId = $holder.data('color-option');
        // colorAttr is stored on the corresponding label .color-option-item
        var colorAttr = $('.color-option-item[data-color-option="'+colorId+'"]').data('color-attr');
        var sizeId = $btn.data('size-option');
        var sizeAttr = $btn.data('size-attr');
        var qty = parseInt($holder.find('.qtyValueLocal').val()||1) || 1;

        // Prevent accidental double-add by disabling this size button during request
        $btn.prop('disabled', true).addClass('opacity-75');

        var params = {
            item_id: $('#item_id').val(),
            options_ids: colorId+','+sizeId,
            attribute_ids: (colorAttr||'')+','+(sizeAttr||''),
            quantity: qty,
            absolute_qty: 1 // Use absolute quantity update for consistency
        };

        // Use same add-to-cart route as the Add Selected button
        $.get("{{ route('product.addcart') }}", params).done(function(){
            console.log('Size button click - item added to cart successfully');
            // Refresh cart header
            $.get("{{ route('front.header.cart') }}").done(function(html){
                $('.cart_view_header').html(html);
                var cnt = $('.cart_view_header').find('.entry').length || 0;
                $('.cart_count').text(cnt);
            });
            // re-enable button and refresh totals
            $btn.prop('disabled', false).removeClass('opacity-75');
            updateTotalsFromLocalQty();
        }).fail(function(){
            $btn.prop('disabled', false).removeClass('opacity-75');
            alert('Failed to add item');
        });
    });

    // quick qty inc/dec
    $(document).on('click', '.btn-increase-quick', function(){
        var $i = $(this).closest('.input-group').find('.quick-qty');
        $i.val(Math.max(1, parseInt($i.val()||0) + 1));
    });
    $(document).on('click', '.btn-decrease-quick', function(){
        var $i = $(this).closest('.input-group').find('.quick-qty');
        $i.val(Math.max(1, parseInt($i.val()||0) - 1));
    });

    // Per-color product-quantity controls: ensure clicks operate on the local holder only
    // Handlers for cloned/local qty controls to avoid double increments and accidental label toggles
    // Helper: set absolute quantity for a variant in cart
    function setVariantQuantityInCart($holder, $triggerEl) {
        if(!$holder || $holder.length === 0) {
            return;
        }
        var colorId = $holder.data('color-option');
        var sizeId = $holder.data('selected-size-option');
        
        // If no size is selected, automatically select the first available size
        if(!sizeId) {
            var $firstSizeBtn = $holder.find('.size-box').first();
            if($firstSizeBtn.length) {
                // Clear any existing active size
                $holder.find('.size-box').removeClass('active');
                // Mark the first size as active
                $firstSizeBtn.addClass('active');
                // Store the selected size data on the holder
                $holder.data('selected-size-option', $firstSizeBtn.data('size-option'));
                $holder.data('selected-size-attr', $firstSizeBtn.data('size-attr'));
                sizeId = $firstSizeBtn.data('size-option');
            } else {
                return; // no sizes available
            }
        }
        
        var colorAttr = $('.color-option-item[data-color-option="'+colorId+'"]').data('color-attr');
        var sizeAttr = $holder.data('selected-size-attr');
        var qty = parseInt($holder.find('.qtyValueLocal').val()||1) || 1;
        
        // Use absolute quantity update with absolute_qty flag to prevent incrementing
        var params = {
            item_id: $('#item_id').val(),
            options_ids: colorId+','+sizeId,
            attribute_ids: (colorAttr||'')+','+(sizeAttr||''),
            quantity: qty,
            absolute_qty: 1 // Flag to indicate this is an absolute quantity update, not increment
        };
        // disable trigger while request is in-flight
        if($triggerEl && $triggerEl.length) { $triggerEl.prop('disabled', true).addClass('opacity-75'); }
        $.get("{{ route('product.addcart') }}", params).done(function(response){
            console.log('Variant quantity updated in cart');
            // Refresh cart header
            $.get("{{ route('front.header.cart') }}").done(function(html){
                $('.cart_view_header').html(html);
                var cnt = $('.cart_view_header').find('.entry').length || 0;
                $('.cart_count').text(cnt);
            });
            // re-enable trigger and update totals
            if($triggerEl && $triggerEl.length) { $triggerEl.prop('disabled', false).removeClass('opacity-75'); }
            updateTotalsFromLocalQty();
        }).fail(function(){
            if($triggerEl && $triggerEl.length) { $triggerEl.prop('disabled', false).removeClass('opacity-75'); }
            console.error('Failed to update variant quantity in cart');
        });
    }

    // Helper: add/update a variant (color+size) to cart using a holder element
    function addVariantToCartByHolder($holder, $triggerEl){
        if(!$holder || $holder.length === 0) return;
        var colorId = $holder.data('color-option');
        var sizeId = $holder.data('selected-size-option');
        if(!sizeId) return; // nothing selected for this holder
        var colorAttr = $('.color-option-item[data-color-option="'+colorId+'"]').data('color-attr');
        var sizeAttr = $holder.data('selected-size-attr');
        var qty = parseInt($holder.find('.qtyValueLocal').val()||1) || 1;
        var params = {
            item_id: $('#item_id').val(),
            options_ids: colorId+','+sizeId,
            attribute_ids: (colorAttr||'')+','+(sizeAttr||''),
            quantity: qty,
            absolute_qty: 1 // Use absolute quantity update for consistency
        };
        // disable trigger while request is in-flight
        if($triggerEl && $triggerEl.length) { $triggerEl.prop('disabled', true).addClass('opacity-75'); }
        $.get("{{ route('product.addcart') }}", params).done(function(){
            $.get("{{ route('front.header.cart') }}").done(function(html){
                $('.cart_view_header').html(html);
                var cnt = $('.cart_view_header').find('.entry').length || 0;
                $('.cart_count').text(cnt);
            });
        }).always(function(){
            if($triggerEl && $triggerEl.length) { $triggerEl.prop('disabled', false).removeClass('opacity-75'); }
            updateTotalsFromLocalQty();
        });
    }
    
    $(document).on('click', '.color-sizes-container .increaseQtyLocal', function(e){
        e.stopPropagation();
        var $holder = $(this).closest('.color-sizes-container');
        var $input = $holder.find('.qtyValueLocal');
        $input.val(Math.max(1, parseInt($input.val()||0) + 1));
        updateTotalsFromLocalQty();
        // set absolute quantity in server cart for this variant
        setVariantQuantityInCart($holder, $(this));
    });
    
    $(document).on('click', '.color-sizes-container .decreaseQtyLocal', function(e){
        e.stopPropagation();
        var $holder = $(this).closest('.color-sizes-container');
        var $input = $holder.find('.qtyValueLocal');
        var currentVal = parseInt($input.val()||0);
        if(currentVal > 1) {
            $input.val(currentVal - 1);
            updateTotalsFromLocalQty();
            // set absolute quantity in server cart for this variant
            setVariantQuantityInCart($holder, $(this));
        }
    });
    
    // update totals when local qty input changed directly
    $(document).on('input change', '.qtyValueLocal', function(e){
        e.stopPropagation();
        var v = parseInt($(this).val()||0);
        if(isNaN(v) || v < 1) $(this).val(1);
        updateTotalsFromLocalQty();
        // update server cart for this variant using absolute quantity
        var $holder = $(this).closest('.color-sizes-container');
        setVariantQuantityInCart($holder, $(this));
    });
    
    // prevent product-quantity clicks (original and cloned) from toggling parent color checkbox
    $(document).on('click', '.color-sizes-container .product-quantity, .color-sizes-container .product-qty-inline, .color-sizes-container .increaseQty, .color-sizes-container .decreaseQty, .color-sizes-container .increaseQtyLocal, .color-sizes-container .decreaseQtyLocal, .color-sizes-container .qtyValueLocal', function(e){ e.stopPropagation(); });

    // Handle main product quantity controls (for products without color/size variants)
    $(document).on('click', '.product-quantity .increaseQty', function(e){
        if ($(this).closest('.color-sizes-container').length) return; // Skip if inside variant container
        var $input = $('.product-quantity .qtyValue');
        var newVal = Math.max(1, parseInt($input.val()||0) + 1);
        $input.val(newVal);
        updateTotalsFromLocalQty();
    });
    
    $(document).on('click', '.product-quantity .decreaseQty', function(e){
        if ($(this).closest('.color-sizes-container').length) return; // Skip if inside variant container
        var $input = $('.product-quantity .qtyValue');
        var newVal = Math.max(1, parseInt($input.val()||0) - 1);
        $input.val(newVal);
        updateTotalsFromLocalQty();
    });
    
    $(document).on('input change', '.product-quantity .qtyValue', function(e){
        if ($(this).closest('.color-sizes-container').length) return; // Skip if inside variant container
        var v = parseInt($(this).val()||0);
        if(isNaN(v) || v < 1) $(this).val(1);
        
        // If there are variant quantities, distribute the main quantity to the first active variant
        var $firstLocalQty = $('.qtyValueLocal:visible:first');
        if ($firstLocalQty.length) {
            $firstLocalQty.val(v);
            // Clear other local quantities to avoid confusion
            $('.qtyValueLocal').not($firstLocalQty).val(0);
        }
        
        updateTotalsFromLocalQty();
    });

    // Add single selected size for a specific color to cart, using main qty
    $(document).on('click', '.add-color-variants', function(){
        var colorOption = $(this).data('color-option');
        var colorAttr = $(this).data('color-attr');
        var $holder = $('.color-sizes-container[data-color-option="'+colorOption+'"]');
        var sizeOption = $holder.data('selected-size-option');
        var sizeAttr = $holder.data('selected-size-attr');
        if(!sizeOption){ alert('Please select a size for the chosen color'); return; }

        // get qty from this color holder's product quantity input (local)
        var qty = parseInt($holder.find('.qtyValueLocal').val()||1);
        var params = {
            item_id: $('#item_id').val(),
            options_ids: colorOption+','+sizeOption,
            attribute_ids: colorAttr+','+sizeAttr,
            quantity: qty,
            absolute_qty: 1 // Use absolute quantity update for consistency
        };
        var $btn = $(this);
        $btn.prop('disabled', true).text('Adding...');
        $.get("{{ route('product.addcart') }}", params).done(function(){
            console.log('Add Selected - item added successfully');
            // Refresh cart header
            $.get("{{ route('front.header.cart') }}").done(function(html){
                $('.cart_view_header').html(html);
                var cnt = $('.cart_view_header').find('.entry').length || 0;
                $('.cart_count').text(cnt);
            });
            $btn.prop('disabled', false).text(tAddSelected);
            updateTotalsFromLocalQty();
        }).fail(function(){
            alert('Failed to add item');
            $btn.prop('disabled', false).text(tAddSelected);
        });
    });

    // Modal form submit handler — use AJAX and expect JSON responses for debug/fast-feedback
    $(document).on('click', '.modal button[type="submit"], .modal input[type="submit"]', function(e){
        try {
            var $form = $(this).closest('form');
            console.log('modal submit clicked', { action: $form.attr('action'), dataArray: $form.serializeArray(), serialized: $form.serialize() });
        } catch(err) { console.error('modal submit log error', err); }
    });

    // Intercept modal form submissions on this page and POST via AJAX (expects JSON)
    $(document).on('submit', '.modal form', function(e){
        e.preventDefault();
        var $form = $(this);
        var action = $form.attr('action');
        var data = $form.serialize();
        console.log('AJAX modal submit', { action: action, serialized: data });

        $.ajax({
            url: action,
            method: 'POST',
            data: data,
            dataType: 'text', // fetch as text so we can inspect headers/body before parsing
            success: function(responseText, status, xhr){
                console.log('AJAX success', { status: status, statusCode: xhr.status });
                console.log('Response headers:', xhr.getAllResponseHeaders());
                console.log('Full response:', responseText);
                var contentType = xhr.getResponseHeader('Content-Type') || '';
                console.log('Content-Type:', contentType);
                if (contentType.indexOf('application/json') !== -1 || contentType.indexOf('+json') !== -1) {
                    try {
                        var json = JSON.parse(responseText);
                        console.log('Parsed JSON:', json);
                        if (json.redirect) {
                            window.location.href = json.redirect;
                            return;
                        }
                        if (json.message) {
                            alert(json.message);
                            return;
                        }
                    } catch(err){ console.error('JSON parse error on success handler', err); }
                }
                // If server returned HTML (or non-JSON) — log and optionally show it
                console.log('Non-JSON response received from server; inspect server output in console.');
                // For HTML responses (likely a successful redirect page), redirect to success
                if (responseText.indexOf('<!DOCTYPE') !== -1 || responseText.indexOf('<html') !== -1) {
                    console.log('HTML response detected, redirecting to success page');
                    window.location.href = '{{ route("front.checkout.success") }}';
                }
            },
            error: function(xhr, status, error){
                console.error('AJAX Error:', status, error);
                console.error('Response body:', xhr.responseText);
                console.log('Response headers:', xhr.getAllResponseHeaders());
                var contentType = xhr.getResponseHeader('Content-Type') || '';
                if (contentType.indexOf('application/json') !== -1 || contentType.indexOf('+json') !== -1) {
                    try {
                        var json = JSON.parse(xhr.responseText);
                        console.log('Parsed JSON (error):', json);
                        if (json.redirect) {
                            window.location.href = json.redirect; return;
                        }
                        if (json.message) { alert(json.message); return; }
                    } catch(err){ console.error('JSON parse error in error handler', err); }
                }
                // Final fallback
                alert('Request failed. See console for details.');
            }
        });
    });

    // Terms checkbox handler
    $(document).on("click", "#trams__condition_single", function() {
        if ($(this).is(':checked')) {
            $('.single_checkout_payment').attr('id', "single_checkout_payment").prop('disabled', false);
        } else {
            $('.single_checkout_payment').removeAttr('id').prop('disabled', true);
        }
    });

    // Pathao City change: populate zones and enable zone select
    $(document).on('change', '#pathao_city_id', function(){
        const cityId = $(this).val();
        const $z = $('#pathao_zone_id');
        $z.val('').trigger('change.select2');
        $('#pathao_area_id').val('').trigger('change.select2').prop('disabled', true);
        $('.address_shipping_tr').addClass('d-none');
        $('.address_shipping_price').text('0.00');
        $('#address_shipping_amount').val('0');
        recalcTotals();

        if(!cityId){ 
            $z.prop('disabled', true); 
            $('.payment-shipping-option-wrap .shipping-group').show(); 
            return; 
        }
        
        $.get("{{ route('front.pathao.zones') }}", { city_id: cityId })
            .done(function(zones){
                $z.empty().append('<option value="">Select</option>');
                zones.forEach(function(zone){ 
                    $z.append('<option value="'+zone.zone_id+'">'+zone.zone_name+'</option>'); 
                });
                $z.prop('disabled', false).trigger('change.select2');
                console.log('Zones loaded for city', cityId, zones);
            })
            .fail(function(xhr, status, error){
                console.error('Failed to load zones:', { xhr, status, error });
                toastr.error('অঞ্চল লোড করতে সমস্যা হয়েছে', 'ত্রুটি');
            });
    });

    // Pathao Zone change: populate areas and compute shipping (area optional)
    $(document).on('change', '#pathao_zone_id', function(){
        const zoneId = $(this).val();
        const cityId = $('#pathao_city_id').val();
        const $a = $('#pathao_area_id');
        $a.prop('disabled', !zoneId).val('').trigger('change.select2');
        $('.address_shipping_tr').addClass('d-none');
        $('.address_shipping_price').text('0.00');
        $('#address_shipping_amount').val('0');
        recalcTotals();

        if(!zoneId) return;
        
        // Get areas
        $.get("{{ route('front.pathao.areas') }}", { zone_id: zoneId })
            .done(function(areas){
                $a.empty().append('<option value="">Select</option>');
                areas.forEach(function(area){ 
                    $a.append('<option value="'+area.area_id+'">'+area.area_name+'</option>'); 
                });
                $a.trigger('change.select2');
                console.log('Areas loaded for zone', zoneId, areas);
            })
            .fail(function(xhr, status, error){
                console.error('Failed to load areas:', { xhr, status, error });
                toastr.error('এলাকা লোড করতে সমস্যা হয়েছে', 'ত্রুটি');
            });

        // Calculate Pathao shipping price
        if(cityId && zoneId) {
            // Get district ID for accurate markup calculation
            var districtId = $('#district_id').val() || '';
            
            $.get("{{ route('front.pathao.price') }}", { 
                city_id: cityId, 
                zone_id: zoneId,
                district_id: districtId
            })
                .done(function(resp){
                    console.log('Pathao price response (manual):', resp);
                    if(resp.status === 'success' && resp.price) {
                        // Display price with breakdown if available
                        var displayText = resp.formatted || '৳' + resp.price;
                        
                        // Add breakdown info if available
                        if(resp.breakdown) {
                            // Create a more user-friendly display
                            var markup = parseFloat(resp.breakdown.markup) || 0;
                            var basePrice = parseFloat(resp.breakdown.base_price) || parseFloat(resp.breakdown.base) || 0;
                            var total = parseFloat(resp.breakdown.total) || parseFloat(resp.price) || 0;
                            
                            // Show markup if there is one
                            if(markup > 0) {
                                var markupText = ' <small class="text-info">(+৳' + markup.toFixed(2) + ' markup)</small>';
                                displayText = '৳' + total.toFixed(2) + markupText;
                            } else {
                                displayText = '৳' + total.toFixed(2);
                            }
                            
                            // Create detailed tooltip
                            var tooltipText = 'Pathao Base Price: ৳' + basePrice.toFixed(2);
                            if(markup > 0) {
                                tooltipText += '\nLocation Markup: ৳' + markup.toFixed(2);
                                tooltipText += '\n' + (resp.is_dhaka ? 'Dhaka/Dhaka-Metro area' : 'Outside Dhaka area');
                            }
                            tooltipText += '\nTotal Price: ৳' + total.toFixed(2);
                            
                            displayText = '<span data-bs-toggle="tooltip" data-bs-placement="top" title="' + 
                                         tooltipText + '">' + displayText + '</span>';
                        }
                        
                        $('.address_shipping_price').html(displayText);
                        
                        // Initialize tooltips if Bootstrap is available
                        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                            setTimeout(function() {
                                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                                tooltipTriggerList.map(function (tooltipTriggerEl) {
                                    return new bootstrap.Tooltip(tooltipTriggerEl);
                                });
                            }, 100);
                        }
                        $('#address_shipping_amount').val(resp.price);
                        $('.address_shipping_tr').removeClass('d-none');
                        $('.payment-shipping-option-wrap .shipping-group').hide();
                        $('input[name="shipping_id"]').prop('checked', false);
                        recalcTotals();
                        
                        // Show breakdown in console for debugging
                        if(resp.breakdown) {
                            console.log('Pathao Price Breakdown:', {
                                base_price: resp.base_price,
                                markup: resp.markup,
                                final_price: resp.price,
                                location_type: resp.is_dhaka ? 'Dhaka' : 'Outside Dhaka'
                            });
                        }
                    } else {
                        console.error('Pathao price calculation failed (manual):', resp);
                        if(typeof toastr !== 'undefined') {
                            toastr.error('ডেলিভারি চার্জ গণনা করতে সমস্যা হয়েছে', 'ত্রুটি');
                        }
                        $('.payment-shipping-option-wrap .shipping-group').show();
                    }
                })
                .fail(function(xhr, status, error){
                    console.error('Pathao price calculation error (manual):', { xhr, status, error });
                    if(typeof toastr !== 'undefined') {
                        toastr.error('ডেলিভারি চার্জ গণনা করতে সমস্যা হয়েছে', 'ত্রুটি');
                    }
                    $('.payment-shipping-option-wrap .shipping-group').show();
                });
        }
    });

    // Pathao Area change: optional calculation
    $(document).on('change', '#pathao_area_id', function(){
        const areaId = $(this).val();
        console.log('Area selected:', areaId);
        // For now, just show shipping methods since area is optional
        $('.payment-shipping-option-wrap .shipping-group').show();
    });

    // Payment Gateway Selection Enhancement
    $(document).on('change', 'input[name="payment_gateway"]', function(){
        // Remove active class from all labels
        $('.payment-shipping-option-wrap .option-group label').removeClass('active');
        
        // Add active class to selected label
        $(this).closest('label').addClass('active');
        
        console.log('Payment gateway selected:', $(this).val());
    });

    // Initialize payment gateway styling on page load
    setTimeout(function(){
        $('input[name="payment_gateway"]:checked').closest('label').addClass('active');
    }, 100);

});
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection
 