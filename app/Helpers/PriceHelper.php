<?php

namespace App\Helpers;

use App\Models\AttributeOption;
use App\Models\Currency;
use App\Models\Item;
use App\Models\PaymentSetting;
use App\Models\Setting;
use App\Models\State;
use App\Models\Transaction;
use Illuminate\Support\Facades\Session;

class PriceHelper
{

    public static function setPrice($price)
    {
        // Currency system commented out - using fixed Taka (৳) currency
        // $curr = cache()->remember('default_currency', 3600, function () {
        //     return Currency::where('is_default', 1)->first();
        // });
        return round($price * 1 , 2);
    }

    public static function adminCurrencyPrice($price)
    {
        // Currency system commented out - using fixed Taka (৳) currency        
        // $curr = cache()->remember('default_currency', 3600, function () {
        //     return Currency::where('is_default', 1)->first();
        // });
        $setting = cache()->remember('global_setting', 3600, function () {
            return Setting::first();
        });
        $price = self::testPrice($price * 1, 2);
        if ($setting->currency_direction == 1) {
            return '৳' . $price;
        } else {
            return $price . '৳';
        }
    }

    public static function adminCurrency()
    {
        // Currency system commented out - using fixed Taka (৳) currency
        // $curr = cache()->remember('default_currency', 3600, function () {
        //     return Currency::where('is_default', 1)->first();
        // });
        return '৳';
    }

    public static function storePrice($price)
    {
        // $curr = cache()->remember('default_currency', 3600, function () {
        //     return Currency::where('is_default', 1)->first();
        // });
        return round($price * 1, 2);
    }

    public static function setCurrencyPrice($price)
    {
        // Currency system commented out - using fixed Taka (৳) currency
        // if (Session::has('currency')) {
        //     $curr = cache()->remember('currency_' . Session::get('currency'), 3600, function () {
        //         return Currency::findOrFail(Session::get('currency'));
        //     });
        // } else {
        //     $curr = cache()->remember('default_currency', 3600, function () {
        //         return Currency::where('is_default', 1)->first();
        //     });
        // }

        $setting = cache()->remember('global_setting', 3600, function () {
            return Setting::first();
        });
        $price = self::testPrice(round($price * 1, 2));

        if ($setting->currency_direction == 1) {
            return '৳' . $price;
        } else {
            return $price . '৳';
        }
    }

    public static function setPreviousPrice($price)
    {

        // if (Session::has('currency')) {
        //     $curr = Currency::findOrFail(Session::get('currency'));
        // } else {
        //     $curr = Currency::where('is_default', 1)->first();
        // }
        // if ($price != 0) {
        //     $setting = Setting::first();
        //     $price = self::testPrice($price * $curr->value, 2);
        //     if ($setting->currency_direction == 1) {
        //         return $curr->sign . $price;
        //     } else {
        //         return $price . $curr->sign;
        //     }
        // } else {
        //     $price = '';
        // }

        return html_entity_decode($price);
    }

    public static function setConvertPrice($price)
    {
        // if (Session::has('currency')) {
        //     $curr = cache()->remember('currency_' . Session::get('currency'), 3600, function () {
        //         return Currency::findOrFail(Session::get('currency'));
        //     });
        // } else {
        //     $curr = cache()->remember('default_currency', 3600, function () {
        //         return Currency::where('is_default', 1)->first();
        //     });
        // }
        return round($price * 1, 2);
    }

    public static function convertPrice($price)
    {
        // Currency system commented out - using fixed Taka (৳) currency
        // if (Session::has('currency')) {
        //     $curr = cache()->remember('currency_' . Session::get('currency'), 3600, function () {
        //         return Currency::findOrFail(Session::get('currency'));
        //     });
        // } else {
        //     $curr = cache()->remember('default_currency', 3600, function () {
        //         return Currency::where('is_default', 1)->first();
        //     });
        // }
        return round($price / 1, 2);
    }

    public static function setCurrencySign()
    {
        // Currency system commented out - using fixed Taka (৳) currency
        // if (Session::has('currency')) {
        //     $curr = Currency::findOrFail(Session::get('currency'));
        // } else {
        //     $curr = Currency::where('is_default', 1)->first();
        // }
        return '৳';
    }

