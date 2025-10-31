<?php

namespace App\Http\Controllers\Front;

use App\{
    Models\Order,
    Models\PaymentSetting,
    Traits\StripeCheckout,
    Traits\MollieCheckout,
    Traits\PaypalCheckout,
    Traits\PaystackCheckout,
    Http\Controllers\Controller,
    Http\Requests\PaymentRequest,
    Traits\CashOnDeliveryCheckout,
    Traits\BankCheckout,
     Repositories\Front\CartRepository
};
use App\Helpers\PriceHelper;
use App\Helpers\SmsHelper;
use App\Models\Currency;
use App\Models\Item;
use App\Models\Setting;
use App\Models\ShippingService;
use App\Models\State;
use App\Models\Division;
use App\Models\District;
use App\Models\Thana;
use App\Models\ShippingLocationPrice;
use App\Services\PathaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Mollie\Laravel\Facades\Mollie;
use Stripe\Price;

class CheckoutController extends Controller
{
    protected $repository;

    use CashOnDeliveryCheckout;

    public function __construct(CartRepository $repository)
    {
        $setting = Setting::first();
        if ($setting->is_guest_checkout != 1) {
            $this->middleware('auth');
        }
        $this->repository = $repository;
        $this->middleware('localize');
    }

    public function checkoutPage()
    {

        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }
        $data['user'] = Auth::user();
        $cart = Session::get('cart');

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $items) {

            $total += ($items['main_price'] + $items['attribute_price']) * $items['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item) * $items['qty'];
            }
        }

        $shipping = [];

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        if (!PriceHelper::Digital()) {
            $shipping = null;
        }

        $grand_total = ($cart_total  + $total_tax);
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $state_tax = Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0;
        $grand_total = $grand_total + $state_tax;


        $total_amount = $grand_total;

    $data['cart'] = $cart;
        $data['cart_total'] = $cart_total;
        $data['grand_total'] = $total_amount;
        $data['discount'] = $discount;
        $data['shipping'] = $shipping;
        $data['tax'] = $total_tax;
        $data['payments'] = PaymentSetting::whereStatus(1)->get();
    // Pathao cities for shipping selection
    $pathaoService = new PathaoService();
    $pathaoResponse = $pathaoService->getCities();
    $data['pathao_cities'] = isset($pathaoResponse['data']['data']) ? $pathaoResponse['data']['data'] : [];

        return view('front.checkout.index', $data);
    }

    public function menualcheckout(Request $request, $id, $slug=null)
    {
      $item = Item::where('id',$id)->first();
      if(empty($item)){
             return abort(404);
       }
        if ($item->item_type == 'normal') {
            if ($item->stock < (int)1) {
                $data = ['message' => 'Product Out Of Stock', 'status' => 'outStock'];
                return $data;
            }
        }

        // Clear any existing cart data for manual checkout
        Session::forget('cart');
        $cart = [];

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;
      
        // Since cart is cleared, all totals start at 0
        // Users will need to add items manually to calculate totals

        $shipping = [];

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        if (!PriceHelper::Digital()) {
            $shipping = null;
        }

        $grand_total = ($cart_total);
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $state_tax = Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0;
        $grand_total = $grand_total + $state_tax;

        $total_amount = $grand_total;

        $data['cart'] = $cart;
        $data['item'] = $item;
        $data['cart_total'] = $cart_total;
        $data['grand_total'] = $total_amount;
        $data['discount'] = $discount;
        $data['shipping'] = $shipping;
        $data['tax'] = $total_tax;
        $data['payments'] = PaymentSetting::whereStatus(1)->get();
        
        // Pathao cities for shipping selection
        $pathaoService = new PathaoService();
        $pathaoResponse = $pathaoService->getCities();
        $data['pathao_cities'] = isset($pathaoResponse['data']['data']) ? $pathaoResponse['data']['data'] : [];

        return view('front.checkout.menualcheckout',$data);
    }

    public function ship_address()
    {
        $setting = Setting::first();

        if ($setting->is_single_checkout == 1) {
            return redirect(route("front.checkout"));
        }


        Session::forget('shipping_address');
        if (Session::has('shipping_address')) {
            return redirect(route('front.checkout.payment'));
        }



        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }
        $data['user'] = Auth::user();
        $cart = Session::get('cart');

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $items) {

            $total += ($items['main_price'] + $items['attribute_price']) * $items['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item) * $items['qty'];
            }
        }

        $shipping = [];

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        if (!PriceHelper::Digital()) {
            $shipping = null;
        }

        $grand_total = $cart_total + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $state_tax = Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0;
        $grand_total = $grand_total + $state_tax;

        $total_amount = $grand_total;
        $data['cart'] = $cart;
        $data['cart_total'] = $cart_total;
        $data['grand_total'] = $total_amount;
        $data['discount'] = $discount;
        $data['shipping'] = $shipping;
        $data['tax'] = $total_tax;
        $data['payments'] = PaymentSetting::whereStatus(1)->get();

        return view('front.checkout.billing', $data);
    }



    public function billingStore(Request $request)
    {
        // laravel validation
        $request->validate([
            'bill_first_name' => 'required',
           // 'bill_last_name' => 'required',
            'bill_email' => 'nullable|email',
            'bill_phone' => 'required',
            'bill_address1' => 'required',
            // 'bill_city' => 'required',
            // 'bill_zip' => 'required',
        ]);

        if ($request->same_ship_address) {
            Session::put('billing_address', $request->all());

            if (PriceHelper::CheckDigital()) {
                $shipping = [
                    "ship_first_name" => $request->bill_first_name,
                    "ship_last_name" => $request->bill_last_name,
                    "ship_email" => $request->bill_email,
                    "ship_phone" => $request->bill_phone,
                    "ship_company" => $request->bill_company,
                    "ship_address1" => $request->bill_address1,
                    "ship_address2" => $request->bill_address2,
                    "ship_zip" => $request->bill_zip,
                    "ship_city" => $request->bill_city,
                    "ship_country" => $request->bill_country,
                ];
            } else {
                $shipping = [
                    "ship_first_name" => $request->bill_first_name,
                    "ship_last_name" => $request->bill_last_name,
                    "ship_email" => $request->bill_email,
                    "ship_phone" => $request->bill_phone,
                ];
            }
            Session::put('shipping_address', $shipping);
        } else {
            Session::put('billing_address', $request->all());
            Session::forget('shipping_address');
        }

        if (Session::has('shipping_address')) {
            return redirect()->route('front.checkout.payment');
        } else {
            return redirect()->route('front.checkout.shipping');
        }
    }


    public function shipping()
    {

        if (Session::has('shipping_address')) {
            return redirect(route('front.checkout.payment'));
        }

        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }
        $data['user'] = Auth::user();
        $cart = Session::get('cart');

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $items) {

            $total += ($items['main_price'] + $items['attribute_price']) * $items['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item) * $items['qty'];
            }
        }

        $shipping = [];

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        if (!PriceHelper::Digital()) {
            $shipping = null;
        }

        $grand_total = $cart_total + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $state_tax = Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0;
        $grand_total = $grand_total + $state_tax;

        $total_amount = $grand_total;
        $data['cart'] = $cart;
        $data['cart_total'] = $cart_total;
        $data['grand_total'] = $total_amount;
        $data['discount'] = $discount;
        $data['shipping'] = $shipping;
        $data['tax'] = $total_tax;
        $data['payments'] = PaymentSetting::whereStatus(1)->get();
        return view('front.checkout.shipping', $data);
    }

    /**
     * Calculate authoritative totals for current selection (AJAX)
     * Expects payload: items => [{ item_id, options_ids:[], attribute_ids:[], quantity }], shipping_id, division_id, district_id, thana_id
     * Returns JSON: { subtotal, tax, shipping, address_shipping, state_price, discount, grand_total, formatted: { subtotal, shipping, address_shipping, grand_total } }
     */
    public function calculateTotalsAjax(Request $request)
    {
        // Basic validation
        $data = $request->all();
        $items = isset($data['items']) && is_array($data['items']) ? $data['items'] : [];

        // Start totals
        $subtotal = 0;
        $total_tax = 0;

        foreach ($items as $it) {
            $item_id = isset($it['item_id']) ? $it['item_id'] : null;
            $qty = isset($it['quantity']) ? (int) $it['quantity'] : 0;
            if (!$item_id || $qty <= 0) continue;
            $item = Item::find($item_id);
            if (!$item) continue;

            // base main_price
            $main_price = $item->discount_price ?: $item->previous_price;

            // attribute_price: if attribute option ids are provided compute sum of option.price
            $attribute_price = 0;
            if (isset($it['options_ids']) && is_array($it['options_ids'])) {
                foreach ($it['options_ids'] as $optId) {
                    $opt = \App\Models\AttributeOption::find($optId);
                    if ($opt) $attribute_price += floatval($opt->price);
                }
            }

            $subtotal += ($main_price + $attribute_price) * $qty;

            if ($item->tax) {
                $total_tax += $item::taxCalculate($item) * $qty;
            }
        }

        // shipping method
        $methodPrice = 0;
        if ($request->has('shipping_id') && $request->shipping_id) {
            $svc = ShippingService::find($request->shipping_id);
            if ($svc) $methodPrice = $svc->price;
        }

        // address-based shipping
        $addressShipping = 0;
        if ($request->has('division_id') && $request->division_id && $request->has('district_id') && $request->district_id) {
            // reuse existing helper in controller
            $resp = $this->addressShippingSetUp($request);
            // addressShippingSetUp may return a response or array; it returns JSON in other callers
            if (is_array($resp) && isset($resp['price'])) {
                $addressShipping = floatval($resp['price']);
            } elseif ($resp instanceof \Illuminate\Http\JsonResponse) {
                $j = $resp->getData(true);
                $addressShipping = isset($j['price']) ? floatval($j['price']) : 0;
            }
        }

        // discount from session coupon
        $discount = 0;
        if (Session::has('coupon')) {
            $disc = Session::get('coupon');
            $discount = isset($disc['discount']) ? floatval($disc['discount']) : 0;
        }

        // state price (fixed or percent)
        $state_price = 0;
        $stateType = '';
        if (Auth::check() && Auth::user()->state_id) {
            $state = State::find(Auth::user()->state_id);
            if ($state) {
                $stateType = $state->type;
                $stateCfg = floatval($state->price);
                if ($stateType === 'fixed') {
                    $state_price = $stateCfg;
                } elseif ($stateType === 'percent') {
                    $state_price = ($subtotal * $stateCfg) / 100;
                }
            }
        }

        $grand = $subtotal + $total_tax + $methodPrice + $addressShipping + $state_price - $discount;

        // Return numeric and formatted values
        return response()->json([
            'subtotal' => round($subtotal, 2),
            'tax' => round($total_tax, 2),
            'shipping' => round($methodPrice, 2),
            'address_shipping' => round($addressShipping, 2),
            'state_price' => round($state_price, 2),
            'discount' => round($discount, 2),
            'grand_total' => round($grand, 2),
            'formatted' => [
                'subtotal' => PriceHelper::setCurrencyPrice($subtotal),
                'shipping' => PriceHelper::setCurrencyPrice($methodPrice),
                'address_shipping' => PriceHelper::setCurrencyPrice($addressShipping),
                'grand_total' => PriceHelper::setCurrencyPrice($grand),
            ]
        ]);
    }

    public function shippingStore(Request $request)
    {

        // laravel validation
        $request->validate([
            'ship_first_name' => 'required',
           // 'ship_last_name' => 'required',
            'ship_email' => 'nullable|email',
            'ship_phone' => 'required',
            'ship_address1' => 'required',
            //'ship_zip' => 'required',
            //'ship_city' => 'required',
        ]);

        Session::put('shipping_address', $request->all());
        return redirect(route('front.checkout.payment'));
    }



    public function payment()
    {
        if (!Session::has('billing_address')) {
            return redirect(route('front.checkout.billing'));
        }

        if (!Session::has('shipping_address')) {
            return redirect(route('front.checkout.shipping'));
        }


        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }
        $data['user'] = Auth::user();
        $cart = Session::get('cart');

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $items) {

            $total += ($items['main_price'] + $items['attribute_price']) * $items['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item) * $items['qty'];
            }
        }

        $shipping = [];

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        if (!PriceHelper::Digital()) {
            $shipping = null;
        }

        $grand_total = ($cart_total  + $total_tax);
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $state_tax = Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0;
        $grand_total = $grand_total + $state_tax;


        $total_amount = $grand_total;

        $data['cart'] = $cart;
        $data['cart_total'] = $cart_total;
        $data['grand_total'] = $total_amount;
        $data['discount'] = $discount;
        $data['shipping'] = $shipping;
        $data['tax'] = $total_tax;
        $data['payments'] = PaymentSetting::whereStatus(1)->get();
        return view('front.checkout.payment', $data);
    }

    public function checkout(Request $request)
    {
        // Log incoming request for debugging missing payload issues
        Log::info('Front Checkout called', [
            'input' => $request->all(),
            'cookies' => $request->cookies->all(),
            'session_id' => Session::getId(),
            'path' => $request->path(),
            'is_ajax' => $request->ajax(),
        ]);

        PriceHelper::checkCheckout($request);

        $input = $request->all();
        $payment = null;
        if(isset($input['payment_method']) && $input['payment_method'] == 'Cash On Delivery') {
            $payment = $this->cashOnDeliverySubmit($input);
            
            // Return JSON for AJAX requests, redirect for regular form submissions
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully!',
                    'redirect' => route('front.checkout.success')
                ]);
            }
            
            return redirect()->route('front.checkout.success');
        }

        // Return JSON error for AJAX requests, redirect for regular form submissions
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method not supported or missing.',
                'redirect' => route('front.checkout.cancle')
            ], 400);
        }

        return redirect()->route('front.checkout.cancle');
    }


    public function paymentRedirect(Request $request)
    {
        $responseData = $request->all();

        if (isset($responseData['session_id'])) {
            $payment = $this->stripeNotify($responseData);
            if ($payment['status']) {
                return redirect()->route('front.checkout.success');
            } else {
                Session::put('message', $payment['message']);
                return redirect()->route('front.checkout.cancle');
            }
        } elseif (Session::has('order_payment_id')) {
            $payment = $this->paypalNotify($responseData);
            if ($payment['status']) {
                return redirect()->route('front.checkout.success');
            } else {
                Session::put('message', $payment['message']);
                return redirect()->route('front.checkout.cancle');
            }
        } else {
            return redirect()->route('front.checkout.cancle');
        }
    }

    public function mollieRedirect(Request $request)
    {

        $responseData = $request->all();

        $payment = Mollie::api()->payments()->get(Session::get('payment_id'));
        $responseData['payment_id'] = $payment->id;
        if ($payment->status == 'paid') {
            $payment = $this->mollieNotify($responseData);
            if ($payment['status']) {
                return redirect()->route('front.checkout.success');
            } else {
                Session::put('message', $payment['message']);
                return redirect()->route('front.checkout.cancle');
            }
        } else {
            return redirect()->route('front.checkout.cancle');
        }
    }

    public function paymentSuccess()
    {
        if (Session::has('order_id')) {
            $order_id = Session::get('order_id');
            $order = Order::find($order_id);
            $cart = json_decode($order->cart, true);
            $setting = Setting::first();
            if ($setting->is_twilio == 1) {
                // message
                $sms = new SmsHelper();
                $user_number = $order->user->phone;
                if ($user_number) {
                    $sms->SendSms($user_number, "'purchase'");
                }
            }
            return view('front.checkout.success', compact('order', 'cart'));
        }
        return redirect()->route('front.index');
    }



    public function paymentCancle()
    {
        $message = '';
        if (Session::has('message')) {
            $message = Session::get('message');
            Session::forget('message');
        } else {
            $message = __('Payment Failed!');
        }
        Session::flash('error', $message);
        return redirect()->route('front.checkout.billing');
    }

    public function stateSetUp(Request $request)
    {
        $state_id = $request->state_id;
        $shipping_id = $request->shipping_id;


        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }

        $cart = Session::get('cart');
        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $items) {

            $total += ($items['main_price'] + $items['attribute_price']) * $items['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item) * $items['qty'];
            }
        }

        $shipping = [];
        if ($shipping_id) {
            $shipping = ShippingService::findOrFail($shipping_id);
        }
        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        $grand_total = ($cart_total + ($shipping ? $shipping->price : 0)) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);

        $state_price = 0;
        if ($state_id) {
            $state = State::findOrFail($state_id);
            if ($state->type == 'fixed') {
                $state_price = $state->price;
            } else {
                $state_price = ($cart_total * $state->price) / 100;
            }
        } else {
            if (Auth::check() && Auth::user()->state_id) {
                $state = Auth::user()->state;
                if ($state->type == 'fixed') {
                    $state_price = $state->price;
                } else {
                    $state_price = ($cart_total * $state->price) / 100;
                }
            } else {
                $state_price = 0;
            }
        }

        $total_amount = $grand_total + $state_price;

        $data['state_price'] = PriceHelper::setCurrencyPrice($state_price);
        $data['grand_total'] = PriceHelper::setCurrencyPrice($total_amount);

        return response()->json($data);
    }

    public function shippingSetUp(Request $request)
    {
        $state_id = $request->state_id;
        $shipping_id = $request->shipping_id;



        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }

        $cart = Session::get('cart');
        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $items) {

            $total += ($items['main_price'] + $items['attribute_price']) * $items['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item) * $items['qty'];
            }
        }

        $shipping = ShippingService::findOrFail($shipping_id);

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        $grand_total = ($cart_total + ($shipping ? $shipping->price : 0)) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);

        $state_price = 0;
        if ($state_id && $state_id != 'undefined') {
            $state = State::findOrFail($state_id);
            if ($state->type == 'fixed') {
                $state_price = $state->price;
            } else {
                $state_price = ($cart_total * $state->price) / 100;
            }
        } else {
            if (Auth::check() && Auth::user()->state_id) {
                $state = Auth::user()->state;
                if ($state->type == 'fixed') {
                    $state_price = $state->price;
                } else {
                    $state_price = ($cart_total * $state->price) / 100;
                }
            } else {
                $state_price = 0;
            }
        }

        $total_amount = $grand_total + $state_price;

        $data['state_price'] = PriceHelper::setCurrencyPrice($state_price);
        $data['shipping_price'] = PriceHelper::setCurrencyPrice($shipping->price);
        $data['grand_total'] = PriceHelper::setCurrencyPrice($total_amount);

        return response()->json($data);
    }

    // Frontend AJAX: get districts by division for checkout selects
    public function getDistricts(Request $request)
    {
        $divisionId = $request->get('division_id');
        if (!$divisionId) return response()->json([]);
        $rows = District::where('division_id', $divisionId)->orderBy('name')->get(['id','name']);
        return response()->json($rows);
    }

    // Frontend AJAX: get thanas by district for checkout selects
    public function getThanas(Request $request)
    {
        $districtId = $request->get('district_id');
        if (!$districtId) return response()->json([]);
        $rows = Thana::where('district_id', $districtId)->orderBy('name')->get(['id','name']);
        return response()->json($rows);
    }

    // Frontend AJAX: compute address-based shipping surcharge (not required)
    public function addressShippingSetUp(Request $request)
    {
        $divisionId = $request->get('division_id');
        $districtId = $request->get('district_id');
        $thanaId = $request->get('thana_id');

        // Price selection precedence (most specific -> least): thana, district, division
        // Special-case: if the district is Dhaka or Dhaka-metro, prefer the Dhaka/dhaka-metro price
        $price = 0.0;
        $useAddressPrice = false;

        // helper to fetch a matching row by specificity (thana -> district).
        // Note: division-level prices are intentionally ignored per requirements.
        $findPriceRow = function($divisionId, $districtId, $thanaId) {
            // try thana
            if ($thanaId) {
                $r = ShippingLocationPrice::where('status',1)->where('thana_id', $thanaId)->orderByDesc('id')->first();
                if ($r) return $r;
            }
            // try district
            if ($districtId) {
                $r = ShippingLocationPrice::where('status',1)->where('district_id', $districtId)->orderByDesc('id')->first();
                if ($r) return $r;
            }
            return null;
        };

        // If we have a district, check for Dhaka / Dhaka-metro special handling
        $districtName = '';
        $dn = '';
        if ($districtId) {
            $district = District::find($districtId);
            $districtName = $district->name ?? '';
            $dn = strtolower(trim(str_replace([' ', '_'], '-', $districtName)));
            if (in_array($dn, ['dhaka','dhaka-metro'])) {
                // For Dhaka/Dhaka-metro: only consider thana -> district (do NOT fallback to division)
                // try thana
                if ($thanaId) {
                    $r = ShippingLocationPrice::where('status',1)->where('thana_id', $thanaId)->orderByDesc('id')->first();
                    if ($r) {
                        $price = (float) $r->price;
                        $useAddressPrice = true;
                    }
                }
                // try district if no thana price found
                if (!$useAddressPrice && $districtId) {
                    $r = ShippingLocationPrice::where('status',1)->where('district_id', $districtId)->orderByDesc('id')->first();
                    if ($r) {
                        $price = (float) $r->price;
                        $useAddressPrice = true;
                    }
                }

                return response()->json([
                    'price' => round($price, 2),
                    'formatted' => PriceHelper::setCurrencyPrice($price),
                    'use_address_price' => $useAddressPrice,
                ]);
            }
        }

        // General case: prefer thana -> district -> division
        // Only consider thana/district rows (division-level ignored). If none found:
        // - if district supplied and not Dhaka/Dhaka-metro => default to 150
        // - otherwise no address price
        $row = $findPriceRow($divisionId, $districtId, $thanaId);
        if ($row) {
            $price = (float) $row->price;
            $useAddressPrice = true;
        } else {
            if ($districtId && !in_array($dn, ['dhaka','dhaka-metro'])) {
                // non-Dhaka district with no specific price -> default 150
                $price = 150.00;
                $useAddressPrice = true;
            } else {
                // no district provided or Dhaka without a specific row -> no address price
                $price = 0.0;
                $useAddressPrice = false;
            }
        }

        return response()->json([
            'price' => round($price, 2),
            'formatted' => PriceHelper::setCurrencyPrice($price),
            'use_address_price' => $useAddressPrice,
        ]);
    }

    // Pathao API Integration
    public function getPathaoCities(Request $request)
    {
        $pathaoService = new PathaoService();
        $response = $pathaoService->getCities();
        
        if (isset($response['data']['data'])) {
            return response()->json($response['data']['data']);
        }
        
        return response()->json([]);
    }

    public function getPathaoZones(Request $request)
    {
        $cityId = $request->get('city_id');
        if (!$cityId) {
            return response()->json([]);
        }

        $pathaoService = new PathaoService();
        $response = $pathaoService->getZones($cityId);
        
        if (isset($response['data']['data'])) {
            return response()->json($response['data']['data']);
        }
        
        return response()->json([]);
    }

    public function getPathaoAreas(Request $request)
    {
        $zoneId = $request->get('zone_id');
        if (!$zoneId) {
            return response()->json([]);
        }

        $pathaoService = new PathaoService();
        $response = $pathaoService->getAreas($zoneId);
        
        if (isset($response['data']['data'])) {
            return response()->json($response['data']['data']);
        }
        
        return response()->json([]);
    }

    public function getPathaoPrice(Request $request)
    {
        $cityId = $request->get('city_id');
        $zoneId = $request->get('zone_id');
        $itemWeight = $request->get('item_weight', 0.5);
        $districtId = $request->get('district_id'); // Optional district for accurate markup
        
        if (!$cityId || !$zoneId) {
            return response()->json(['error' => 'City and Zone are required']);
        }

        $pathaoService = new PathaoService();
        $response = $pathaoService->calculatePrice($cityId, $zoneId, $itemWeight);
        
        if (isset($response['data'])) {
            // Handle different response structures
            $basePrice = 0;
            if (isset($response['data']['price'])) {
                $basePrice = $response['data']['price'];
            } elseif (isset($response['data']['total_price'])) {
                $basePrice = $response['data']['total_price'];
            } elseif (isset($response['price'])) {
                $basePrice = $response['price'];
            }
            
            // Apply markup based on district selection
            $finalPrice = $basePrice;
            $markup = 0;
            $isDhaka = false;
            
            // Method 1: Check by district_id if provided
            if ($districtId) {
                $district = District::find($districtId);
                if ($district) {
                    $districtName = strtolower(trim(str_replace([' ', '_'], '-', $district->name)));
                    $isDhaka = in_array($districtName, ['dhaka', 'dhaka-metro', 'dhaka-city']);
                }
            }
            
            // Method 2: Fallback to city name if no district provided
            if (!$districtId) {
                try {
                    $cityName = $pathaoService->getCityName($cityId);
                    $cityNameLower = strtolower(trim(str_replace([' ', '_'], '-', $cityName)));
                    $isDhaka = in_array($cityNameLower, ['dhaka', 'dhaka-metro', 'dhaka-city']);
                } catch (\Exception $e) {
                    // Default to non-Dhaka if can't determine
                    $isDhaka = false;
                }
            }
            
            // Apply markup
            if ($isDhaka) {
                // Dhaka/Dhaka-metro: add 20 markup
                $markup = 20;
                $finalPrice = $basePrice + $markup;
            } else {
                // Other districts: add 40 markup
                $markup = 40;
                $finalPrice = $basePrice + $markup;
            }
            
            return response()->json([
                'status' => 'success',
                'price' => $finalPrice,
                'base_price' => $basePrice,
                'markup' => $markup,
                'is_dhaka' => $isDhaka,
                'formatted' => '৳' . number_format($finalPrice, 2),
                'breakdown' => [
                    'base' => '৳' . number_format($basePrice, 2),
                    'markup' => '৳' . number_format($markup, 2),
                    'total' => '৳' . number_format($finalPrice, 2)
                ],
                'data' => $response['data'] ?? $response
            ]);
        }
        
        return response()->json([
            'status' => 'error',
            'error' => 'Unable to calculate price',
            'response' => $response
        ]);
    }
    
    public static function getPathaoNames($shipping_info)
    {
        if (!is_array($shipping_info)) {
            $shipping_info = json_decode($shipping_info, true) ?? [];
        }
        
        $names = [
            'city_name' => 'Unknown City',
            'zone_name' => 'Unknown Zone',
            'area_name' => 'Unknown Area'
        ];
        
        if (isset($shipping_info['pathao_city_id'], $shipping_info['pathao_zone_id'], $shipping_info['pathao_area_id'])) {
            try {
                $pathaoService = new PathaoService();
                $names['city_name'] = $pathaoService->getCityName($shipping_info['pathao_city_id']);
                $names['zone_name'] = $pathaoService->getZoneName($shipping_info['pathao_city_id'], $shipping_info['pathao_zone_id']);
                $names['area_name'] = $pathaoService->getAreaName($shipping_info['pathao_zone_id'], $shipping_info['pathao_area_id']);
            } catch (\Exception $e) {
                // Keep default values if API fails
            }
        }
        
        return $names;
    }
}
