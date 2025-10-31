<?php

namespace App\Http\Controllers\Back;

use App\{
    Models\Order,
    Models\PromoCode,
    Models\TrackOrder,
    Models\User,
    Models\Item,
    Http\Controllers\Controller
};
use App\Helpers\SmsHelper;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\PathaoService;

class OrderController extends Controller
{

    /**
     * Constructor Method.
     *
     * Setting Authentication
     *
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Order::latest('id');
        
        // Apply filters based on request parameters
        if ($request->type) {
            $query->whereOrderStatus($request->type);
        }
        
        // Date range filter
        if ($request->start_date && $request->end_date) {
            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
            $query->whereDate('created_at', '>=', $start_date)
                  ->whereDate('created_at', '<=', $end_date);
        }
        
        // Order ID filter
        if ($request->order_id) {
            $query->where('transaction_number', 'LIKE', '%' . $request->order_id . '%');
        }
        
        // Transaction ID filter (search in transactions table)
        if ($request->transaction_id) {
            $query->whereHas('tranaction', function($q) use ($request) {
                $q->where('txn_id', 'LIKE', '%' . $request->transaction_id . '%')
                  ->orWhere('id', 'LIKE', '%' . $request->transaction_id . '%');
            });
        }
        
        // Payment Status filter
        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }
        
        // Payment Method filter
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }
        
        // User Name filter (search in both first_name and last_name)
        if ($request->user_name) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where(function($subQuery) use ($request) {
                    $subQuery->where('first_name', 'LIKE', '%' . $request->user_name . '%')
                             ->orWhere('last_name', 'LIKE', '%' . $request->user_name . '%')
                             ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $request->user_name . '%']);
                });
            });
        }
        
        // User Phone filter
        if ($request->user_phone) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('phone', 'LIKE', '%' . $request->user_phone . '%');
            });
        }
        
        $datas = $query->with(['user', 'tranaction'])->get();
        
        return view('back.order.index', compact('datas'));
    }

    
    /**
     * Show the form for creating a new order.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get all users (remove is_vendor filter since column doesn't exist)
        $users = User::orderBy('first_name')->get();
        $items = Item::where('status', 1)->orderBy('name')->get();
        
        return view('back.order.create', compact('users', 'items'));
    }

    /**
     * Store a newly created order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:items,id',
            'products.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'payment_status' => 'required|in:Paid,Unpaid',
            'order_status' => 'required|in:Pending,In Progress,Delivered,Canceled',
            'ship_first_name' => 'required|string|max:255',
            'ship_phone' => 'required|string|max:255',
            'ship_address1' => 'required|string|max:255',
            'ship_city' => 'required|string|max:255',
            'pathao_city_id' => 'nullable|integer',
            'pathao_zone_id' => 'nullable|integer',
            'pathao_area_id' => 'nullable|integer',
            'pathao_shipping_cost' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
        ]);

        // Generate unique transaction number
        $transaction_number = 'ORD-' . date('YmdHis') . '-' . rand(1000, 9999);
        while (Order::where('transaction_number', $transaction_number)->exists()) {
            $transaction_number = 'ORD-' . date('YmdHis') . '-' . rand(1000, 9999);
        }

        // Prepare cart data
        $cart = [];
        $subtotal = 0;

        foreach ($request->products as $productData) {
            $item = Item::with('attributes.options')->find($productData['id']);
            if (!$item) { continue; }

            $qty = (int) ($productData['qty'] ?? 1);
            $basePrice = $item->discount_price; // base main price
            $attributePrice = isset($productData['attribute_price']) ? (float)$productData['attribute_price'] : 0;
            $unitPrice = $basePrice + $attributePrice;
            $lineTotal = $unitPrice * $qty;
            $subtotal += $lineTotal;

            // Build attribute structure from submitted attributes array (products[index][attributes][attribute_name] => option)
            $attributeStruct = [];
            if (isset($productData['attributes']) && is_array($productData['attributes'])) {
                $names = []; $options = [];
                foreach ($productData['attributes'] as $attrName => $attrValue) {
                    if ($attrValue !== null && $attrValue !== '') {
                        $names[] = ucfirst($attrName);
                        $options[] = $attrValue;
                    }
                }
                if (count($names)) {
                    $attributeStruct = [
                        'names' => $names,
                        'option_name' => $options,
                    ];
                }
            }

            $cart[] = [
                'name' => $item->name,
                'slug' => $item->slug,
                'qty' => $qty,
                'price' => $basePrice, // original base price
                'main_price' => $basePrice,
                'attribute_price' => $attributePrice,
                'photo' => $item->photo,
                'type' => $item->item_type,
                'item_type' => $item->item_type,
                'attribute' => $attributeStruct,
            ];
        }

        // Calculate shipping (removed tax)
        $shipping_cost = (float) ($request->pathao_shipping_cost ?? $request->shipping_cost ?? 0);

        // Prepare shipping information
        $shipping_info = [
            'ship_first_name' => $request->ship_first_name,
            'ship_email' => $request->ship_email,
            'ship_phone' => $request->ship_phone,
            'ship_address1' => $request->ship_address1,
            'ship_address2' => $request->ship_address2,
            'ship_city' => $request->ship_city,
            'ship_zip' => $request->ship_zip,
            'ship_country' => $request->ship_country ?? 'Bangladesh',
            'ship_company' => $request->ship_company,
        ];
        
        // Add Pathao information if provided
        if ($request->pathao_city_id) {
            $shipping_info['pathao_city_id'] = $request->pathao_city_id;
        }
        if ($request->pathao_zone_id) {
            $shipping_info['pathao_zone_id'] = $request->pathao_zone_id;
        }
        if ($request->pathao_area_id) {
            $shipping_info['pathao_area_id'] = $request->pathao_area_id;
        }
        if ($request->pathao_shipping_cost) {
            $shipping_info['pathao_shipping_cost'] = $request->pathao_shipping_cost;
        }

        // Prepare billing information (same as shipping for now)
        $billing_info = $shipping_info;

        // Create order (removed user_info since column doesn't exist)
        $order = Order::create([
            'user_id' => $request->user_id,
            'cart' => json_encode($cart),
            'shipping' => json_encode(['price' => $shipping_cost]), // Changed 'cost' to 'price' for PriceHelper compatibility
            'discount' => null,
            'payment_method' => $request->payment_method,
            'txnid' => $request->txnid,
            'charge_id' => null,
            'transaction_number' => $transaction_number,
            'order_status' => $request->order_status,
            'payment_status' => $request->payment_status,
            'shipping_info' => json_encode($shipping_info),
            'billing_info' => json_encode($billing_info),
            'currency_sign' => '৳',
            'currency_value' => 1,
            'tax' => 0,
            'state_price' => null,
            'state' => null,
        ]);

        // Handle order status tracking
        $this->setTrackOrder($order);

        // Send SMS notification
        $sms = new SmsHelper();
        $user_number = $user->phone ?? null;
        if ($user_number) {
            $sms->SendSms($user_number, "order_status", $order->transaction_number);
        }

        return redirect()->route('back.order.index')->withSuccess(__('Order Created Successfully.'));
    }

    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        $cart = json_decode($order->cart, true);
        
        return view('back.order.show', compact('order', 'cart'));
    }


    public function edit($id)
    {
        $order = Order::findOrFail($id);
        
        // Load all items for product changing
        $items = \App\Models\Item::with('attributes','attributes.options')->where('status', 1)->get();
        $pathaoService = new PathaoService();
        $pathaoResponse = $pathaoService->getCities();
        $pathao_cities = isset($pathaoResponse['data']['data']) ? $pathaoResponse['data']['data'] : [];

        return view('back.order.edit', compact('order', 'items', 'pathao_cities'));
    }

    

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            // 'transaction_number' => 'required|string|max:255',
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|in:Paid,Unpaid',
            'order_status' => 'nullable|in:Pending,In Progress,Delivered,Canceled',
            'shipping_id' => 'nullable|exists:shipping_services,id',
            'ship_first_name' => 'nullable|string|max:255',
            'ship_email' => 'nullable|email|max:255',
            'ship_phone' => 'nullable|string|max:255',
            'ship_address1' => 'required|string|max:500',
            'ship_address2' => 'nullable|string|max:255',
            'ship_city' => 'nullable|string|max:255',
            'ship_zip' => 'nullable|string|max:255',
            'ship_country' => 'nullable|string|max:255',
            'ship_company' => 'nullable|string|max:255',
            'pathao_city_id' => 'nullable|integer',
            'pathao_zone_id' => 'nullable|integer',
            'pathao_area_id' => 'nullable|integer',
            'pathao_shipping_cost' => 'nullable|numeric|min:0',
            'bill_first_name' => 'nullable|string|max:255',
            'bill_last_name' => 'nullable|string|max:255',
            'bill_email' => 'nullable|email|max:255',
            'bill_phone' => 'nullable|string|max:255',
            'bill_address1' => 'nullable|string|max:255',
            'bill_address2' => 'nullable|string|max:255',
            'bill_city' => 'nullable|string|max:255',
            'bill_zip' => 'nullable|string|max:255',
            'bill_country' => 'nullable|string|max:255',
            'bill_company' => 'nullable|string|max:255',
            'txnid' => 'nullable|string|max:255',
            'products' => 'nullable|array',
            'products.*.qty' => 'nullable|integer|min:1',
            'products.*.name' => 'nullable|string',
            'products.*.main_price' => 'nullable|numeric|min:0',
            'products.*.attribute_price' => 'nullable|numeric|min:0',
            'products.*.attributes' => 'nullable|array',
            'products.*.attributes.*' => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);
        
        // Check if order_id is available
        if (Order::where('transaction_number', $request->transaction_number)->where('id', '!=', $id)->exists()) {
            return redirect()->route('back.order.index')->withErrors(__('Order ID already exists.'));
        }

        // Prepare shipping information
        $shipping_info = [
            'ship_first_name' => $request->ship_first_name,
            'ship_email' => $request->ship_email,
            'ship_phone' => $request->ship_phone,
            'ship_address1' => $request->ship_address1,
            'ship_address2' => $request->ship_address2,
            'ship_city' => $request->ship_city,
            'ship_zip' => $request->ship_zip,
            'ship_country' => $request->ship_country,
            'ship_company' => $request->ship_company,
        ];
        
        // Add Pathao information if provided
        if ($request->pathao_city_id) {
            $shipping_info['pathao_city_id'] = $request->pathao_city_id;
        }
        if ($request->pathao_zone_id) {
            $shipping_info['pathao_zone_id'] = $request->pathao_zone_id;
        }
        if ($request->pathao_area_id) {
            $shipping_info['pathao_area_id'] = $request->pathao_area_id;
        }
        if ($request->pathao_shipping_cost) {
            $shipping_info['pathao_shipping_cost'] = $request->pathao_shipping_cost;
        }

        // Prepare billing information
        $billing_info = [
            'bill_first_name' => $request->bill_first_name,
            'bill_last_name' => $request->bill_last_name,
            'bill_email' => $request->bill_email,
            'bill_phone' => $request->bill_phone,
            'bill_address1' => $request->bill_address1,
            'bill_address2' => $request->bill_address2,
            'bill_city' => $request->bill_city,
            'bill_zip' => $request->bill_zip,
            'bill_country' => $request->bill_country,
            'bill_company' => $request->bill_company,
        ];

        // Handle products/cart update if provided
        if ($request->has('products') && is_array($request->products)) {
            $currentCart = json_decode($order->cart, true) ?? [];
            $updatedCart = [];

            foreach ($request->products as $index => $productData) {
                $submittedId = $productData['id'] ?? null;
                // If existing row
                if (isset($currentCart[$index])) {
                    $cartItem = $currentCart[$index];

                    // If product id supplied and different, replace base product data
                    if ($submittedId) {
                        $existingId = null;
                        if (isset($cartItem['slug'])) {
                            $existingId = Item::where('slug', $cartItem['slug'])->value('id');
                        }
                        if (!$existingId && isset($cartItem['name'])) {
                            $existingId = Item::where('name', $cartItem['name'])->value('id');
                        }
                        if ($existingId !== (int)$submittedId) {
                            $newItem = Item::find($submittedId);
                            if ($newItem) {
                                $cartItem['name'] = $newItem->name;
                                $cartItem['slug'] = $newItem->slug;
                                $cartItem['price'] = $newItem->discount_price;
                                $cartItem['main_price'] = $newItem->discount_price;
                                $cartItem['photo'] = $newItem->photo;
                                $cartItem['type'] = $newItem->item_type;
                                $cartItem['item_type'] = $newItem->item_type;
                                // Reset attributes when product changes
                                unset($cartItem['attribute']);
                                $cartItem['attribute_price'] = 0;
                            }
                        }
                    }

                    if (isset($productData['qty']) && $productData['qty'] > 0) {
                        $cartItem['qty'] = (int)$productData['qty'];
                    }
                    if (isset($productData['attribute_price'])) {
                        $cartItem['attribute_price'] = (float)$productData['attribute_price'];
                    }
                    if (isset($productData['attributes']) && is_array($productData['attributes'])) {
                        $attrNames = [];
                        $attrOptions = [];
                        foreach ($productData['attributes'] as $attrType => $attrValue) {
                            if ($attrValue !== null && $attrValue !== '') {
                                $attrNames[] = ucfirst($attrType);
                                $attrOptions[] = $attrValue;
                            }
                        }
                        if (count($attrNames)) {
                            $cartItem['attribute'] = [
                                'names' => $attrNames,
                                'option_name' => $attrOptions
                            ];
                        } else {
                            unset($cartItem['attribute']);
                            $cartItem['attribute_price'] = 0;
                        }
                    }
                    $updatedCart[] = $cartItem;
                    continue;
                }

                // New product addition (row index beyond current cart)
                if ($submittedId) {
                    $newItem = Item::find($submittedId);
                    if ($newItem) {
                        $qty = isset($productData['qty']) ? max(1, (int)$productData['qty']) : 1;
                        $attrPrice = isset($productData['attribute_price']) ? (float)$productData['attribute_price'] : 0;
                        $attrNames = [];
                        $attrOptions = [];
                        if (isset($productData['attributes']) && is_array($productData['attributes'])) {
                            foreach ($productData['attributes'] as $attrType => $attrValue) {
                                if ($attrValue !== null && $attrValue !== '') {
                                    $attrNames[] = ucfirst($attrType);
                                    $attrOptions[] = $attrValue;
                                }
                            }
                        }
                        $attributeStruct = [];
                        if (count($attrNames)) {
                            $attributeStruct = [
                                'names' => $attrNames,
                                'option_name' => $attrOptions,
                            ];
                        }
                        $updatedCart[] = [
                            'options_id' => [],
                            'attribute' => $attributeStruct,
                            'attribute_price' => $attrPrice,
                            'name' => $newItem->name,
                            'slug' => $newItem->slug,
                            'qty' => $qty,
                            'price' => $newItem->discount_price,
                            'main_price' => $newItem->discount_price,
                            'photo' => $newItem->photo,
                            'type' => $newItem->item_type,
                            'item_type' => $newItem->item_type,
                        ];
                    }
                }
            }

            $order->update(['cart' => json_encode($updatedCart)]);
        }

        // Handle shipping service update
        $shipping_service = null;
        $shipping_cost = 0;
        
        if ($request->shipping_id) {
            $shipping_service = \App\Models\ShippingService::findOrFail($request->shipping_id);
            $shipping_cost = $shipping_service->price;
        }
        
        // Add Pathao shipping cost if provided
        if ($request->pathao_shipping_cost) {
            $shipping_cost += (float) $request->pathao_shipping_cost;
        }

        // Update order with all fields
        $order->update([
            'transaction_number' => $request->transaction_number,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'order_status' => $request->order_status,
            'txnid' => $request->txnid,
            'shipping_info' => json_encode($shipping_info),
            'billing_info' => json_encode($billing_info),
            'shipping' => $shipping_service ? json_encode($shipping_service->toArray()) : json_encode(['price' => $shipping_cost]),
        ]);

        // Handle order status tracking
        $this->setTrackOrder($order);

        // Send SMS notification if status changed
        if ($request->order_status != $order->getOriginal('order_status') || 
            $request->payment_status != $order->getOriginal('payment_status')) {
            $sms = new SmsHelper();
            $user_number = $order->user->phone ?? null;
            if ($user_number) {
                $sms->SendSms($user_number, "order_status", $order->transaction_number);
            }
        }

        return redirect()->route('back.order.index')->withSuccess(__('Order Updated Successfully.'));
    }

    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoice($id)
    {
        $order = Order::findOrfail($id);
        $cart = json_decode($order->cart, true);
        return view('back.order.invoice',compact('order','cart'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printOrder($id)
    {
        $order = Order::findOrfail($id);
        $cart = json_decode($order->cart, true);
        return view('back.order.print',compact('order','cart'));
    }


    /**
     * Change the status for editing the specified resource.
     *
     * @param  int  $id
     * @param  string  $field
     * @param  string  $value
     * @return \Illuminate\Http\Response
     */
    public function status($id,$field,$value)
    {

        $order = Order::find($id);
        if($field == 'payment_status'){
            if($order['payment_status'] == 'Paid'){
                return redirect()->route('back.order.index')->withErrors(__('Order is already paid.'));
            }
        }
        if($field == 'order_status'){
            if($order['order_status'] == 'Delivered'){
                return redirect()->route('back.order.index')->withErrors(__('Order is already Delivered.'));
            }
        }
        $order->update([$field => $value]);
        if($order->payment_status == 'Paid'){
            $this->setPromoCode($order);
        }
        $this->setTrackOrder($order);
        
        $sms = new SmsHelper();
        $user_number = $order->user->phone;
        if($user_number){
            $sms->SendSms($user_number,"'order_status'",$order->transaction_number);
        }
       
        return redirect()->route('back.order.index')->withSuccess(__('Status Updated Successfully.'));
    }