    public static function setCurrencyValue()
    {
        // Currency system commented out - using fixed Taka (৳) currency
        // if (Session::has('currency')) {
        //     $curr = Currency::findOrFail(Session::get('currency'));
        // } else {
        //     $curr = Currency::where('is_default', 1)->first();
        // }
        return 1;
    }

    public static function setCurrencyName()
    {
        // Currency system commented out - using fixed Taka (৳) currency
        // if (Session::has('currency')) {
        //     $curr = Currency::findOrFail(Session::get('currency'));
        // } else {
        //     $curr = Currency::where('is_default', 1)->first();
        // }
        return 'BDT';
    }

    public static function grandCurrencyPrice($item)
    {
        $option_price = 0;
        if (count($item->attributes) > 0) {
            foreach ($item->attributes as $attr) {
                if (isset($attr->options[0])) {
                    $option_price += $attr->options[0]->price;
                }
            }
        }

        // if (Session::has('currency')) {
        //     $curr = Currency::findOrFail(Session::get('currency'));
        // } else {
        //     $curr = Currency::where('is_default', 1)->first();
        // }
        $price = $item->discount_price + $option_price;

        //$setting = Setting::first();

        //$price = self::testPrice(round($price * $curr->value, 2));

        // if ($setting->currency_direction == 1) {
        //     return $curr->sign . $price;
        // } else {
        //     return $price . $curr->sign;
        // }

         return $price . '৳';

    }

    public static function grandPrice($item)
    {
        $option_price = 0;
        if (count($item->attributes) > 0) {
            foreach ($item->attributes as $attr) {
                if (isset($attr->options[0])) {
                    $option_price += PriceHelper::convertPrice($attr->options[0]->price);
                }

            }

        }

        // if (Session::has('currency')) {
        //     $curr = Currency::findOrFail(Session::get('currency'));
        // } else {
        //     $curr = Currency::where('is_default', 1)->first();
        // }
        $price = ($item->discount_price + $option_price);

        return $price;

    }

    public static function Discount($discount)
    {
        if ($discount) {
            $discount = json_decode($discount, true);
        } else {
            $discount = 0;
        }
        return $discount;
    }

    public static function OrderTotal($order, $trns = null)
    {
        $cart = json_decode($order->cart, true);

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $items) {
            // Handle different cart structures for backward compatibility
            $main_price = isset($items['main_price']) ? $items['main_price'] : (isset($items['price']) ? $items['price'] : 0);
            $attribute_price = isset($items['attribute_price']) ? $items['attribute_price'] : 0;
            $qty = isset($items['qty']) ? $items['qty'] : 1;
            
            $total += ($main_price + $attribute_price) * $qty;
            $cart_total = $total;
            if (Item::where('id', $key)->exists()) {
                $item = Item::findOrFail($key);
                if (isset($item)) {

                    if ($item && $item->tax) {
                        $total_tax += $item::taxCalculate($item) * $items['qty'];
                    }
                }
            }
        }

        $shipping = [];
        $address_shipping_cost = 0;
        if (json_decode($order->shipping)) {
            $shipping = json_decode($order->shipping, true);
        }
        
        // Get address shipping cost or Pathao shipping cost from shipping_info
        if (json_decode($order->shipping_info)) {
            $shipping_info = json_decode($order->shipping_info, true);
            // Priority: Pathao shipping cost > address shipping cost
            if (isset($shipping_info['pathao_shipping_cost'])) {
                $address_shipping_cost = (float)$shipping_info['pathao_shipping_cost'];
            } else {
                $address_shipping_cost = isset($shipping_info['address_shipping_cost']) ? (float)$shipping_info['address_shipping_cost'] : 0;
            }
        }

        $discount = [];
        if (json_decode($order->discount)) {
            $discount = json_decode($order->discount, true);
        }

