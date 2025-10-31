@extends('master.back')


<link rel="stylesheet" href="{{ asset('assets/back/fonts/flaticon/flaticon.css') }}">
<link rel="stylesheet" href="{{ asset('assets/back/css/fonts.css') }}">
<link rel="stylesheet" href="{{ asset('assets/back/css/dashboard.css') }}">

@section('content')

<!-- Main Dashboard Card Container -->
<div class="dashboard-page-wrapper">
<div class="card border-0 shadow-lg" style="border-radius: 20px;">
    <div class="card-body p-4">
        @if(session()->has('multipledomain'))
            <div class="alert alert-danger" style="background-color: #FFE4E4;" id="license_alert">
                <strong>One Purchase Code Use in multiple domain :</strong>
                @foreach (session()->get('multipledomain') as $item)
                    <p style="margin-bottom: 0px;color: #155724;">{{ $item }}</p>
                @endforeach
                <hr>
                <strong>
                    {{ __('Envato not allow to install script multiple domin using one purchase code. ') }}
                    <br>
                    {{ __('One purched codes for one Domin. Author can take action any time for that.') }}
                    <br>
                    <hr>
                    {{ __('Author Contact : geniusdevs24@gmail.com') }}
                </strong>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="text-dark mb-1 fw-bold">
                    {{ __('Welcome to E-Commerce Dashboard') }}
                </h1>
                <p class="lead text-dark opacity-75">
                    {{ __('Manage your online store efficiently') }}
                </p>
            </div>
            <div class="col-md-6 text-end">
                <div class="d-flex align-items-center justify-content-end">
                    <div class="me-4 mr-3">
                        <div id="current-time" class="h4 text-danger mb-0 fw-bold">{{ now()->format('H:i:s') }}</div>
                        <div id="current-date" class="text-dark small opacity-75">{{ now()->format('l, F j, Y') }}</div>
                    </div>
                    <div class="position-relative">
                        <!-- Canvas Analog Clock -->
                        <div id="analog-clock" class="analog-clock">
                            <canvas id="clockCanvas" width="120" height="120" style="background-color: white; border-radius: 50%; box-shadow: 0 8px 25px rgba(0,0,0,0.3), inset 0 2px 10px rgba(0,0,0,0.1);">
                                Sorry, your browser does not support canvas.
                            </canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs for Data Filtering -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 bg-transparent">
                    <div class="card-body p-0">
                        <div class="row">
                            <!-- Navigation Row -->
                            <div class="col-12">
                                <div class="row align-items-center">
                                    <!-- Time Period Tabs -->
                                    <div class="col-12">
                                        <div class="dashboard-tabs-container d-flex align-items-center flex-wrap gap-2  for_borders">
                                            <button class="dashboard-tab-btn" data-period="all" type="button">
                                                <i class="flaticon-circle me-2"></i>All Time
                                            </button>
                                            <button class="dashboard-tab-btn active" data-period="today" type="button">
                                                <i class="flaticon-calendar me-2"></i>Today
                                            </button>
                                            <button class="dashboard-tab-btn" data-period="week" type="button">
                                                <i class="flaticon-calendar me-2"></i>This Week
                                            </button>
                                            <button class="dashboard-tab-btn" data-period="month" type="button">
                                                <i class="flaticon-calendar me-2"></i>This Month
                                            </button>
                                            <button class="dashboard-tab-btn" data-period="year" type="button">
                                                <i class="flaticon-calendar me-2"></i>This Year
                                            </button>
                                            <button class="dashboard-tab-btn" data-period="custom" type="button">
                                                <i class="flaticon-calendar me-2"></i>Custom Range
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Date Range Filter (shown when Custom Range is selected) -->
                            <div class="col-12" id="date-range-container" style="display: none;">
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="card border-0" style="background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 100%); backdrop-filter: blur(10px); border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                                            <div class="card-body p-4">
                                                <div class="row align-items-end">
                                                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                                        <label class="text-white small mb-2 fw-semibold d-block">
                                                            <i class="flaticon-calendar me-2"></i>Start Date
                                                        </label>
                                                        <input type="date" class="form-control bg-opacity-20 border-0 shadow-sm" id="start-date" value="{{ date('Y-m-d', strtotime('-7 days')) }}" style="backdrop-filter: blur(10px); border-radius: 8px;">
                                                    </div>
                                                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                                        <label class="text-white small mb-2 fw-semibold d-block">
                                                            <i class="flaticon-calendar me-2"></i>End Date
                                                        </label>
                                                        <input type="date" class="form-control bg-opacity-20 border-0 shadow-sm" id="end-date" value="{{ date('Y-m-d') }}" style="backdrop-filter: blur(10px); border-radius: 8px;">
                                                    </div>
                                                    <div class="col-lg-4 col-md-8 mb-3 mb-lg-0">
                                                        <label class="text-white small mb-2 fw-semibold d-block">
                                                            <i class="flaticon-settings me-2"></i>Quick Select
                                                        </label>
                                                        <div class="d-flex gap-3 flex-wrap" id="quick-select-buttons">
                                                            <button class="btn btn-sm btn-outline-light border-2 quick-select-btn mr-2" data-days="7" onclick="setQuickRange(7)" style="border-radius: 20px; color:rgb(3, 0, 0);">7 Days</button>
                                                            <button class="btn btn-sm btn-outline-light border-2 quick-select-btn mr-2" data-days="30" onclick="setQuickRange(30)" style="border-radius: 20px; color:rgb(2, 0, 0);">30 Days</button>
                                                            <button class="btn btn-sm btn-outline-light border-2 quick-select-btn mr-2" data-days="90" onclick="setQuickRange(90)" style="border-radius: 20px; color:rgb(1, 0, 0);">3 Months</button>
                                                            <button class="btn btn-sm btn-outline-light border-2 quick-select-btn" data-days="365" onclick="setQuickRange(365)" style="border-radius: 20px; color:rgb(0, 0, 0);">1 Year</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-md-4">
                                                        <div class="d-flex gap-3">
                                                            <button class="btn btn-success fw-semibold shadow-sm mr-2" id="apply-date-range" style="border-radius: 8px;">
                                                                <i class="flaticon-check me-1 pr-2"></i>Apply
                                                            </button>
                                                            <button class="btn btn-outline-light shadow-sm" id="reset-date-range" style="border-radius: 8px;">
                                                                <i class="flaticon-repeat me-1 pr-2"></i>Reset
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Top Statistics Row with Gradient Cards - Dynamic Data -->
        <div class="row mb-4" id="statistics-cards">
            <!-- Total Orders Card -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                <div class="card border-0 shadow-lg h-100 hover-card gradient-card-1">
                    <div class="card-body text-center text-white">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title rounded-circle">
                                <i class="flaticon-cart font-size-28 center"></i>
                            </div>
                        </div>
                        <h3 class="text-white mb-1 fw-bold" id="total-orders">
                            {{ $totalOrders }}
                        </h3>
                        <p class="text-white-50 mb-0 font-size-13">Total Orders</p>
                    </div>
                </div>
            </div>

            <!-- Pending Orders Card -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                <div class="card border-0 shadow-lg h-100 hover-card gradient-card-5">
                    <div class="card-body text-center text-white">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title rounded-circle">
                                <i class="flaticon-clock font-size-28"></i>
                            </div>
                        </div>
                        <h3 class="text-white mb-1 fw-bold" id="pending-orders">
                            {{ $totalPendingOrders }}
                        </h3>
                        <p class="text-white-50 mb-0 font-size-13">Pending Orders</p>
                    </div>
                </div>
            </div>

            <!-- Delivered Orders Card -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                <div class="card border-0 shadow-lg h-100 hover-card gradient-card-2">
                    <div class="card-body text-center text-white">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title rounded-circle">
                                <i class="flaticon-check font-size-28"></i>
                            </div>
                        </div>
                        <h3 class="text-white mb-1 fw-bold" id="delivered-orders">
                            {{ $totalDeliveredOrders }}
                        </h3>
                        <p class="text-white-50 mb-0 font-size-13">Delivered Orders</p>
                    </div>
                </div>
            </div>

            <!-- Canceled Orders Card -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                <div class="card border-0 shadow-lg h-100 hover-card gradient-card-7">
                    <div class="card-body text-center text-white">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title rounded-circle">
                                <i class="flaticon-close font-size-28"></i>
                            </div>
                        </div>
                        <h3 class="text-white mb-1 fw-bold" id="canceled-orders">
                            {{ $totalCanceledOrders }}
                        </h3>
                        <p class="text-white-50 mb-0 font-size-13">Canceled Orders</p>
                    </div>
                </div>
            </div>

            <!-- Total Customers Card -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                <div class="card border-0 shadow-lg h-100 hover-card gradient-card-3">
                    <div class="card-body text-center text-white">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title rounded-circle">
                                <i class="flaticon-users font-size-28"></i>
                            </div>
                        </div>
                        <h3 class="text-white mb-1 fw-bold" id="total-customers">
                            {{ $totalUsers }}
                        </h3>
                        <p class="text-white-50 mb-0 font-size-13">Total Customers</p>
                    </div>
                </div>
            </div>

            <!-- Total Products Card -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                <div class="card border-0 shadow-lg h-100 hover-card gradient-card-4">
                    <div class="card-body text-center text-white">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title rounded-circle">
                                <i class="flaticon-shopping-bag font-size-28"></i>
                            </div>
                        </div>
                        <h3 class="text-white mb-1 fw-bold" id="total-products">
                            {{ $totalItems }}
                        </h3>
                        <p class="text-white-50 mb-0 font-size-13">Total Products</p>
                    </div>
                </div>
            </div>

            <!-- Total Categories Card -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                <div class="card border-0 shadow-lg h-100 hover-card gradient-card-6">
                    <div class="card-body text-center text-white">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title rounded-circle">
                                <i class="flaticon-list font-size-28"></i>
                            </div>
                        </div>
                        <h3 class="text-white mb-1 fw-bold" id="total-categories">
                            {{ $totalCategory }}
                        </h3>
                        <p class="text-white-50 mb-0 font-size-13">Total Categories</p>
                    </div>
                </div>
            </div>

            <!-- Total Brands Card -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                <div class="card border-0 shadow-lg h-100 hover-card gradient-card-8">
                    <div class="card-body text-center text-white">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title rounded-circle">
                                <i class="flaticon-price-tag font-size-28"></i>
                            </div>
                        </div>
                        <h3 class="text-white mb-1 fw-bold" id="total-brands">
                            {{ $totalBrand }}
                        </h3>
                        <p class="text-white-50 mb-0 font-size-13">Total Brands</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings and Sales Statistics Row -->
        <div class="row mb-4">
            <!-- Total Earning Card -->
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-lg h-100 hover-card gradient-card-earning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-md me-3">
                                <div class="avatar-title rounded-3">
                                    <i class="flaticon-coins font-size-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 pl-3">
                                <p class="text-white-50 mb-1 font-size-13 fw-medium" id="earning-label">Total Earning</p>
                                <h4 class="mb-1 text-white fw-bold font-size-24" id="total-earning">
                                    {{ $totalEarning }}
                                </h4>
                                <p class="text-white-50 mb-0 font-size-11">
                                   <span id="earning-period">All Time Revenue</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Sales Card -->
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-lg h-100 hover-card gradient-card-sales">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-md me-3">
                                <div class="avatar-title rounded-3">
                                    <i class="flaticon-shopping-bag font-size-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 pl-3">
                                <p class="text-white-50 mb-1 font-size-13 fw-medium">Product Sales</p>
                                <h4 class="mb-1 text-white fw-bold font-size-24" id="product-sales">
                                    {{ $totalProductSale }}
                                </h4>
                                <p class="text-white-50 mb-0 font-size-11">
                                    Items Sold
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Reviews Card -->
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-lg h-100 hover-card gradient-card-reviews">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-md me-3">
                                <div class="avatar-title rounded-3">
                                    <i class="flaticon-star font-size-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 pl-3">
                                <p class="text-white-50 mb-1 font-size-13 fw-medium">Total Reviews</p>
                                <h4 class="mb-1 text-white fw-bold font-size-24" id="total-reviews">
                                    {{ $totalReview }}
                                </h4>
                                <p class="text-white-50 mb-0 font-size-11">
                                   Customer Feedback
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Subscribers Card -->
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-lg h-100 hover-card gradient-card-subscribers">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-md me-3">
                                <div class="avatar-title rounded-3">
                                    <i class="flaticon-envelope font-size-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 pl-3">
                                <p class="text-white-50 mb-1 font-size-13 fw-medium">Newsletter Subscribers</p>
                                <h4 class="mb-1 text-white fw-bold font-size-24" id="total-subscribers">
                                    {{ $totalSubscriber }}
                                </h4>
                                <p class="text-white-50 mb-0 font-size-11">
                                    Email List
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Order Management Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px;">
                    <div class="card-header border-0 bg-transparent">
                        <h5 class="card-title mb-0 text-white">
                            <i class="flaticon-settings me-2"></i>{{ __('Quick Order Management') }}
                        </h5>
                        <p class="text-white-50 mb-0 small">{{ __('Fast access to order management tools') }}</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- View All Orders -->
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                <a href="{{ route('back.order.index') }}" class="btn btn-light btn-lg w-100 text-center shadow-sm hover-lift" style="border-radius: 12px;">
                                    <i class="flaticon-cart d-block mb-2" style="font-size: 24px; color: #667eea;"></i>
                                    <span class="small fw-medium text-dark">{{ __('All Orders') }}</span>
                                </a>
                            </div>

                            <!-- Pending Orders -->
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                <a href="{{ route('back.order.index') }}?type=Pending" class="btn btn-warning btn-lg w-100 text-center shadow-sm hover-lift" style="border-radius: 12px;">
                                    <i class="flaticon-clock d-block mb-2" style="font-size: 24px;"></i>
                                    <span class="small fw-medium">{{ __('Pending') }}</span>
                                </a>
                            </div>

                            <!-- In Progress Orders -->
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                <a href="{{ route('back.order.index') }}?type=In Progress" class="btn btn-info btn-lg w-100 text-center shadow-sm hover-lift" style="border-radius: 12px;">
                                    <i class="flaticon-gear d-block mb-2" style="font-size: 24px;"></i>
                                    <span class="small fw-medium">{{ __('In Progress') }}</span>
                                </a>
                            </div>

                            <!-- Delivered Orders -->
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                <a href="{{ route('back.order.index') }}?type=Delivered" class="btn btn-success btn-lg w-100 text-center shadow-sm hover-lift" style="border-radius: 12px;">
                                    <i class="flaticon-check d-block mb-2" style="font-size: 24px;"></i>
                                    <span class="small fw-medium">{{ __('Delivered') }}</span>
                                </a>
                            </div>

                            <!-- Canceled Orders -->
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                <a href="{{ route('back.order.index') }}?type=Canceled" class="btn btn-danger btn-lg w-100 text-center shadow-sm hover-lift" style="border-radius: 12px;">
                                    <i class="flaticon-close d-block mb-2" style="font-size: 24px;"></i>
                                    <span class="small fw-medium">{{ __('Canceled') }}</span>
                                </a>
                            </div>

                            <!-- Export Orders -->
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                <a href="{{ route('back.csv.order.export') }}" class="btn btn-secondary btn-lg w-100 text-center shadow-sm hover-lift" style="border-radius: 12px;">
                                    <i class="flaticon-download d-block mb-2" style="font-size: 24px;"></i>
                                    <span class="small fw-medium">{{ __('Export CSV') }}</span>
                                </a>
                            </div>
                        </div>

                        <!-- Recent Orders Quick View -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="rounded p-3">
                                    <h6 class="text-white mb-3">
                                        <i class="flaticon-list me-2"></i>{{ __('Recent Orders') }}
                                        <a href="{{ route('back.order.index') }}" class="btn btn-sm btn-outline-light float-right" style="border-radius: 20px;">
                                            {{ __('View All') }} <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </h6>
                                    
                                    @php
                                        $recentOrders = \App\Models\Order::with('user')->latest()->take(5)->get();
                                    @endphp

                                    @if($recentOrders->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-white-text" id="recent-orders-table">
                                                <thead>
                                                    <tr class="text-white-50">
                                                        <th class="text-white">{{ __('Order ID') }}</th>
                                                        <th class="text-white">{{ __('Customer') }}</th>
                                                        <th class="text-white">{{ __('Status') }}</th>
                                                        <th class="text-white">{{ __('Total') }}</th>
                                                        <th class="text-white">{{ __('Date') }}</th>
                                                        <th class="text-white">{{ __('Actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recentOrders as $order)
                                                        <tr>
                                                            <td class="text-white">#{{ $order->transaction_number }}</td>
                                                            <td class="text-white">
                                                                @php
                                                                    $shipping = json_decode($order->shipping_info, true);
                                                                @endphp
                                                                {{ $shipping['ship_first_name'] ?? 'N/A' }}
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-{{ $order->order_status == 'Pending' ? 'warning' : ($order->order_status == 'Delivered' ? 'success' : ($order->order_status == 'Canceled' ? 'danger' : 'info')) }}">
                                                                    {{ $order->order_status }}
                                                                </span>
                                                            </td>
                                                            <td class="text-white fw-medium">
                                                                @if ($setting->currency_direction == 1)
                                                                    {{ '৳' }}{{ \App\Helpers\PriceHelper::OrderTotal($order) }}
                                                                @else
                                                                    {{ \App\Helpers\PriceHelper::OrderTotal($order) }}{{ '৳' }}
                                                                @endif
                                                            </td>
                                                            <td class="text-white">{{ $order->created_at->format('M d, Y') }}</td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="{{ route('back.order.invoice', $order->id) }}" class="btn btn-sm btn-light" title="{{ __('View') }}">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('back.order.edit', $order->id) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="flaticon-cart display-4 text-dark-50 mb-3"></i>
                                            <p class="text-dark-50 mb-0">{{ __('No recent orders found') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <!-- Orders Chart -->
            <div class="col-xl-3 col-lg-6 mb-3">
                <div class="card border-0 shadow-lg gradient-card-chart1">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="card-title mb-0 text-white" id="orders-chart-title">
                            <i class="flaticon-cart me-2 pr-2"></i>Orders - Today
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="ordersChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Revenue Chart -->
            <div class="col-xl-3 col-lg-6 mb-3">
                <div class="card border-0 shadow-lg gradient-card-chart2">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="card-title mb-0 text-white" id="revenue-chart-title">
                            <i class="flaticon-analytics me-2 pr-2"></i>Revenue - Today
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="earningsChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Categories Chart -->
            <div class="col-xl-3 col-lg-6 mb-3">
                <div class="card border-0 shadow-lg gradient-card-chart3">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="card-title mb-0 text-white" id="categories-chart-title">
                            <i class="flaticon-chart-pie me-2 pr-2"></i>Sales - Today
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Products Chart -->
            <div class="col-xl-3 col-lg-6 mb-3">
                <div class="card border-0 shadow-lg gradient-card-chart4">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="card-title mb-0 text-white" id="products-chart-title">
                            <i class="flaticon-users me-2 pr-2"></i>Customers - Today
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="customersChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar and Recent Orders Row -->
        <div class="row mb-4">
            <!-- Calendar Widget -->
            <div class="col-xl-4 col-lg-4">
                <div id="calendar-widget">
                    <div class="calendar-header">
                        <button type="button" class="calendar-nav-btn" data-action="prev">‹</button>
                        <div class="calendar-month-year">
                            <h6 class="calendar-title" id="current-month">{{ now()->format('F') }}</h6>
                            <div class="calendar-subtitle" id="current-year">{{ now()->format('Y') }}</div>
                        </div>
                        <button type="button" class="calendar-nav-btn" data-action="next">›</button>
                    </div>
                    
                    <div class="calendar-controls">
                        <button type="button" class="calendar-control-btn" data-action="today">Today</button>
                        <button type="button" class="calendar-control-btn" data-action="clear">Clear</button>
                    </div>
                    
                    <div id="calendar-grid" class="calendar-grid">
                        <!-- Calendar will be generated by JavaScript -->
                    </div>
                    
                    <div class="calendar-footer">
                        <div class="selected-date-info" id="selected-date-info">No date selected</div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="col-xl-8 col-lg-8">
                <div class="card border-0 shadow-lg gradient-card-recent">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="card-title mb-0 text-white">
                            <i class="flaticon-cart text-white me-2 pr-2"></i>Recent Orders
                            <span class="badge bg-info bg-opacity-20 text-white ms-2">
                                {{ $recentOrders->count() }} orders
                            </span>
                        </h5>
                    </div>
                    <div class="card-body pt-0">
                        @if ($recentOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-borderless table-white-text">
                                    <thead>
                                        <tr>
                                            <th class="text-white-50">{{ __('Customer') }}</th>
                                            <th class="text-white-50">{{ __('Order ID') }}</th>
                                            <th class="text-white-50">{{ __('Payment Method') }}</th>
                                            <th class="text-white-50">{{ __('Total') }}</th>
                                            <th class="text-white-50">{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentOrders as $data)
                                        <tr>
                                            <td class="text-white">
                                                <a href="{{route('back.user.show',$data->user_id)}}" class="text-white text-decoration-none">
                                                    {{ $data->user->displayName() }}
                                                </a>
                                            </td>
                                            <td class="text-warning">
                                                <a href="{{route('back.order.invoice',$data->id)}}" class="text-warning text-decoration-none">
                                                    #{{ $data->transaction_number}}
                                                </a>
                                            </td>
                                            <td class="text-white-50">
                                                {{ $data->payment_method}}
                                            </td>
                                            <td class="text-white fw-semibold">
                                                {{ '৳' }}{{\App\Helpers\PriceHelper::OrderTotal($data)}}
                                            </td>
                                            <td>
                                                @if($data->order_status == 'Pending')
                                                    <span class="badge bg-warning text-dark">{{ $data->order_status }}</span>
                                                @elseif($data->order_status == 'Delivered')
                                                    <span class="badge bg-success text-white">{{ $data->order_status }}</span>
                                                @elseif($data->order_status == 'Canceled')
                                                    <span class="badge bg-danger text-white">{{ $data->order_status }}</span>
                                                @else
                                                    <span class="badge bg-info text-white">{{ $data->order_status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="flaticon-cart text-white-50 font-size-48"></i>
                                <p class="text-white-50 mt-2">{{ __('No Recent Orders Found') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
{{-- <link rel="stylesheet" href="{{ asset('assets/back/css/dashboard.css') }}"> --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
.dashboard-tab-btn {
    border: 2px solid transparent;
    background: transparent;
    color: #6c757d;
    padding: 8px 16px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
}

.dashboard-tab-btn:hover {
    background: rgba(0,123,255,0.1);
    color: #007bff;
    border-color: rgba(0,123,255,0.3);
}

.dashboard-tab-btn.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
    box-shadow: 0 2px 4px rgba(0,123,255,0.3);
}

.table-responsive {
    background: rgba(255,255,255,0.1);
    border-radius: 8px;
    padding: 15px;
}

.table th, .table td {
    border-color: rgba(255,255,255,0.1);
    vertical-align: middle;
}

.badge {
    font-size: 0.75em;
    padding: 0.375rem 0.5rem;
}

/* Fix text visibility */
.text-white {
    color: #ffffff !important;
}

.text-white-50 {
    color: rgba(255,255,255,0.5) !important;
}

/* Ensure table text is visible */
.table-white-text th,
.table-white-text td {
    color: #ffffff !important;
}

/* Loading states */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

@endsection

@section('scripts')
<script>
// Real-time clock update
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', { 
        hour12: true,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    const dateString = now.toLocaleDateString('en-US', { 
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    const timeElement = document.getElementById('current-time');
    const dateElement = document.getElementById('current-date');
    
    if (timeElement) timeElement.textContent = timeString;
    if (dateElement) dateElement.textContent = dateString;
}

// Update clock every second
setInterval(updateClock, 1000);

// Initialize clock immediately
updateClock();

// Enhanced tab functionality for new dashboard tabs
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.dashboard-tab-btn');
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs
            tabs.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Show/hide custom date range
            const period = this.getAttribute('data-period');
            const dateContainer = document.getElementById('date-range-container');
            
            if (period === 'custom') {
                if (dateContainer) {
                    dateContainer.style.display = 'block';
                    dateContainer.classList.add('show');
                }
            } else {
                if (dateContainer) {
                    dateContainer.classList.remove('show');
                    setTimeout(() => {
                        dateContainer.style.display = 'none';
                    }, 300);
                }
                
                // Update dashboard data for the selected period
                updateDashboardData(period);
            }
            
            console.log('Selected period:', period);
        });
    });
    
    // Function to update dashboard data via AJAX
    function updateDashboardData(period, startDate = null, endDate = null) {
        const url = '{{ route("back.dashboard.data.post") }}';
        const data = {
            period: period,
            start_date: startDate,
            end_date: endDate,
            _token: '{{ csrf_token() }}'
        };
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Dashboard data received:', data);
                // Update statistics cards
                updateStatisticsCards(data);
                // Update charts
                updateCharts(data);
                // Update recent orders table
                updateRecentOrdersTable(data.recentOrders);
                console.log('Dashboard data updated successfully');
            } else {
                console.error('Error updating dashboard data:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching dashboard data:', error);
        });
    }
    
    // Function to update statistics cards
    function updateStatisticsCards(data) {
        if (document.getElementById('total-orders')) {
            document.getElementById('total-orders').textContent = data.totalOrders || 0;
        }
        if (document.getElementById('pending-orders')) {
            document.getElementById('pending-orders').textContent = data.totalPendingOrders || 0;
        }
        if (document.getElementById('delivered-orders')) {
            document.getElementById('delivered-orders').textContent = data.totalDeliveredOrders || 0;
        }
        if (document.getElementById('canceled-orders')) {
            document.getElementById('canceled-orders').textContent = data.totalCanceledOrders || 0;
        }
        if (document.getElementById('total-customers')) {
            document.getElementById('total-customers').textContent = data.totalCustomers || 0;
        }
        if (document.getElementById('total-products')) {
            document.getElementById('total-products').textContent = data.totalProducts || 0;
        }
        if (document.getElementById('total-categories')) {
            document.getElementById('total-categories').textContent = data.totalCategories || 0;
        }
        if (document.getElementById('total-brands')) {
            document.getElementById('total-brands').textContent = data.totalBrands || 0;
        }
        if (document.getElementById('total-earning')) {
            document.getElementById('total-earning').textContent = data.totalEarning || '0';
        }
        if (document.getElementById('product-sales')) {
            document.getElementById('product-sales').textContent = data.productSales || 0;
        }
        if (document.getElementById('total-reviews')) {
            document.getElementById('total-reviews').textContent = data.totalReviews || 0;
        }
        if (document.getElementById('total-subscribers')) {
            document.getElementById('total-subscribers').textContent = data.totalSubscribers || 0;
        }
    }
    
    // Function to update charts
    function updateCharts(data) {
        // Update chart titles based on period
        const period = data.period || 'today';
        const periodText = period.charAt(0).toUpperCase() + period.slice(1);
        
        if (document.getElementById('orders-chart-title')) {
            document.getElementById('orders-chart-title').innerHTML = '<i class="flaticon-cart me-2 pr-2"></i>Orders - ' + periodText;
        }
        if (document.getElementById('revenue-chart-title')) {
            document.getElementById('revenue-chart-title').innerHTML = '<i class="flaticon-analytics me-2 pr-2"></i>Revenue - ' + periodText;
        }
        if (document.getElementById('categories-chart-title')) {
            document.getElementById('categories-chart-title').innerHTML = '<i class="flaticon-chart-pie me-2 pr-2"></i>Sales - ' + periodText;
        }
        if (document.getElementById('products-chart-title')) {
            document.getElementById('products-chart-title').innerHTML = '<i class="flaticon-users me-2 pr-2"></i>Customers - ' + periodText;
        }
        
        // Update charts with new data
        if (window.ordersChart && data.salesChart) {
            window.ordersChart.data = data.salesChart;
            window.ordersChart.update();
        }
        if (window.earningsChart && data.earningsChart) {
            window.earningsChart.data = data.earningsChart;
            window.earningsChart.update();
        }
        if (window.salesChart && data.ordersChart) {
            window.salesChart.data = data.ordersChart;
            window.salesChart.update();
        }
        if (window.customersChart && data.customersChart) {
            window.customersChart.data = data.customersChart;
            window.customersChart.update();
        }
    }
    
    // Function to update recent orders table
    function updateRecentOrdersTable(recentOrders) {
        const tableBody = document.querySelector('#recent-orders-table tbody');
        if (!tableBody) {
            console.log('Recent orders table body not found');
            return;
        }
        
        if (!recentOrders) {
            console.log('No recent orders data received');
            return;
        }
        
        console.log('Updating recent orders table with:', recentOrders);
        
        // Clear existing rows
        tableBody.innerHTML = '';
        
        if (recentOrders.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <i class="flaticon-cart display-4 text-white-50 mb-3"></i>
                        <p class="text-white-50 mb-0">No recent orders found</p>
                    </td>
                </tr>
            `;
            return;
        }
        
        // Add new rows
        recentOrders.forEach(order => {
            const shipping = order.shipping_info ? JSON.parse(order.shipping_info) : {};
            const customerName = shipping.ship_first_name || 'N/A';
            const statusBadgeClass = 
                order.order_status === 'Pending' ? 'bg-warning' :
                order.order_status === 'Delivered' ? 'bg-success' :
                order.order_status === 'Canceled' ? 'bg-danger' : 'bg-info';
            
            const orderDate = new Date(order.created_at).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
            
            // Calculate order total (this should match the PriceHelper calculation)
            const orderTotal = parseFloat(order.total_amount || order.amount || 0).toFixed(2);
            const currencySign = '৳'; // Fixed currency sign
            
            const row = `
                <tr>
                    <td class="text-white">#${order.transaction_number}</td>
                    <td class="text-white">${customerName}</td>
                    <td>
                        <span class="badge ${statusBadgeClass} text-white">${order.order_status}</span>
                    </td>
                    <td class="text-white fw-medium">${currencySign}${orderTotal}</td>
                    <td class="text-white">${orderDate}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ url('admin/order/invoice') }}/${order.id}" class="btn btn-sm btn-light" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ url('admin/order/edit') }}/${order.id}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    }
});
</script>

<script src="{{ asset('assets/back/js/dashboard.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize Dashboard Manager when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the enhanced dashboard
    window.dashboardManager = new DashboardManager();
    console.log('Dashboard Manager initialized');
});
</script>


@endsection






