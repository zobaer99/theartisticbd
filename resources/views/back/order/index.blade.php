@extends('master.back')
@section('styles')
	<link rel="stylesheet" href="{{asset('assets/back/css/datepicker.css')}}">
@endsection
@section('content')



<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class=" mb-0 bc-title"><b>{{ request()->input('type') ? request()->input('type') : 'All' }} Orders</b></h3>
                <div class="right">
        <a href="{{route('back.order.create')}}" class="btn btn-primary btn-sm d-inline-block"><i class="fas fa-plus"></i> Create Order</a>
        <a href="{{route('back.csv.order.export')}}" class="btn btn-info btn-sm d-inline-block">CSV Export</a>
            <a href="{{route('back.order.index')}}?type=Pending" class="btn btn-warning btn-sm d-inline-block">Pending Orders</a>
            <a href="{{route('back.order.index')}}?type=In Progress" class="btn btn-primary btn-sm d-inline-block">In Progress</a>
            <a href="{{route('back.order.index')}}?type=Delivered" class="btn btn-success btn-sm d-inline-block">Delivered</a>
            <a href="{{route('back.order.index')}}?type=Canceled" class="btn btn-danger btn-sm d-inline-block">Canceled</a>
                  <form class="d-inline-block" action="{{route('back.bulk.delete')}}" method="get">
                    <input type="hidden" value="" name="ids[]" id="bulk_delete">
                    <input type="hidden" value="orders" name="table">
                    <button class="btn btn-danger btn-sm">Delete</button>
                  </form>
                  <form class="d-inline-block" action="{{route('back.bulk.pathao')}}" method="get">
                    <input type="hidden" value="" name="ids[]" id="bulk_pathao">
                    <input type="hidden" value="orders" name="table">
                    <button class="btn btn-info btn-sm">Send to Pathao</button>
                  </form>
                  <form class="d-inline-block" action="#" method="post" id="bulk_pathao_status_form">
                    @csrf
                    <input type="hidden" value="" name="order_ids[]" id="bulk_pathao_status">
                    <button type="button" class="btn btn-secondary btn-sm" id="get_pathao_status_btn">Get Pathao Status</button>
                  </form>
              </div>
              </div>
        </div>
    </div>

	<!-- DataTales -->
	<div class="card shadow mb-4">
		<div class="card-body">

        <form action="{{route('back.order.index')}}" method="GET">
          <div class="row mb-4">
            <!-- Date Range Filters -->
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="text" name="start_date" id="datepicker" class="form-control datepicker"
                        placeholder="Start Date"
                        value="{{ request('start_date') }}">
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="text" name="end_date" id="datepicker1" class="form-control datepicker"
                        placeholder="End Date"
                        value="{{ request('end_date') }}">
                </div>
            </div>
            
            <!-- Order ID Filter -->
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="order_id">Order ID</label>
                    <input type="text" name="order_id" class="form-control"
                        placeholder="Enter Order ID"
                        value="{{ request('order_id') }}">
                </div>
            </div>
            
            <!-- Transaction ID Filter -->
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="transaction_id">Transaction ID</label>
                    <input type="text" name="transaction_id" class="form-control"
                        placeholder="Enter Transaction ID"
                        value="{{ request('transaction_id') }}">
                </div>
            </div>
            
            <!-- User Name Filter -->
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="user_name">User Name</label>
                    <input type="text" name="user_name" class="form-control"
                        placeholder="Enter User Name"
                        value="{{ request('user_name') }}">
                </div>
            </div>
            
            <!-- User Phone Filter -->
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="user_phone">User Phone</label>
                    <input type="text" name="user_phone" class="form-control"
                        placeholder="Enter Phone Number"
                        value="{{ request('user_phone') }}">
                </div>
            </div>
            
            <!-- Payment Status Filter -->
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="payment_status">Payment Status</label>
                    <select name="payment_status" class="form-control">
                        <option value="">All Payment Status</option>
                        <option value="Paid" {{ request('payment_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                        <option value="Unpaid" {{ request('payment_status') == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                    </select>
                </div>
            </div>
            
            <!-- Payment Method Filter -->
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select name="payment_method" class="form-control">
                        <option value="">All Payment Methods</option>
                        <option value="Cash On Delivery" {{ request('payment_method') == 'Cash On Delivery' ? 'selected' : '' }}>Cash On Delivery</option>
                        <option value="Paypal" {{ request('payment_method') == 'Paypal' ? 'selected' : '' }}>Paypal</option>
                        <option value="Stripe" {{ request('payment_method') == 'Stripe' ? 'selected' : '' }}>Stripe</option>
                        <option value="Razorpay" {{ request('payment_method') == 'Razorpay' ? 'selected' : '' }}>Razorpay</option>
                        <option value="Mollie" {{ request('payment_method') == 'Mollie' ? 'selected' : '' }}>Mollie</option>
                        <option value="Instamojo" {{ request('payment_method') == 'Instamojo' ? 'selected' : '' }}>Instamojo</option>
                        <option value="Paytabs" {{ request('payment_method') == 'Paytabs' ? 'selected' : '' }}>Paytabs</option>
                        <option value="Mercadopago" {{ request('payment_method') == 'Mercadopago' ? 'selected' : '' }}>Mercadopago</option>
                        <option value="Authorize.net" {{ request('payment_method') == 'Authorize.net' ? 'selected' : '' }}>Authorize.net</option>
                    </select>
                </div>
            </div>
            
            <!-- Filter Buttons -->
            <div class="col-lg-12 text-center mt-3">
                <button type="submit" class="btn btn-success py-2 px-4 mr-2">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{route('back.order.index')}}" class="btn btn-info py-2 px-4">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>
        </div>
        </form>


			@include('alerts.alerts')
			<div class="gd-responsive-table">
				<table class="table table-bordered table-striped" id="admin-table" width="100%" cellspacing="0">

					<thead>
						<tr>
              <th> <input type="checkbox" data-target="order-bulk-delete" class="form-control bulk_all_delete"> </th>
                            <th>Order ID</th>
                            <th>Order Date</th>
                            <th>User Info</th>
                            <th>Phone</th>
                            <th>Total Amount</th>
                            <th>Payment Method</th>
                            <th>Transaction ID</th>
                            <th>Payment Status</th>
                            <th>Order Status</th>
                            <th>Pathao Status</th>
							<th>Actions</th>
						</tr>
					</thead>

					<tbody>
              @include('back.order.table',compact('datas'))
					</tbody>

				</table>
			</div>
		</div>
	</div>

</div>



{{-- STATUS MODAL --}}

<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

		<!-- Modal Header -->
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Update Status?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
		</div>

		<!-- Modal Body -->
      <div class="modal-body">
                You are going to update the status. Do you want proceed?
		  </div>

		<!-- Modal footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <a href="" class="btn btn-ok btn-success">Update</a>
		</div>

      </div>
    </div>
  </div>

{{-- STATUS MODAL ENDS --}}

{{-- DELETE MODAL --}}

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="confirm-deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

  <!-- Modal Header -->
      <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Confirm Delete?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
  </div>

  <!-- Modal Body -->
      <div class="modal-body">
    You are going to delete this order. All contents related with this order will be lost. Do you want to delete it?
  </div>

  <!-- Modal footer -->
      <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    <form action="" class="d-inline btn-ok" method="POST">

      @csrf
      @method('DELETE')
    <button type="submit" class="btn btn-danger">Delete</button>

    </form>
  </div>

    </div>
  </div>
</div>

{{-- DELETE MODAL ENDS --}}

@section('scripts')
<script>
$(document).ready(function() {
    // Load Pathao status for orders with consignment IDs on page load
    loadPathaoStatuses();

    // Handle bulk checkbox selection for Pathao operations
    $('[data-checkboxes]').on('click', function(){
        $('.bulk-item:checked').each(function(){
            $('#bulk_delete').val($('#bulk_delete').val() + $(this).val() + ',');
            $('#bulk_pathao').val($('#bulk_pathao').val() + $(this).val() + ',');
            $('#bulk_pathao_status').val($('#bulk_pathao_status').val() + $(this).val() + ',');
        });
    });

    $('#all_orders').on('click', function(){
        if($(this).is(':checked')){
            $('.bulk-item').prop('checked', true);
        } else {
            $('.bulk-item').prop('checked', false);
        }
        var ids = '';
        $('.bulk-item:checked').each(function(){
            ids += $(this).val() + ',';
        });
        $('#bulk_delete').val(ids);
        $('#bulk_pathao').val(ids);
        $('#bulk_pathao_status').val(ids);
    });

    $('.bulk-item').on('click', function(){
        var ids = '';
        $('.bulk-item:checked').each(function(){
            ids += $(this).val() + ',';
        });
        $('#bulk_delete').val(ids);
        $('#bulk_pathao').val(ids);
        $('#bulk_pathao_status').val(ids);
    });

    // Handle Pathao status get
    $('#get_pathao_status_btn').on('click', function() {
        var selectedIds = [];
        $('.bulk-item:checked').each(function(){
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            alert('Please select at least one order to get Pathao status.');
            return;
        }

        // Confirm action
        if (!confirm('Are you sure you want to fetch Pathao status for ' + selectedIds.length + ' selected orders?')) {
            return;
        }

        getPathaoStatusForOrders(selectedIds);
    });

    function loadPathaoStatuses() {
        // Get all orders with Pathao consignment IDs
        var orderIds = [];
        $('.pathao-status-container').each(function() {
            orderIds.push($(this).data('order-id'));
        });

        if (orderIds.length > 0) {
            getPathaoStatusForOrders(orderIds, false);
        }
    }

    function getPathaoStatusForOrders(orderIds, showAlert = true) {
        // Show loading state for bulk button if needed
        if (showAlert) {
            $('#get_pathao_status_btn').prop('disabled', true).text('Fetching...');
        }

        // Send AJAX request
        $.ajax({
            url: '{{ route("back.bulk.pathao.status") }}',
            method: 'POST',
            data: {
                order_ids: orderIds,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Update status displays
                    if (response.statuses) {
                        Object.keys(response.statuses).forEach(function(orderId) {
                            var statusData = response.statuses[orderId];
                            updatePathaoStatusDisplay(orderId, statusData.status, statusData.fetched_at);
                        });
                    }

                    if (showAlert) {
                        alert(response.message);
                    }
                } else {
                    if (showAlert) {
                        alert('Error: ' + (response.message || 'Failed to fetch Pathao status'));
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching Pathao status:', error);
                if (showAlert) {
                    alert('Failed to fetch Pathao status. Please try again.');
                }
            },
            complete: function() {
                if (showAlert) {
                    $('#get_pathao_status_btn').prop('disabled', false).text('Get Pathao Status');
                }
            }
        });
    }

    function updatePathaoStatusDisplay(orderId, status, fetchedAt) {
        var container = $('.pathao-status-container[data-order-id="' + orderId + '"]');
        if (container.length > 0) {
            var statusClass = getStatusClass(status);
            var statusIcon = getStatusIcon(status);
            var displayText = status ? status.replace(/_/g, ' ') : 'Unknown';
            
            container.html(
                '<span class="badge ' + statusClass + '">' +
                '<i class="' + statusIcon + '"></i> ' + displayText +
                '</span>' +
                '<br><small class="text-muted">Fetched: ' + new Date(fetchedAt).toLocaleString() + '</small>'
            );
        }
    }

    function getStatusClass(status) {
        switch(status ? status.toLowerCase() : '') {
            case 'pickup_pending':
            case 'delivered_approval_pending':
            case 'delivered':
                return 'badge-success';
            case 'in_transit':
            case 'delivery_in_progress':
                return 'badge-info';
            case 'partial_delivered':
                return 'badge-warning';
            case 'cancelled':
            case 'return_in_progress':
            case 'returned':
                return 'badge-danger';
            default:
                return 'badge-secondary';
        }
    }

    function getStatusIcon(status) {
        switch(status ? status.toLowerCase() : '') {
            case 'pickup_pending':
                return 'fas fa-clock';
            case 'in_transit':
            case 'delivery_in_progress':
                return 'fas fa-shipping-fast';
            case 'delivered':
                return 'fas fa-check-circle';
            case 'cancelled':
            case 'returned':
                return 'fas fa-times-circle';
            case 'partial_delivered':
                return 'fas fa-exclamation-triangle';
            default:
                return 'fas fa-info-circle';
        }
    }
});
</script>
@endsection

@endsection