    /**
     * Custom Function
     */
    public function setTrackOrder($order)
    {

        if($order->order_status == 'In Progress'){
            if(!TrackOrder::whereOrderId($order->id)->whereTitle('In Progress')->exists()){
                TrackOrder::create([
                    'title' => 'In Progress',
                    'order_id' => $order->id
                ]);
            }
        }
        if($order->order_status == 'Canceled'){
            if(!TrackOrder::whereOrderId($order->id)->whereTitle('Canceled')->exists()){

                if(!TrackOrder::whereOrderId($order->id)->whereTitle('In Progress')->exists()){
                    TrackOrder::create([
                        'title' => 'In Progress',
                        'order_id' => $order->id
                    ]);
                }
                if(!TrackOrder::whereOrderId($order->id)->whereTitle('Delivered')->exists()){
                    TrackOrder::create([
                        'title' => 'Delivered',
                        'order_id' => $order->id
                    ]);
                }

                if(!TrackOrder::whereOrderId($order->id)->whereTitle('Canceled')->exists()){
                    TrackOrder::create([
                        'title' => 'Canceled',
                        'order_id' => $order->id
                    ]);
                }


            }
        }

        if($order->order_status == 'Delivered'){

            if(!TrackOrder::whereOrderId($order->id)->whereTitle('In Progress')->exists()){
                TrackOrder::create([
                    'title' => 'In Progress',
                    'order_id' => $order->id
                ]);
            }

            if(!TrackOrder::whereOrderId($order->id)->whereTitle('Delivered')->exists()){
                TrackOrder::create([
                    'title' => 'Delivered',
                    'order_id' => $order->id
                ]);
            }
        }
    }


