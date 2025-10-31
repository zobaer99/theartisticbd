<!-- COD Modal -->
<div class="modal fade" id="cod" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">{{ __('Cash On Delivery') }}</h6>
                <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="{{ url(route('front.checkout.submit')) }}" method="POST">
                @csrf
                <input type="hidden" name="payment_method" value="Cash On Delivery">
                <input type="hidden" name="shipping_id" value="" class="shipping_id_setup">
                
                <div class="modal-body">
                    <p>{{ PriceHelper::GatewayText('cod') }}</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-sm" type="button" data-bs-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>
                    <button class="btn btn-primary btn-sm" type="submit">
                        {{ __('Confirm Order') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SSLCommerz Modal -->
<div class="modal fade" id="sslcommerz" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">{{ __('SSLCommerz Payment') }}</h6>
                <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="{{ route('front.sslcommerz.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="payment_method" value="SSLCommerz">
                <input type="hidden" name="shipping_id" value="" class="shipping_id_setup">
                
                <div class="modal-body">
                    <p>{{ PriceHelper::GatewayText('sslcommerz') }}</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-sm" type="button" data-bs-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>
                    <button class="btn btn-primary btn-sm" type="submit">
                        {{ __('Proceed to Payment') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add similar modals for other payment methods -->