        // Include both shipping method price and address-based shipping cost
        $shipping_total = ($shipping && isset($shipping['price']) ? $shipping['price'] : 0) + $address_shipping_cost;
        $grand_total = ($cart_total + $shipping_total) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $grand_total = $grand_total + $order->state_price;

        // Currency system commented out - using fixed Taka (৳) currency value = 1
        $total_amount = round($grand_total * 1, 2); // Changed from $order->currency_value to 1
        if (!$trns) {
            $total_amount = self::testPrice($total_amount);
        }

        return $total_amount;
    }
    public static function OrderTotalChart($order)
    {
        $cart = json_decode($order->cart, true);

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;
        $option_price = 0;

        foreach ($cart as $key => $items) {
            // Handle different cart structures for backward compatibility
            $main_price = isset($items['main_price']) ? $items['main_price'] : (isset($items['price']) ? $items['price'] : 0);
            $attribute_price = isset($items['attribute_price']) ? $items['attribute_price'] : 0;
            $qty = isset($items['qty']) ? $items['qty'] : 1;
            
            $total += $main_price * $qty;
            $option_price += $attribute_price;
            $cart_total = $total + $option_price;
            if (Item::where('id', $key)->exists()) {
                $item = Item::findOrFail($key);
                if (isset($item)) {
                    if ($item && $item->tax) {
                        $total_tax += $item::taxCalculate($item) * $items['qty'];
                    }
                }
            }
        }

        $shipping = [];
        $address_shipping_cost = 0;
        if (json_decode($order->shipping)) {
            $shipping = json_decode($order->shipping, true);
        }
        
        // Get address shipping cost or Pathao shipping cost from shipping_info
        if (json_decode($order->shipping_info)) {
            $shipping_info = json_decode($order->shipping_info, true);
            // Priority: Pathao shipping cost > address shipping cost
            if (isset($shipping_info['pathao_shipping_cost'])) {
                $address_shipping_cost = (float)$shipping_info['pathao_shipping_cost'];
            } else {
                $address_shipping_cost = isset($shipping_info['address_shipping_cost']) ? (float)$shipping_info['address_shipping_cost'] : 0;
            }
        }
        
        $discount = [];
        if (json_decode($order->discount)) {
            $discount = json_decode($order->discount, true);
        }

        // Include both shipping method price and address-based shipping cost
        $shipping_total = ($shipping && isset($shipping['price']) ? $shipping['price'] : 0) + $address_shipping_cost;
        $grand_total = ($cart_total + $shipping_total) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        // $curr = Currency::where('is_default', 1)->first();
        $total_amount = round($grand_total * 1, 2);

        return $total_amount;
    }

    public static function cartTotal($cartt, $trns = null)
    {
        $total = 0;
    
        foreach ($cartt as $key => $cart) {
            // Handle different cart structures for backward compatibility
            $main_price = isset($cart['main_price']) ? $cart['main_price'] : (isset($cart['price']) ? $cart['price'] : 0);
            $attribute_price = isset($cart['attribute_price']) ? $cart['attribute_price'] : 0;
            $qty = isset($cart['qty']) ? $cart['qty'] : 1;
            
            $itemTotal = ($main_price + $attribute_price) * $qty;
            $total += $itemTotal;
        }
    
        // Currency system commented out - using fixed Taka (৳) currency
        // if (Session::has('currency')) {
        //     $curr = Currency::findOrFail(Session::get('currency'));
        // } else {
        //     $curr = Currency::where('is_default', 1)->first();
        // }
    
        if ($trns) {
            if ($trns == 2) {
                return $total;
            }
            return round($total / 1, 2);
        }

        $price = self::testPrice($total / 1);
        return $price;
    }

    public static function CheckDigital()
    {
        $cart = Session::get('cart');
        $check_digital = false;
        
        if ($cart && is_array($cart)) {
            foreach ($cart as $key => $item) {
                if ($item['item_type'] == 'normal') {
                    $check_digital = true;
                }
            }
        }

        return $check_digital;
    }

    public static function CheckDigitalPaymentGateway()
    {
        $cart = Session::get('cart');
        $check_digital = true;
        
        if ($cart && is_array($cart)) {
            foreach ($cart as $key => $item) {
                if ($item['item_type'] == 'normal') {
                    $check_digital = false;
                }
            }
        }
        
        return $check_digital;
    }

    public static function Transaction($order_id, $txn_id, $user_email, $amount)
    {

        // if (Session::has('currency')) {
        //     $curr = Currency::findOrFail(Session::get('currency'));
        // } else {
        //     $curr = Currency::where('is_default', 1)->first();
        // }
        $transaction = new Transaction();
        $transaction->order_id = $order_id;
        $transaction->txn_id = $txn_id;
        $transaction->user_email = $user_email;
        $transaction->amount = $amount / 1;
        $transaction->currency_sign = '৳';
        $transaction->currency_value = 1;
        $transaction->save();

    }

    public static function GatewayText($keyword)
    {
        return PaymentSetting::where('unique_keyword', $keyword)->first()->text;
    }

    public static function DiscountPercentage($item)
    {
        if ($item->previous_price && $item->previous_price != 0) {
            $discount_price = $item->previous_price - $item->discount_price;
            $percentage = round($discount_price / $item->previous_price * 100);
            return $percentage . '%';
        }
    }

    public static function GetItemId($cart_id)
    {
        $item_id = explode('-', $cart_id);
        return $item_id[0];
    }

    public static function LicenseQtyDecrese($cart)
    {
        foreach ($cart as $item_id => $item) {
            if ($item['item_type'] == 'license') {
                $item = Item::findOrFail(PriceHelper::GetItemId($item_id));
                $license_key_new = json_decode($item->license_key, true);
                $last_key = array_key_last($license_key_new);
                unset($license_key_new[$last_key]);
                $license_name_new = json_decode($item->license_key, true);
                unset($license_name_new[$last_key]);
                $item->license_name = json_encode($license_name_new, true);
                $item->license_key = json_encode($license_key_new, true);
                $item->update();
            }

        }
    }

    public static function stockDecrese()
    {
        $cart = Session::get('cart');
        
        if ($cart && is_array($cart)) {
            foreach ($cart as $key => $item) {
                $main_item = Item::findOrFail($key);
                if ($main_item->item_type == 'normal') {
                    $current = $main_item->stock - $item['qty'];
                    if ($current <= 0) {
                        $main_item->stock = 0;
                    } else {
                        $main_item->stock = $current;
                    }
                    $main_item->update();
                    foreach ($item['options_id'] as $id) {
                        $option = AttributeOption::findOrFail($id);

                        if ($option->stock != 'unlimited') {
                            $new_stock = (int) $option->stock - $item['qty'];

                            if ($new_stock <= 0) {
                                $option->stock = '0';
                            } else {
                                $option->stock = (string) $new_stock;
                            }
                            $option->save();
                        }
                    }
                }
            }
        }
    }

    public static function testPrice($price)
    {

       // $setting = Setting::first();
 return number_format($price);
        // if ($setting->is_decimal == 1) {
        //     if (is_numeric($price) || floor($price) != $price) {
        //         return number_format($price, 2, 2, $setting->thousand_separator);
        //     } else {
        //         return number_format($price, 2, 2, $setting->thousand_separator);
        //     }
        // } else {

        //     return number_format($price);
        // }
    }

    public static function Digital()
    {
        $cart = Session::get('cart');
        $return = false;
        
        if ($cart && is_array($cart)) {
            foreach ($cart as $item) {
                if ($item['type'] == 'normal') {
                    $return = true;
                }
            }
        }
        
        return $return;
    }

    public static function StatePrce($state_id, $grand_total)
    {
        $state_price = 0;
        if ($state_id) {
            $state = State::findOrFail($state_id);
            if ($state->type == 'fixed') {
                $state_price = $state->price;
            } else {
                $state_price = ($grand_total * $state->price) / 100;
            }
        }

        return $state_price;
    }

    public static function checkCheckout($request)
    {
        $setting = cache()->remember('global_setting', 3600, function () {
            return Setting::first();
        });
        if ($setting->is_single_checkout == 0) {
            return true;
        }

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
    }

}