    public function setPromoCode($order)
    {

        $discount = json_decode($order->discount, true);
        if($discount != null){
            $code = PromoCode::find($discount['code']['id']);
            $code->no_of_times--;
            $code->update();
        }
    }


    public function delete($id)
    {
        $order = Order::findOrFail($id);
        $order->tranaction->delete();
        if(Notification::where('order_id',$id)->exists()){
            Notification::where('order_id',$id)->delete();
        }
        if(count($order->tracks_data)>0){
            foreach($order->tracks_data as $track){
                $track->delete();
            }
        }
        $order->delete();
        return redirect()->back()->withSuccess(__('Order Deleted Successfully.'));
    }

    /**
     * Get product details for AJAX requests
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProductDetails($id)
    {
        $item = Item::with('attributes.options')->find($id);
        
        if (!$item) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        
        // Build attributes payload
        $attrs = [];
        foreach($item->attributes as $attr){
            $options = [];
            foreach($attr->options as $opt){
                if($opt->stock != '0'){
                    $options[] = [
                        'id' => $opt->id,
                        'name' => $opt->name,
                        'price' => (float)$opt->price,
                        'stock' => $opt->stock,
                    ];
                }
            }
            if(count($options)){
                $attrs[] = [
                    'id' => $attr->id,
                    'name' => $attr->name,
                    'options' => $options,
                ];
            }
        }

        return response()->json([
            'id' => $item->id,
            'name' => $item->name,
            'price' => $item->discount_price, // use discount price for consistency
            'stock' => $item->stock,
            'photo' => $item->photo ? asset('storage/images/'.$item->photo) : asset('assets/images/placeholder.png'),
            'attributes' => $attrs,
        ]);
    }

}
