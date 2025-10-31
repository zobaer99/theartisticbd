@extends('master.front')
@section('title')
    {{ __('Orders') }}
@endsection

@section('content')
    <style>
        .table {
            font-size: 0.875rem;
            width: 100% !important;
        }

        .table th {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .table td {
            padding: 1.25rem 1rem;
            vertical-align: middle;
        }

        .badge {
            font-weight: 500;
            padding: 0.5em 0.75em;
            border-radius: 50px;
        }

        .btn-outline-primary {
            border-width: 1px;
            padding: 0.25rem 0.75rem;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
    </style>

    <!-- Page Title-->
    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a> </li>
                        <li class="separator"></li>
                        <li>{{ __('Orders') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Content-->
    <div class="container padding-bottom-3x mb-1">
        <div class="row">
            @include('includes.user_sitebar')
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="ordersTable" class="table table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>{{ __('Order') }} #</th>
                                        <th>{{ __('Total') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Payment') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th class="text-end">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>
                                                <a href="#"
                                                    class="fw-bold text-primary">#{{ $order->transaction_number }}</a>
                                            </td>
                                            <td>
                                                @if ($setting->currency_direction == 1)
                                                    {{ '৳' }}{{ PriceHelper::OrderTotal($order) }}
                                                @else
                                                    {{ PriceHelper::OrderTotal($order) }}{{ '৳' }}
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge 
                                        @if ($order->order_status == 'Pending') bg-info
                                        @elseif($order->order_status == 'In Progress') bg-warning
                                        @elseif($order->order_status == 'Delivered') bg-success
                                        @else bg-danger @endif">
                                                    {{ $order->order_status }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge 
                                        @if ($order->payment_status == 'Paid') bg-success
                                        @else bg-danger @endif">
                                                    {{ $order->payment_status }}
                                                </span>
                                            </td>
                                            <td data-order="{{ $order->created_at->format('Ymd') }}">
                                                {{ $order->created_at->format('d M Y') }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('user.order.invoice', $order->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i> {{ __('Details') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
