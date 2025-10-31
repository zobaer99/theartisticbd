<aside class="order-summary-sidebar">
{{--
 @Author: Anwarul
 @Date: 2025-08-13 15:10:28
 @LastEditors: Anwarul
 @LastEditTime: 2025-08-17 12:05:10
 @Description: Innova IT
 --}}
    <div class="padding-top-2x hidden-lg-up"></div>

    <section class="card widget widget-featured-posts widget-order-summary p-4">
        <h3 class="widget-title">{{ __('অর্ডার সারাংশ') }}</h3>
        
        @php
            $free_shipping = DB::table('shipping_services')->whereStatus(1)->whereIsCondition(1)->first();
        @endphp

        @if($free_shipping)
            @if($free_shipping->minimum_price >= $cart_total)
                <p class="free-shippin-aa"><em>{{ __('অর্ডার পরবর্তী ফ্রি শিপিং') }}
                    {{ PriceHelper::setCurrencyPrice($free_shipping->minimum_price) }}</em></p>
            @endif
        @endif

        <table class="table">
            <tr>
                <td>{{ __('কার্ট সর্বমোট') }}:</td>
                <td class="text-gray-dark">{{ PriceHelper::setCurrencyPrice($cart_total) }}</td>
            </tr>

            @if($discount)
                <tr>
                    <td>{{ __('কুপন ডিসকাউন্ট') }}:</td>
                    <td class="text-danger">-{{ PriceHelper::setCurrencyPrice($discount['discount']) }}</td>
                </tr>
            @endif

            <!-- Shipping Options Section -->
            <tr >
                <td>
                    <p class="mb-2">{{ __('শিপিং পদ্ধতি নির্বাচন করুন') }}</p>
                </td>
                <td colspan="2">
                    <div class=" mb-2">
                        <p class="mb-2">&nbsp;&nbsp;&nbsp;</p>
                        <div class="payment-shipping-option-wrap">
                            <div class="option-group shipping-group">
                                @php
                                    $is_free_shipping = false;
                                    if(isset($free_shipping) && $free_shipping){
                                        $is_free_shipping = $cart_total >= $free_shipping->minimum_price;
                                    }
                                    $methods = DB::table('shipping_services')->whereStatus(1)->get();
                                @endphp
                                @foreach($methods as $method)
                                    @php
                                        // For free shipping method, check if condition is met
                                        if($method->is_condition == 1) {
                                            $price = $is_free_shipping ? 0 : $method->price;
                                            $display_price = $is_free_shipping ? __('ফ্রি') : PriceHelper::setCurrencyPrice($method->price);
                                        } else {
                                            $price = $method->price;
                                            $display_price = $method->price == 0 ? __('ফ্রি') : PriceHelper::setCurrencyPrice($method->price);
                                        }
                                    @endphp
                                    <label>
                                        <input type="radio" name="shipping_id" value="{{ $method->id }}"
                                               data-price="{{ $price }}"
                                               data-href="{{ route('front.shipping.setup') }}"
                                               {{ $loop->first ? 'checked' : '' }}>
                                        <p class="option">
                                            {{ $method->title }} ({{ $display_price }})
                                        </p>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            <tr class="set__shipping_price_tr">
                <td>{{ __('শিপিং') }}:</td>
                <td class="text-gray-dark set__shipping_price">
                    @php
                        $firstMethod = $methods->first();
                        if($firstMethod) {
                            if($firstMethod->is_condition == 1) {
                                $firstPrice = $is_free_shipping ? 0 : $firstMethod->price;
                            } else {
                                $firstPrice = $firstMethod->price;
                            }
                        } else {
                            $firstPrice = 0;
                        }
                    @endphp
                    {{ PriceHelper::setCurrencyPrice($firstPrice) }}
                </td>
            </tr>

{{-- 
            <tr class="set__shipping_price_tr d-none">
                <td>{{ __('Shipping') }}:</td>
                <td class="text-gray-dark set__shipping_price">0.00</td>
            </tr>
             --}}
            <tr class="address_shipping_tr d-none">
                <td>{{ __('Shipping') }}:</td>
                <td class="text-gray-dark address_shipping_price">0.00</td>
            </tr>
            
            <tr>
                <td class="text-lg text-primary">{{ __('পরিশোধযোগ্য পরিমাণ') }}</td>
                <td class="text-lg text-primary grand_total_set">
                    @php
                        $total_with_shipping = $grand_total;
                        if($firstMethod) {
                            if($firstMethod->is_condition == 1) {
                                $shipping_amount = $is_free_shipping ? 0 : $firstMethod->price;
                            } else {
                                $shipping_amount = $firstMethod->price;
                            }
                            $total_with_shipping = $grand_total + $shipping_amount;
                        }
                    @endphp
                    {{ PriceHelper::setCurrencyPrice($total_with_shipping) }}
                </td>
            </tr>
        </table>
    </section>

    {{-- @if(PriceHelper::CheckDigital())
    <section class="card widget widget-order-summary p-4">
        <h3 class="widget-title">{{ __('Choose Shipping Method') }}</h3>
        <div class="row">
            <div class="col-sm-12">
                <div class="payment-shipping-option-wrap">
                    <div class="option-group shipping-group">
                        @php
                            $is_free_shipping = false;
                            if(isset($free_shipping) && $free_shipping){
                                $is_free_shipping = $grand_total >= $free_shipping->minimum_price;
                            }
                            $methods = DB::table('shipping_services')->whereStatus(1)->get();
                        @endphp
                        @foreach($methods as $method)
                            @php
                                $price = $is_free_shipping ? 0 : $method->price;
                            @endphp
                            <label>
                                <input type="radio" name="shipping_id" value="{{ $method->id }}"
                                    data-price="{{ $price }}"
                                    data-href="{{ route('front.shipping.setup') }}"
                                    {{ $loop->first ? 'checked' : '' }}>
                                <p class="option">
                                    {{ $method->title }} ({{ $is_free_shipping ? __('Free') : $method->price }})
                                </p>
                            </label>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif --}}
</aside>