<?php

namespace App\Traits;

use App\{
    Models\Order,
    Models\Setting,
    Models\TrackOrder,
    Helpers\EmailHelper,
    Helpers\PriceHelper,
    Models\Notification,
};
use App\Helpers\SmsHelper;
use App\Jobs\EmailSendJob;
use App\Models\Item;
use App\Models\PromoCode;
use App\Models\ShippingService;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

trait CashOnDeliveryCheckout
{

    public function cashOnDeliverySubmit($data)
    {
        

        $user = Auth::user();

        $setting = Setting::first();
        $cart = Session::get('cart');
        // Defensive: ensure cart exists
        if (empty($cart) || !is_array($cart)) {
            \Log::warning('CashOnDeliverySubmit called with empty cart', ['data' => $data]);
            return [
                'status' => false,
                'message' => 'Cart is empty'
            ];
        }
        $total_tax = 0;
        $cart_total = 0;
        $total = 0;
        $option_price = 0;

        foreach ($cart as $key => $items) {

            $total += $items['main_price'] * $items['qty'];
            $option_price += $items['attribute_price'];
            $cart_total = $total + $option_price;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item) * $items['qty'];
            }
        }
                // dd($cart);


        // Digital products don't need shipping. For physical products, shipping_id is optional
        // — if provided we'll load the ShippingService, otherwise leave shipping as null.
        if (PriceHelper::Digital()) {
            $shipping = null;
        } else {
            if (empty($data['shipping_id'])) {
                $shipping = null;
            } else {
                $shipping = ShippingService::findOrFail($data['shipping_id']);
            }
        }

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

         $billingInfo = [
        'bill_first_name' => $data['bill_first_name'] ?? $data['ship_full_name'] ?? null,
        'bill_last_name' => $data['bill_last_name'] ?? $data['ship_full_name'] ?? null,
        'bill_email' => $data['bill_email'] ?? $data['ship_email'] ?? null,
        'bill_phone' => $data['bill_phone'] ?? $data['ship_phone'] ?? null,
        'bill_company' => $data['bill_company'] ?? null,
        'bill_address1' => $data['bill_address1'] ?? $data['ship_address'] ?? null,
        'bill_address2' => $data['bill_address2'] ?? $data['ship_address'] ?? null,
        'bill_zip' => $data['bill_zip'] ?? $data['ship_zip'] ?? null,
        'bill_city' => $data['bill_city'] ?? $data['ship_city'] ?? null,
        'bill_country' => $data['bill_country'] ?? $data['ship_country'] ?? null
        ];

    $shippingInfo = [
    'ship_first_name' => $data['ship_full_name'] ?? $billingInfo['bill_first_name'] ?? null,
    'ship_last_name' => $data['ship_last_name'] ?? $billingInfo['bill_last_name'] ?? null,
    'ship_email' => $data['ship_email'] ?? $billingInfo['bill_email'] ?? null,
    'ship_phone' => $data['ship_phone'] ?? $billingInfo['bill_phone'] ?? null,
    'ship_company' => $data['ship_company'] ?? null,
    'ship_address1' => $data['ship_address'] ?? null,
    'ship_address2' => $data['ship_address2'] ?? null,
    'ship_zip' => $data['ship_zip'] ?? null,
    'ship_city' => $data['ship_city'] ?? null,
    'ship_country' => $data['ship_country'] ?? null,
    'address_shipping_cost' => $data['address_shipping_price'] ?? 0,
    'pathao_city_id' => $data['pathao_city_id'] ?? null,
    'pathao_zone_id' => $data['pathao_zone_id'] ?? null,
    'pathao_area_id' => $data['pathao_area_id'] ?? null,
    'pathao_shipping_cost' => $data['pathao_shipping_cost'] ?? null
    ];

        // Include address-based shipping cost if provided
        $address_shipping_cost = isset($data['address_shipping_price']) ? (float)$data['address_shipping_price'] : 0;
        
        // base grand total: cart subtotal + shipping method + address shipping + tax - discount
        $shipping_total = ($shipping ? $shipping->price : 0) + $address_shipping_cost;
        $grand_total = ($cart_total + $shipping_total) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $total_amount = PriceHelper::setConvertPrice($grand_total);
        $orderData['state'] =   null;
        $orderData['cart'] = json_encode($cart, true);
        $orderData['discount'] = json_encode($discount, true);
        $orderData['shipping'] = json_encode($shipping, true);
        $orderData['tax'] = $total_tax;
        $orderData['state_price'] = 0;
        $orderData['shipping_info'] =  json_encode($shippingInfo, true);
        $orderData['billing_info'] =  json_encode($billingInfo, true);
        $orderData['payment_method'] = 'Cash On Delivery';
        $orderData['user_id'] = isset($user) ? $user->id : 0;
        $orderData['transaction_number'] = Str::random(10);
        $orderData['currency_sign'] = PriceHelper::setCurrencySign();
        $orderData['currency_value'] = PriceHelper::setCurrencyValue();
        $orderData['payment_status'] = 'Unpaid';
        $orderData['order_status'] = 'Pending';
        
        $order = Order::create($orderData);

        $new_txn =  $new_txn = 'ORD-' . str_pad(Carbon::now()->format('Ymd'), 4, '0000', STR_PAD_LEFT) . '-' . $order->id;
        $order->transaction_number = $new_txn;
        $order->save();

        TrackOrder::create([
            'title' => 'Pending',
            'order_id' => $order->id,
        ]);

        // Note: Pathao orders are now created manually via admin bulk operations
        // Automatic Pathao order creation has been disabled for manual control

        PriceHelper::Transaction($order->id, $order->transaction_number, EmailHelper::getEmail(), PriceHelper::OrderTotal($order, 'trns'));
        PriceHelper::LicenseQtyDecrese($cart);
        PriceHelper::stockDecrese();
        Notification::create([
            'order_id' => $order->id
        ]);

        $emailData = [
            'to' => EmailHelper::getEmail(),
            'type' => "Order",
            'user_name' => isset($user) ? $user->name : Session::get('billing_address')['ship_full_name'],
            'order_cost' => $total_amount,
            'transaction_number' => $order->transaction_number,
            'site_title' => Setting::first()->title,
        ];

        $setting = Setting::first();
        if ($setting->is_queue_enabled == 1) {
            dispatch(new EmailSendJob($emailData, "template"));
        } else {
            $email = new EmailHelper();
            $email->sendTemplateMail($emailData, "template");
        }
        
        if ($discount) {
            $coupon_id = $discount['code']['id'];
            $get_coupon = PromoCode::findOrFail($coupon_id);
            $get_coupon->no_of_times -= 1;
            $get_coupon->update();
        }
        // if ($setting->is_twilio == 1) {
        //     // message
        //     $sms = new SmsHelper();
        //     $user_number = json_decode($order->billing_info, true)['bill_phone'];
        //     if ($user_number) {
        //         $sms->SendSms($user_number, "'purchase'", $order->transaction_number);
        //     }
        // }

        Session::put('order_id', $order->id);
        Session::forget('cart');
        Session::forget('discount');
        Session::forget('coupon');
        return [
            'status' => true
        ];
    }
}
