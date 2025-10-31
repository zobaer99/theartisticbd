<?php

namespace App\Repositories\Back;

use Auth;
use App\{
    Models\Post,
    Models\User,
    Models\Order,
    Helpers\ImageHelper,
    Helpers\PriceHelper
};
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Item;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Subscriber;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AccountRepository
{

    /**
     * Update profile.
     *
     * @param  \App\Http\Requests\ImageUpdateRequest  $request
     * @return \Illuminate\Http\Response
     */

    public function updateProfile($request)
    {
        $input = $request->all();
        $data = Auth::guard('admin')->user();
        if ($file = $request->file('photo')) {
            $input['photo'] = ImageHelper::handleUpdatedUploadedImage($file,'images',$data,'images/','photo');
        }
        $data->update($input);
    }


    /**
     * Update password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function updatePassword($request)
    {
        $data = Auth::guard('admin')->user();

        if ($request->current_password){
            if (Hash::check($request->current_password, $data->password)){
                if ($request->new_password == $request->renew_password){
                    $input['password'] = Hash::make($request->new_password);
                }else{
                    return [
                        'status'  => false,
                        'message' => __('Confirm password does not match.')
                    ];
                }
            }else{
                return [
                    'status'  => false,
                    'message' => __('Current password Does not match.')
                ];
            }
        }

        $data->update($input);

        return [
            'status'  => true,
            'message' => __('Successfully changed your password')
        ];

    }

    public function getTotalOrders()
    {
        return Order::count();
    }
    public function getPendingOrders()
    {
        return Order::whereOrderStatus('Pending')->count();
    }
    public function getDeliveredOrders()
    {
        return Order::whereOrderStatus('Delivered')->count();
    }
    public function getCanceledOrders()
    {
        return Order::whereOrderStatus('Canceled')->count();
    }

    public function getTotalProductSale()
    {
        $orders = Order::whereOrderStatus('Delivered')->get();
        $total_items_qty = 0;
        foreach($orders as $order){
            $cart = json_decode($order->cart,true);
            foreach($cart as $item){
                $total_items_qty += $item['qty'];
            }
        }
        return $total_items_qty;
    }

    public function getcurrentMonthProductSale()
    {
        $current_date = Carbon::now();
        $explode = explode('-',$current_date->format('d-m-Y'));
        $explode[0] = '1';
        $implode= implode("-",$explode);
        $first_day = Carbon::parse($implode);
        $orders = Order::whereOrderStatus('Delivered')->whereDate('created_at','>=',$first_day)->whereDate('created_at','<=',$current_date)->get();

        $total_items_qty = 0;
        foreach($orders as $order){
            $cart = json_decode($order->cart,true);
            foreach($cart as $item){
                $total_items_qty += $item['qty'];
            }
        }
        return $total_items_qty;
    }

    public function getTodayProductSale()
    {
        $current_date = Carbon::now()->format('Y-m-d');
        $orders = Order::whereDate('created_at', $current_date)->get();
        $total_items_qty = 0;
        foreach($orders as $order){
            $cart = json_decode($order->cart,true);
            foreach($cart as $item){
                $total_items_qty += $item['qty'];
            }
        }
        return $total_items_qty;
    }

    public function getYearProductSale()
    {
        $current_date = Carbon::now();
        $explode = explode('-',$current_date->format('d-m-Y'));
        $explode[0] = '1';
        $year = date('Y-m-d', strtotime('today - 365 days'));
        $orders = Order::whereOrderStatus('Delivered')->whereDate('created_at','>=',$year)->whereDate('created_at','<=',$current_date)->get();
        $total_items_qty = 0;
        foreach($orders as $order){
            $cart = json_decode($order->cart,true);
            foreach($cart as $item){
                $total_items_qty += $item['qty'];
            }
        }
        return $total_items_qty;
    }

    public function getTotalEarning()
    {
        $orders = Order::whereOrderStatus('Delivered')->get();
        $total = 0;
        foreach($orders as $order){
            $total += PriceHelper::OrderTotalChart($order);
        }
        // Currency system commented out - using fixed Taka (৳) currency
        // $curr = Currency::where('is_default',1)->first();
        $setting = Setting::first();
      
        if($setting->currency_direction == 1){
            return '৳' . $total;
        }else{
            return  $total . '৳';
        }
    }

    public function getTodayEarning()
    {
        $current_date = Carbon::now()->format('Y-m-d');
        $total = 0;
        $orders = Order::whereDate('created_at', $current_date)->get();
        foreach($orders as $order){
            $total += PriceHelper::OrderTotalChart($order);
        }

        // Currency system commented out - using fixed Taka (৳) currency
        // $curr = Currency::where('is_default',1)->first();
        $setting = Setting::first();
        if($setting->currency_direction == 1){
            return '৳' . $total;
        }else{
            return  $total . '৳';
        }
    }

    public function getMonthEarning()
    {
        $current_date = Carbon::now();
        $explode = explode('-',$current_date->format('d-m-Y'));
        $explode[0] = '1';
        $implode= implode("-",$explode);
        $first_day = Carbon::parse($implode);
        $total = 0;
        $orders = Order::whereOrderStatus('Delivered')->whereDate('created_at','>=',$first_day)->whereDate('created_at','<=',$current_date)->get();

        foreach($orders as $order){
            $total += PriceHelper::OrderTotalChart($order);
        }

        // Currency system commented out - using fixed Taka (৳) currency
        // $curr = Currency::where('is_default',1)->first();
        $setting = Setting::first();
        if($setting->currency_direction == 1){
            return '৳' . $total;
        }else{
            return  $total . '৳';
        }
    }

    public function getYearEarning()
    {
        $current_date = Carbon::now();
        $explode = explode('-',$current_date->format('d-m-Y'));
        $explode[0] = '1';
        $year = date('Y-m-d', strtotime('today - 365 days'));
        $total = 0;
        $orders = Order::whereOrderStatus('Delivered')->whereDate('created_at','>=',$year)->whereDate('created_at','<=',$current_date)->get();
        foreach($orders as $order){
            $total += PriceHelper::OrderTotalChart($order);
        }

        // Currency system commented out - using fixed Taka (৳) currency
        // $curr = Currency::where('is_default',1)->first();
        $setting = Setting::first();
        if($setting->currency_direction == 1){
            return '৳' . $total;
        }else{
            return  $total . '৳';
        }
    }

    public function getSystemUser()
    {
        return Admin::where('id','!=',1)->count();
    }


    public function getTotalUsers()
    {
        return User::count();
    }

    public function getTotalItems()
    {
        return Item::count();
    }

    public function getRecentOrders()
    {
        return Order::latest('id')->take(10)->get();
    }

    public function getRecentUsers()
    {
        return User::latest('id')->take(10)->get();
    }

    public function getRecentProducts()
    {
        return Item::latest('id')->take(10)->get();
    }
    public function getTotalCategory()
    {
        return Category::count();
    }
    public function getTotalBrand()
    {
        return Brand::count();
    }
    public function getTotalReview()
    {
        return Review::count();
    }
    public function getTotalTransaction()
    {
        return Transaction::count();
    }
    public function getTotalPendingTicket()
    {
        return Ticket::whereStatus('Pending')->count();
    }
    public function getTotalTicket()
    {
        return Ticket::count();
    }
    public function getTotalBlog()
    {
        return Post::count();
    }
    public function getTotalSubscriber()
    {
        return Subscriber::count();
    }

    /**
     * Get earnings within date range
     */
    public function getEarning($startDate = null, $endDate = null)
    {
        $query = Order::where('order_status', 'Delivered');
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $orders = $query->get();
        $total = 0;
        
        foreach ($orders as $order) {
            $total += PriceHelper::OrderTotalChart($order);
        }
        
        return round($total, 2);
    }

    /**
     * Get total sales within date range
     */
    public function getTotalSales($startDate = null, $endDate = null)
    {
        $query = Order::where('order_status', 'Delivered');
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->count();
    }

    /**
     * Get total orders within date range
     */
    public function getTotalOrdersByPeriod($startDate = null, $endDate = null)
    {
        $query = Order::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->count();
    }

    /**
     * Get total users within date range
     */
    public function getTotalUsersByPeriod($startDate = null, $endDate = null)
    {
        $query = User::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->count();
    }

    /**
     * Get recent orders within date range
     */
    public function getRecentOrdersByPeriod($startDate = null, $endDate = null, $limit = 5)
    {
        $query = Order::with('user');
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->orderBy('created_at', 'desc')
                    ->take($limit)
                    ->get()
                    ->map(function($order) {
                        $setting = Setting::first();
                        return [
                            'id' => $order->id,
                            'order_number' => $order->order_number,
                            'transaction_number' => $order->transaction_number ?? $order->order_number,
                            'customer_name' => $order->user ? $order->user->name : 'Guest',
                            'user_id' => $order->user_id,
                            'payment_method' => $order->payment_method ?? 'N/A',
                            'total' => PriceHelper::OrderTotalChart($order),
                            'total_amount' => PriceHelper::OrderTotalChart($order),
                            'currency_sign' => $order->currency_sign ?? ($setting ? $setting->currency_sign : '$'),
                            'order_status' => $order->order_status,
                            'status' => $order->order_status,
                            'shipping_info' => $order->shipping_info,
                            'created_at' => $order->created_at->toISOString(),
                            'created_at_human' => $order->created_at->diffForHumans()
                        ];
                    });
    }

    /**
     * Get today's orders count
     */
    public function getTodayOrders()
    {
        return Order::whereDate('created_at', Carbon::today())->count();
    }

    /**
     * Get today's delivered orders count
     */
    public function getTodayDeliveredOrders()
    {
        return Order::whereDate('created_at', Carbon::today())
                   ->where('order_status', 'Delivered')
                   ->count();
    }

    /**
     * Get today's pending orders count
     */
    public function getTodayPendingOrders()
    {
        return Order::whereDate('created_at', Carbon::today())
                   ->where('order_status', 'Pending')
                   ->count();
    }

    /**
     * Get orders by date range with proper timezone handling
     */
    public function getOrdersByDateRange($startDate, $endDate, $status = null)
    {
        $query = Order::query();
        
        // Only apply date filtering if both dates are provided
        if ($startDate && $endDate) {
            // Convert dates to proper format
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            
            $query->whereBetween('created_at', [$start, $end]);
        }
        
        if ($status) {
            $query->where('order_status', $status);
        }
        
        return $query;
    }

    /**
     * Get earnings by date range with proper calculations
     */
    public function getEarningsByDateRange($startDate, $endDate)
    {
        $orders = $this->getOrdersByDateRange($startDate, $endDate, 'Delivered')->get();
        $total = 0;
        
        foreach ($orders as $order) {
            $total += PriceHelper::OrderTotalChart($order);
        }
        
        return round($total, 2);
    }

    /**
     * Get reviews within date range
     */
    public function getReviewsByPeriod($startDate = null, $endDate = null)
    {
        $query = Review::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->count();
    }

    /**
     * Get subscribers within date range
     */
    public function getSubscribersByPeriod($startDate = null, $endDate = null)
    {
        $query = Subscriber::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->count();
    }

    /**
     * Get products within date range
     */
    public function getProductsByPeriod($startDate = null, $endDate = null)
    {
        $query = Item::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->count();
    }

    /**
     * Get categories within date range
     */
    public function getCategoriesByPeriod($startDate = null, $endDate = null)
    {
        $query = Category::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->count();
    }

    /**
     * Get brands within date range
     */
    public function getBrandsByPeriod($startDate = null, $endDate = null)
    {
        $query = Brand::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->count();
    }

}

