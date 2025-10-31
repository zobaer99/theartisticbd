<?php

namespace App\Http\Controllers\Front;

use App\{
    Models\Item,
    Http\Controllers\Controller,
    Repositories\Front\CartRepository
};
use App\Helpers\PriceHelper;
use App\Models\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Constructor Method.
     *
     * @param  \App\Repositories\Front\CartRepository $repository
     *
     */
    public function __construct(CartRepository $repository)
    {
        $this->repository = $repository;
        $this->middleware('localize');
    }

    public function index()
    {
        if (Session::has('cart')) {
            $cart = Session::get('cart');
        } else {
            $cart = [];
        }
        return view('front.catalog.cart', [
            'cart' => $cart
        ]);
    }


    public function addToCart(Request $request)
    {

        $msg = $this->repository->store($request);


        if ($request->ajax()) {
            return $msg;
        }
    }

    public function store(Request $request)
    {

        $msg = $this->repository->store($request);
        if (isset($request->addtocart)) {
            Session::flash('success_message', __('Cart Added Successfully'));
            return back();
        }
        return redirect()->route('front.checkout.billing')->withSuccess($msg);
    }

    public function destroy($id)
    {
        $cart = Session::get('cart');
        
        // Check if cart exists and is an array
        if (!$cart || !is_array($cart)) {
            Session::flash('error', __('Cart is empty.'));
            return back();
        }
        
        // Remove the item if it exists
        if (isset($cart[$id])) {
            unset($cart[$id]);
        }
        
        // Update or clear cart
        if (count($cart) > 0) {
            Session::put('cart', $cart);
        } else {
            Session::forget('cart');
        }
        
        Session::flash('success', __('Cart item remove successfully.'));
        return back();
    }

    public function promoStore(Request $request)
    {
        return response()->json($this->repository->promoStore($request));
    }

    public function shippingStore(Request $request)
    {
        return redirect()->route('front.checkout');
    }


    public function update($id)
    {
        return view('front.catalog.cart_form', [
            'item' => Item::findOrFail($id),
            'attributes' => Item::findOrFail($id)->attributes,
            'cart_item' => Session::get('cart')[$id],
        ]);
    }


    public function shippingCharge(Request $request)
    {

        $charges = [];
        $items = [];
        foreach ($request->user_id as $data) {
            $check = explode('|', $data);
            $charges[] = $check[0];
            $items[] = $check[1];
        }
        $cart = Session::get('cart');
        $delivery_amount = 0;
        foreach ($charges as $index => $charge) {
            if ($charge != 0) {
                $vendor_charge = Item::findOrFail($items[$index])->user->shipping->price;
                $delivery_amount += $vendor_charge;
                $cart[$items[$index]]['delivery_charge'] = $vendor_charge;
            } else {
                $cart[$items[$index]]['delivery_charge'] = 0;
            }
        }

        Session::put('cart', $cart);

        return response()->json(['delivery' => PriceHelper::setPrice($delivery_amount), 'main' => $delivery_amount]);
    }


    public function headerCartLoad()
    {
        return view('includes.header_cart');
    }
    public function CartLoad()
    {
        return view('includes.cart');
    }

    public function cartClear()
    {
        Session::forget('cart');
        Session::flash('success', __('Cart clear successfully'));
        return back();
    }

    public function promoDelete()
    {
        Session::forget('coupon');
        Session::flash('success', __('Promo code remove successfully'));
        return back();
    }

    /**
     * Update cart item attribute
     */
    public function updateAttribute(Request $request)
    {
        try {
            $cartKey = $request->input('cart_key');
            $attributeIndex = $request->input('attribute_index');
            $newOption = $request->input('new_option');
            $productId = $request->input('product_id');

            // Get current cart
            $cart = Session::get('cart', []);
            
            if (!isset($cart[$cartKey])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found'
                ]);
            }

            $cartItem = $cart[$cartKey];
            
            // Try different possible keys for the product ID if not provided
            if (!$productId) {
                if (isset($cartItem['id'])) {
                    $productId = $cartItem['id'];
                } elseif (isset($cartItem['item_id'])) {
                    $productId = $cartItem['item_id'];
                } elseif (isset($cartItem['product_id'])) {
                    $productId = $cartItem['product_id'];
                }
            }
            
            $productSlug = null;
            if (!$productId && isset($cartItem['slug'])) {
                $productSlug = $cartItem['slug'];
            }

            // Get the product to find the new option details
            $item = null;
            if ($productId) {
                $item = Item::find($productId);
            } elseif ($productSlug) {
                $item = Item::where('slug', $productSlug)->first();
            }
            
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ]);
            }

            // Find the attribute and new option
            $attributes = $item->attributes;
            $targetAttribute = null;
            $targetOption = null;

            foreach ($attributes as $attribute) {
                if ($attribute->name === $cart[$cartKey]['attribute']['names'][$attributeIndex]) {
                    $targetAttribute = $attribute;
                    foreach ($attribute->options as $option) {
                        if ($option->name === $newOption) {
                            $targetOption = $option;
                            break 2;
                        }
                    }
                }
            }

            if (!$targetOption) {
                return response()->json([
                    'success' => false,
                    'message' => 'Option not found'
                ]);
            }

            // Update the cart item
            $cart[$cartKey]['attribute']['option_name'][$attributeIndex] = $targetOption->name;
            $cart[$cartKey]['attribute']['option_price'][$attributeIndex] = $targetOption->price;
            $cart[$cartKey]['options_id'][$attributeIndex] = $targetOption->id;

            // Recalculate attribute price
            $totalAttributePrice = 0;
            foreach ($cart[$cartKey]['attribute']['option_price'] as $price) {
                $totalAttributePrice += $price;
            }
            $cart[$cartKey]['attribute_price'] = $totalAttributePrice;

            // Update session
            Session::put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Attribute updated successfully',
                'new_price' => $cart[$cartKey]['main_price'] + $totalAttributePrice
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get available attribute options for a cart item
     */
    public function getAttributeOptions(Request $request)
    {
        try {
            $cartKey = $request->input('cart_key');
            $attributeName = $request->input('attribute_name');

            Log::info('Getting attribute options', [
                'cart_key' => $cartKey,
                'attribute_name' => $attributeName
            ]);

            // Get current cart
            $cart = Session::get('cart', []);
            
            if (!isset($cart[$cartKey])) {
                Log::error('Cart item not found', ['cart_key' => $cartKey, 'available_keys' => array_keys($cart)]);
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found'
                ]);
            }

            $cartItem = $cart[$cartKey];
            
            // Try different possible keys for the product ID
            $productId = null;
            $productSlug = null;
            
            if (isset($cartItem['id'])) {
                $productId = $cartItem['id'];
            } elseif (isset($cartItem['item_id'])) {
                $productId = $cartItem['item_id'];
            } elseif (isset($cartItem['product_id'])) {
                $productId = $cartItem['product_id'];
            }
            
            // If no ID found, try to get slug
            if (!$productId && isset($cartItem['slug'])) {
                $productSlug = $cartItem['slug'];
            }

            Log::info('Cart item structure', [
                'cart_item_keys' => array_keys($cartItem),
                'product_id' => $productId,
                'product_slug' => $productSlug
            ]);

            // Get the product by ID or slug
            $item = null;
            if ($productId) {
                $item = Item::with('attributes.options')->find($productId);
            } elseif ($productSlug) {
                $item = Item::with('attributes.options')->where('slug', $productSlug)->first();
            }

            if (!$item) {
                Log::error('Product not found', [
                    'product_id' => $productId,
                    'product_slug' => $productSlug,
                    'cart_item_keys' => array_keys($cartItem)
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ]);
            }

            Log::info('Product found', [
                'product_id' => $item->id,
                'product_name' => $item->name,
                'attributes_count' => $item->attributes->count()
            ]);

            // Find the attribute and its options
            $options = [];
            foreach ($item->attributes as $attribute) {
                Log::info('Checking attribute', [
                    'attribute_name' => $attribute->name,
                    'looking_for' => $attributeName,
                    'options_count' => $attribute->options->count()
                ]);
                
                if ($attribute->name === $attributeName) {
                    foreach ($attribute->options->where('stock', '!=', '0') as $option) {
                        $options[] = [
                            'id' => $option->id,
                            'name' => $option->name,
                            'price' => $option->price,
                            'stock' => $option->stock
                        ];
                    }
                    break;
                }
            }

            Log::info('Returning options', [
                'options_count' => count($options),
                'options' => $options
            ]);

            return response()->json([
                'success' => true,
                'options' => $options
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting attribute options', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
