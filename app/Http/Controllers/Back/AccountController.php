<?php

namespace App\Http\Controllers\Back;

use Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;

use App\{
    Http\Controllers\Controller,
    Http\Requests\ImageUpdateRequest,
    Repositories\Back\AccountRepository
};
use App\Helpers\PriceHelper;
use App\Models\Item;
use App\Models\Order;
use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Setting;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * @var AccountRepository
     */
    protected $repository;

    /**
     * Constructor Method.
     *
     * Setting Authentication
     *
     * @param  \App\Repositories\Back\AccountRepository $repository
     *
     */
    public function __construct(AccountRepository $repository)
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');

        $this->repository = $repository;
    }

    /**
     * Get dashboard data for AJAX requests
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardData(Request $request)
    {
        try {
            $period = $request->get('period', 'today');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            // Calculate date range based on period
            $dateRange = $this->calculateDateRange($period, $startDate, $endDate);

            $data = [
                'totalEarning' => $this->repository->getEarning($dateRange['start'], $dateRange['end']),
                'totalSales' => $this->repository->getTotalSales($dateRange['start'], $dateRange['end']),
                'totalOrders' => $this->repository->getTotalOrdersByPeriod($dateRange['start'], $dateRange['end']),
                'totalPendingOrders' => $this->repository->getOrdersByDateRange($dateRange['start'], $dateRange['end'], 'Pending')->count(),
                'totalDeliveredOrders' => $this->repository->getOrdersByDateRange($dateRange['start'], $dateRange['end'], 'Delivered')->count(),
                'totalCanceledOrders' => $this->repository->getOrdersByDateRange($dateRange['start'], $dateRange['end'], 'Canceled')->count(),
                'totalCustomers' => $this->repository->getTotalUsersByPeriod($dateRange['start'], $dateRange['end']),
                'totalProducts' => $this->repository->getProductsByPeriod($dateRange['start'], $dateRange['end']), // Now dynamic by period
                'totalCategories' => $this->repository->getCategoriesByPeriod($dateRange['start'], $dateRange['end']), // Now dynamic by period
                'totalBrands' => $this->repository->getBrandsByPeriod($dateRange['start'], $dateRange['end']), // Now dynamic by period
                'totalReviews' => $this->repository->getReviewsByPeriod($dateRange['start'], $dateRange['end']),
                'totalSubscribers' => $this->repository->getSubscribersByPeriod($dateRange['start'], $dateRange['end']),
                'productSales' => $this->repository->getTotalSales($dateRange['start'], $dateRange['end']),
                'recentOrders' => $this->repository->getRecentOrdersByPeriod($dateRange['start'], $dateRange['end']),
                'salesChart' => $this->generateSalesChart($dateRange['start'], $dateRange['end'], $period),
                'earningsChart' => $this->generateEarningsChart($dateRange['start'], $dateRange['end'], $period),
                'ordersChart' => $this->generateOrdersChart($dateRange['start'], $dateRange['end']),
                'customersChart' => $this->generateCustomersChart($dateRange['start'], $dateRange['end'], $period),
                'period' => $period,
                'success' => true,
                'timestamp' => now()->toISOString()
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Dashboard Data Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching dashboard data: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Calculate date range based on period
     */
    private function calculateDateRange($period, $startDate = null, $endDate = null)
    {
        $end = now();
        $start = now();

        switch ($period) {
            case 'today':
                $start = now()->startOfDay();
                $end = now()->endOfDay();
                break;
            case 'week':
                $start = now()->startOfWeek();
                $end = now()->endOfWeek();
                break;
            case 'month':
                $start = now()->startOfMonth();
                $end = now()->endOfMonth();
                break;
            case 'year':
                $start = now()->startOfYear();
                $end = now()->endOfYear();
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $start = \Carbon\Carbon::parse($startDate)->startOfDay();
                    $end = \Carbon\Carbon::parse($endDate)->endOfDay();
                } else {
                    // Fallback to today if custom dates are invalid
                    $start = now()->startOfDay();
                    $end = now()->endOfDay();
                }
                break;
            case 'all':
            default: // all-time
                $start = null;
                $end = null;
                break;
        }

        return ['start' => $start, 'end' => $end];
    }

    /**
     * Generate sales chart data
     */
    private function generateSalesChart($startDate, $endDate, $period)
    {
        $labels = [];
        $data = [];

        try {
            if ($period === 'today') {
                // Hourly data for today
                for ($i = 0; $i < 24; $i++) {
                    $hour = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                    $labels[] = $hour;
                    
                    $count = Order::where('order_status', 'Delivered')
                        ->whereDate('created_at', now())
                        ->whereHour('created_at', $i)
                        ->count();
                    $data[] = $count;
                }
            } elseif ($period === 'week') {
                // Daily data for the week
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $labels[] = $date->format('M d');
                    
                    $count = Order::where('order_status', 'Delivered')
                        ->whereDate('created_at', $date)
                        ->count();
                    $data[] = $count;
                }
            } elseif ($period === 'month') {
                // Daily data for the month
                $daysInMonth = now()->daysInMonth;
                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $date = now()->startOfMonth()->addDays($i - 1);
                    $labels[] = $date->format('M d');
                    
                    $count = Order::where('order_status', 'Delivered')
                        ->whereDate('created_at', $date)
                        ->count();
                    $data[] = $count;
                }
            } elseif ($period === 'custom' && $startDate && $endDate) {
                // Handle custom date range
                $start = \Carbon\Carbon::parse($startDate);
                $end = \Carbon\Carbon::parse($endDate);
                $daysDiff = $start->diffInDays($end);
                
                if ($daysDiff <= 31) {
                    // Daily data for custom range
                    while ($start <= $end) {
                        $labels[] = $start->format('M d');
                        $count = Order::where('order_status', 'Delivered')
                            ->whereDate('created_at', $start)
                            ->count();
                        $data[] = $count;
                        $start->addDay();
                    }
                } else {
                    // Monthly data for longer ranges
                    $currentMonth = $start->copy()->startOfMonth();
                    while ($currentMonth <= $end) {
                        $labels[] = $currentMonth->format('M Y');
                        $count = Order::where('order_status', 'Delivered')
                            ->whereYear('created_at', $currentMonth->year)
                            ->whereMonth('created_at', $currentMonth->month)
                            ->count();
                        $data[] = $count;
                        $currentMonth->addMonth();
                    }
                }
            } else {
                // Monthly data for the year or all time
                for ($i = 11; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $labels[] = $date->format('M Y');
                    
                    $count = Order::where('order_status', 'Delivered')
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
                    $data[] = $count;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Sales Chart Generation Error: ' . $e->getMessage());
            // Return empty chart data
            $labels = ['No Data'];
            $data = [0];
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Sales',
                'data' => $data,
                'borderColor' => 'rgba(255, 255, 255, 0.8)',
                'backgroundColor' => 'rgba(255, 255, 255, 0.1)',
                'tension' => 0.4,
                'fill' => true
            ]]
        ];
    }

    /**
     * Generate earnings chart data
     */
    private function generateEarningsChart($startDate, $endDate, $period)
    {
        $labels = [];
        $data = [];

        try {
            if ($period === 'today') {
                // Hourly data for today
                for ($i = 0; $i < 24; $i++) {
                    $hour = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                    $labels[] = $hour;
                    
                    $earnings = Order::where('order_status', 'Delivered')
                        ->whereDate('created_at', now())
                        ->whereHour('created_at', $i)
                        ->get()
                        ->sum(function($order) {
                            return PriceHelper::OrderTotalChart($order);
                        });
                    $data[] = round($earnings, 2);
                }
            } elseif ($period === 'week') {
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $labels[] = $date->format('D');
                    
                    $earnings = Order::where('order_status', 'Delivered')
                        ->whereDate('created_at', $date)
                        ->get()
                        ->sum(function($order) {
                            return PriceHelper::OrderTotalChart($order);
                        });
                    $data[] = round($earnings, 2);
                }
            } elseif ($period === 'month') {
                // Weekly data for the month
                for ($i = 3; $i >= 0; $i--) {
                    $weekStart = now()->subWeeks($i)->startOfWeek();
                    $weekEnd = now()->subWeeks($i)->endOfWeek();
                    $labels[] = 'Week ' . (4 - $i);
                    
                    $earnings = Order::where('order_status', 'Delivered')
                        ->whereBetween('created_at', [$weekStart, $weekEnd])
                        ->get()
                        ->sum(function($order) {
                            return PriceHelper::OrderTotalChart($order);
                        });
                    $data[] = round($earnings, 2);
                }
            } elseif ($period === 'custom' && $startDate && $endDate) {
                // Handle custom date range
                $start = \Carbon\Carbon::parse($startDate);
                $end = \Carbon\Carbon::parse($endDate);
                $daysDiff = $start->diffInDays($end);
                
                if ($daysDiff <= 7) {
                    // Daily data for week or less
                    while ($start <= $end) {
                        $labels[] = $start->format('M d');
                        $earnings = Order::where('order_status', 'Delivered')
                            ->whereDate('created_at', $start)
                            ->get()
                            ->sum(function($order) {
                                return PriceHelper::OrderTotalChart($order);
                            });
                        $data[] = round($earnings, 2);
                        $start->addDay();
                    }
                } else {
                    // Weekly or monthly data for longer ranges
                    $currentWeek = $start->copy()->startOfWeek();
                    $weekCount = 1;
                    while ($currentWeek <= $end) {
                        $weekEndDate = $currentWeek->copy()->endOfWeek();
                        if ($weekEndDate > $end) $weekEndDate = $end;
                        
                        $labels[] = 'Week ' . $weekCount;
                        $earnings = Order::where('order_status', 'Delivered')
                            ->whereBetween('created_at', [$currentWeek, $weekEndDate])
                            ->get()
                            ->sum(function($order) {
                                return PriceHelper::OrderTotalChart($order);
                            });
                        $data[] = round($earnings, 2);
                        $currentWeek->addWeek();
                        $weekCount++;
                    }
                }
            } else {
                // Monthly data for year or all time
                for ($i = 11; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $labels[] = $date->format('M');
                    
                    $earnings = Order::where('order_status', 'Delivered')
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->get()
                        ->sum(function($order) {
                            return PriceHelper::OrderTotalChart($order);
                        });
                    $data[] = round($earnings, 2);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Earnings Chart Generation Error: ' . $e->getMessage());
            // Return empty chart data
            $labels = ['No Data'];
            $data = [0];
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Earnings',
                'data' => $data,
                'backgroundColor' => 'rgba(255, 255, 255, 0.3)',
                'borderColor' => 'rgba(255, 255, 255, 0.8)',
                'borderWidth' => 1
            ]]
        ];
    }

    /**
     * Generate orders chart data (by status)
     */
    private function generateOrdersChart($startDate, $endDate)
    {
        $baseQuery = Order::query();
        
        if ($startDate && $endDate) {
            $baseQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $completed = (clone $baseQuery)->where('order_status', 'Delivered')->count();
        $pending = (clone $baseQuery)->where('order_status', 'Pending')->count();
        $cancelled = (clone $baseQuery)->where('order_status', 'Canceled')->count();

        return [
            'labels' => ['Completed', 'Pending', 'Cancelled'],
            'datasets' => [[
                'data' => [$completed, $pending, $cancelled],
                'backgroundColor' => [
                    'rgba(255, 255, 255, 0.4)',
                    'rgba(255, 255, 255, 0.2)',
                    'rgba(255, 255, 255, 0.1)'
                ],
                'borderColor' => [
                    'rgba(255, 255, 255, 0.8)',
                    'rgba(255, 255, 255, 0.6)',
                    'rgba(255, 255, 255, 0.4)'
                ],
                'borderWidth' => 2
            ]]
        ];
    }

    /**
     * Generate customers chart data
     */
    private function generateCustomersChart($startDate, $endDate, $period)
    {
        $labels = [];
        $data = [];

        try {
            if ($period === 'today') {
                // Hourly data for today
                for ($i = 0; $i < 24; $i++) {
                    $hour = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                    $labels[] = $hour;
                    
                    $count = \App\Models\User::whereDate('created_at', now())
                        ->whereHour('created_at', $i)
                        ->count();
                    $data[] = $count;
                }
            } elseif ($period === 'week') {
                // Daily data for the week
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $labels[] = $date->format('M d');
                    
                    $count = \App\Models\User::whereDate('created_at', $date)->count();
                    $data[] = $count;
                }
            } elseif ($period === 'month') {
                // Weekly data for the month
                for ($i = 3; $i >= 0; $i--) {
                    $weekStart = now()->subWeeks($i)->startOfWeek();
                    $weekEnd = now()->subWeeks($i)->endOfWeek();
                    $labels[] = 'Week ' . (4 - $i);
                    
                    $count = \App\Models\User::whereBetween('created_at', [$weekStart, $weekEnd])->count();
                    $data[] = $count;
                }
            } elseif ($period === 'custom' && $startDate && $endDate) {
                // Handle custom date range
                $start = \Carbon\Carbon::parse($startDate);
                $end = \Carbon\Carbon::parse($endDate);
                $daysDiff = $start->diffInDays($end);
                
                if ($daysDiff <= 31) {
                    // Daily data for custom range
                    while ($start <= $end) {
                        $labels[] = $start->format('M d');
                        $count = \App\Models\User::whereDate('created_at', $start)->count();
                        $data[] = $count;
                        $start->addDay();
                    }
                } else {
                    // Monthly data for longer ranges
                    $currentMonth = $start->copy()->startOfMonth();
                    while ($currentMonth <= $end) {
                        $labels[] = $currentMonth->format('M Y');
                        $count = \App\Models\User::whereYear('created_at', $currentMonth->year)
                            ->whereMonth('created_at', $currentMonth->month)
                            ->count();
                        $data[] = $count;
                        $currentMonth->addMonth();
                    }
                }
            } else {
                // Monthly data for year or all time
                for ($i = 11; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $labels[] = $date->format('M');
                    
                    $count = \App\Models\User::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
                    $data[] = $count;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Customers Chart Generation Error: ' . $e->getMessage());
            // Return empty chart data
            $labels = ['No Data'];
            $data = [0];
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'New Customers',
                'data' => $data,
                'borderColor' => 'rgba(255, 255, 255, 0.8)',
                'backgroundColor' => 'rgba(255, 255, 255, 0.2)',
                'tension' => 0.4,
                'fill' => true
            ]]
        ];
    }

    /**
     * Get calendar events for dashboard
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalendarEvents(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $date = $request->get('date');

        if ($date) {
            // Get events for specific date
            $events = Order::whereDate('created_at', $date)
                ->select('id', 'order_number', 'order_status', 'created_at')
                ->get()
                ->map(function($order) {
                    return [
                        'title' => "Order #{$order->order_number}",
                        'type' => 'order',
                        'status' => $order->order_status,
                        'time' => $order->created_at->format('H:i')
                    ];
                });
            
            return response()->json($events);
        }

        // Get events for the month
        $events = [];
        $orders = Order::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get()
            ->groupBy(function($order) {
                return $order->created_at->format('Y-m-d');
            });

        foreach ($orders as $date => $dayOrders) {
            $events[$date] = true; // Mark that this date has events
        }

        return response()->json($events);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $days = "";
        $sales = "";
        for($i = 0; $i < 30; $i++) {
            $days .= "'".date("d M", strtotime('-'. $i .' days'))."',";
            $sales .=  "'".Order::where('order_status','=','Delivered')->whereDate('created_at', '=', date("Y-m-d", strtotime('-'. $i .' days')))->count()."',";
        }


        $earning_days = "";
        $total_incomess = '';
        $income = "";
        $check = 0;
        for($i = 0; $i < 30; $i++) {
            $earning_days .= "'".date("d M", strtotime('-'. $i .' days'))."',";
            $incomes = Order::where('order_status','=','Delivered')->whereDate('created_at', '=', date("Y-m-d", strtotime('-'. $i .' days')))->get();

            if($incomes->count() > 0){
                foreach($incomes as $income){
                    $check += PriceHelper::OrderTotalChart($income);
                }
                $total_incomess .=  "'".$check."',";
            }else{
                $total_incomess .=  "'".'0'."',";
            }
        }

        $earning_days =rtrim($earning_days, ", ");
        $check_income =rtrim($total_incomess, ", ");

        return view('back.dashboard.index',[
            'totalUsers' => $this->repository->getTotalUsers(),
            'totalItems' => $this->repository->getTotalItems(),
            'totalOrders' => $this->repository->getTotalOrders(),
            'totalPendingOrders' => $this->repository->getPendingOrders(),
            'totalDeliveredOrders' => $this->repository->getDeliveredOrders(),
            'totalCanceledOrders' => $this->repository->getCanceledOrders(),
            'recentUsers' => $this->repository->getRecentUsers(),
            'recentOrders' => $this->repository->getRecentOrders(),
            'totalBrand' => $this->repository->getTotalBrand(),
            'totalCategory' => $this->repository->getTotalCategory(),
            'totalReview' => $this->repository->getTotalReview(),
            'totalTransaction' => $this->repository->getTotalTransaction(),
            'totalPendingTicket' => $this->repository->getTotalPendingTicket(),
            'totalTicket' => $this->repository->getTotalTicket(),
            'totalBlog' => $this->repository->getTotalBlog(),
            'totalSubscriber' => $this->repository->getTotalSubscriber(),
            'totalProductSale' => $this->repository->getTotalProductSale(),
            'totalCurrentMonthProductSale' => $this->repository->getcurrentMonthProductSale(),
            'totalTodayProductSale' => $this->repository->getTodayProductSale(),
            'totalLatYearProductSale' => $this->repository->getYearProductSale(),
            'totalEarning' => $this->repository->getTotalEarning(),
            'totalTodayEarning' => $this->repository->getTodayEarning(),
            'totalMonthEarning' => $this->repository->getMonthEarning(),
            'totalYearEarning' => $this->repository->getYearEarning(),
            'totalSystemUserEarning' => $this->repository->getSystemUser(),
            'order_days' => $days,
            'earning_days' => $earning_days,
            'order_sales' => $sales,
            'total_incomess' => $check_income,
            'setting' => Setting::first(),
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profileForm()
    {
        $data = FacadesAuth::guard('admin')->user();
        return view('back.dashboard.profile',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(ImageUpdateRequest $request)
    {
        $this->repository->updateProfile($request);
        return redirect()->back()->withSuccess(__('Profile Updated Successfully!'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function passwordResetForm()
    {
        return view('back.dashboard.password');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|min:4|max:16',
            'new_password' => 'required|min:4|max:16',
            'renew_password' => 'required|min:4|max:16',
        ]);

        $resp = $this->repository->updatePassword($request);

        if($resp['status']){
            return redirect()->back()->withSuccess($resp['message']);
        }else{
            return redirect()->back()->withErrors($resp['message']);
        }

    }

}
