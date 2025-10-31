<section class="card widget widget-order-summary p-4 mb-0 payment-method-section">
    <h3 class="widget-title">{{ __('পেমেন্ট পদ্ধতি নির্বাচন করুন') }}</h3>
  @php
                    $gateways = DB::table('payment_settings')->whereStatus(1)->get();
                @endphp
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

            @if($setting->is_privacy_trams == 1)
                <div class="form-group mt-4">
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

            <button id="single_checkout_payment" disabled class="btn btn-primary mt-4 single_checkout_payment d-block mx-auto w-50">
                <span>{{ __('Buy now') }}</span>
            </button>
        </div>
    </div>
</section>