@extends('master.back')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #d1d3e2;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .product-image {
            border-radius: 4px;
        }
        .table td {
            vertical-align: middle;
        }
        .current-attributes {
            font-size: 0.85rem;
        }
        .badge {
            font-size: 0.75rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-sm-flex align-items-center justify-content-between">
            <h3 class="mb-0 bc-title"><b>Create New Order</b></h3>
            <a class="btn btn-primary btn-sm" href="{{ route('back.order.index') }}"><i
                class="fas fa-chevron-left"></i> Back</a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="row">
            <div class="col-lg-12">
                @include('alerts.alerts')
            </div>
        </div>

        <form class="admin-form" action="{{ route('back.order.store') }}" method="POST" enctype="multipart/form-data" id="orderForm">
            @csrf

            <div class="row">
                <!-- Customer & Products Selection -->
                <div class="col-lg-12">
                      <!-- Order Products Section -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Products</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Attributes</th>
                                            <th width="100" class="text-center">Quantity</th>
                                            <th width="120" class="text-right">Unit Price</th>
                                            <th width="120" class="text-right">Total</th>
                                            <th width="80" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products-container">
                                        <tr class="product-item" data-index="0">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='50' height='50'><rect width='100%' height='100%' fill='%23e9ecef'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' font-size='8' fill='%236c757d'>No Image</text></svg>" class="img-thumbnail me-3 product-image" style="width:50px;height:50px;object-fit:cover;" alt="Product">
                                                    <div>
                                                        <select name="products[0][id]" class="form-control select2-product product-select" required style="width: 250px;">
                                                            <option value="">Search and select a product...</option>
                                                            @foreach($items as $item)
                                                                <option value="{{ $item->id }}" 
                                                                        data-price="{{ $item->discount_price }}" 
                                                                        data-name="{{ $item->name }}"
                                                                        data-photo="{{ $item->photo ? asset('storage/images/'.$item->photo) : asset('assets/images/placeholder.png') }}">
                                                                    {{ $item->name }} - ৳{{ number_format($item->discount_price, 2) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="attributes-wrapper" style="display:none;">
                                                    <div class="current-attributes mb-2 p-2 bg-light border rounded">
                                                        <strong class="text-primary d-block mb-1">Current Selection:</strong>
                                                        <div class="current-attributes-display text-muted small">None selected</div>
                                                    </div>
                                                    <div class="row attribute-selects"></div>
                                                    <input type="hidden" name="products[0][attribute_price]" class="attribute-price-input" value="0">
                                                    <input type="hidden" name="products[0][main_price]" class="main-price-input" value="0">
                                                </div>
                                                <span class="text-muted small no-attributes">No attributes available</span>
                                            </td>
                                            <td class="text-center">
                                                <input type="number" name="products[0][qty]" class="form-control text-center product-qty" value="1" min="1" data-price="0" style="width:80px;" required>
                                            </td>
                                            <td class="text-right">
                                                <strong class="unit-price-display">৳0.00</strong>
                                                <input type="hidden" name="products[0][price]" class="product-price" value="0">
                                            </td>
                                            <td class="text-right">
                                                <strong class="line-total-display">৳0.00</strong>
                                                <input type="hidden" class="product-total" value="0.00">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm remove-product" title="Remove Product" style="display: none;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="thead-light">
                                        <tr>
                                            <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                                            <td class="text-right"><strong id="cart-subtotal">৳0.00</strong></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-right"><strong>Shipping:</strong></td>
                                            <td class="text-right">
                                                <strong id="pathao_shipping_display">৳0.00</strong>
                                                <input type="hidden" name="pathao_shipping_cost" id="pathao_shipping_cost" value="">
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr class="table-active">
                                            <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                            <td class="text-right"><strong id="cart-total">৳0.00</strong></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-success" id="addProduct">
                                        <i class="fas fa-plus"></i> Add Product
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    You can adjust product quantities. Prices will be recalculated automatically.
                                </small>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-lg-8">
                    <!-- Customer Selection -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="user_id">Select Customer *</label>
                                <select name="user_id" id="user_id" class="form-control select2-customer" required style="width: 100%;">
                                    <option value="">Search and select a customer...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" data-email="{{ $user->email }}" data-phone="{{ $user->phone }}">
                                            {{ $user->first_name }} {{ $user->last_name }} - {{ $user->email }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="row" id="customer_info" style="display: none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Customer Email</label>
                                        <input type="text" id="customer_email" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Customer Phone</label>
                                        <input type="text" id="customer_phone" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                  
                    <!-- Shipping Information -->
                    <div class="card mb-4 mb-5">
                        <div class="card-header">
                            <h5 class="mb-0">Shipping Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="ship_first_name">Full Name *</label>
                                <input type="text" name="ship_first_name" class="form-control"
                                    id="ship_first_name" placeholder="Enter Full Name" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ship_email">Email</label>
                                        <input type="email" name="ship_email" class="form-control"
                                            id="ship_email" placeholder="Enter Email">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ship_phone">Phone *</label>
                                        <input type="text" name="ship_phone" class="form-control"
                                            id="ship_phone" placeholder="Enter Phone" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="ship_address1">Full Address *</label>
                                <textarea name="ship_address1" id="ship_address1" class="form-control" rows="2" placeholder="Enter Full Address" required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pathao_city_id">Pathao City *</label>
                                        <select name="pathao_city_id" id="pathao_city_id" class="form-control" required>
                                            <option value="">Select City</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pathao_zone_id">Pathao Zone *</label>
                                        <select name="pathao_zone_id" id="pathao_zone_id" class="form-control" required>
                                            <option value="">Select Zone</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pathao_area_id">Pathao Area</label>
                                        <select name="pathao_area_id" id="pathao_area_id" class="form-control">
                                            <option value="">Select Area</option>
                                        </select>
                                    </div>
                                </div>
                            </div>                 
                            <input type="hidden" name="ship_country" value="Bangladesh">
                        </div>
                    </div>
                </div>

                <!-- Order Settings -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="payment_method">Payment Method *</label>
                                <select name="payment_method" id="payment_method" class="form-control" required>
                                    <option value="Cash On Delivery">Cash On Delivery</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Stripe">Stripe</option>
                                    <option value="Paypal">Paypal</option>
                                    <option value="Razorpay">Razorpay</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="payment_status">Payment Status *</label>
                                <select name="payment_status" id="payment_status" class="form-control" required>
                                    <option value="Unpaid">Unpaid</option>
                                    <option value="Paid">Paid</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="order_status">Order Status *</label>
                                <select name="order_status" id="order_status" class="form-control" required>
                                    <option value="Pending">Pending</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Delivered">Delivered</option>
                                    <option value="Canceled">Canceled</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="txnid">Transaction ID</label>
                                <input type="text" name="txnid" class="form-control"
                                    id="txnid" placeholder="Enter Transaction ID">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">Create Order</button>
                            <a href="{{ route('back.order.index') }}" class="btn btn-secondary btn-lg btn-block">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let productIndex = 0;
        
        $(document).ready(function() {
            console.log('Document ready - initializing...');
            
            // Initialize Select2
            initializeSelect2();
            
            // Customer selection change
            $('#user_id').change(function() {
                const selectedOption = $(this).find('option:selected');
                if (selectedOption.val()) {
                    $('#customer_email').val(selectedOption.data('email'));
                    $('#customer_phone').val(selectedOption.data('phone'));
                    $('#customer_info').show();
                    
                    // Auto-fill shipping information
                    $('#ship_email').val(selectedOption.data('email'));
                    $('#ship_phone').val(selectedOption.data('phone'));
                    
                    // Auto-fill customer name
                    const customerName = selectedOption.text().split(' - ')[0];
                    $('#ship_first_name').val(customerName);
                } else {
                    $('#customer_info').hide();
                }
            });

            // Add product functionality
            $('#addProduct').click(function() {
                console.log('Add product clicked, current index:', productIndex);
                productIndex++;
                
                const newRow = `
                    <tr class="product-item" data-index="${productIndex}">
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='50' height='50'><rect width='100%' height='100%' fill='%23e9ecef'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' font-size='8' fill='%236c757d'>No Image</text></svg>" class="img-thumbnail me-3 product-image" style="width:50px;height:50px;object-fit:cover;" alt="Product">
                                <div>
                                    <select name="products[${productIndex}][id]" class="form-control select2-product product-select" required style="width: 250px;">
                                        <option value="">Search and select a product...</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" 
                                                    data-price="{{ $item->discount_price }}" 
                                                    data-name="{{ $item->name }}"
                                                    data-photo="{{ $item->photo ? asset('storage/images/'.$item->photo) : asset('assets/images/placeholder.png') }}">
                                                {{ $item->name }} - ৳{{ number_format($item->discount_price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="attributes-wrapper" style="display:none;">
                                <div class="current-attributes mb-2 p-2 bg-light border rounded">
                                    <strong class="text-primary d-block mb-1">Current Selection:</strong>
                                    <div class="current-attributes-display text-muted small">None selected</div>
                                </div>
                                <div class="row attribute-selects"></div>
                                <input type="hidden" name="products[${productIndex}][attribute_price]" class="attribute-price-input" value="0">
                                <input type="hidden" name="products[${productIndex}][main_price]" class="main-price-input" value="0">
                            </div>
                            <span class="text-muted small no-attributes">No attributes available</span>
                        </td>
                        <td class="text-center">
                            <input type="number" name="products[${productIndex}][qty]" class="form-control text-center product-qty" value="1" min="1" data-price="0" style="width:80px;" required>
                        </td>
                        <td class="text-right">
                            <strong class="unit-price-display">৳0.00</strong>
                            <input type="hidden" name="products[${productIndex}][price]" class="product-price" value="0">
                        </td>
                        <td class="text-right">
                            <strong class="line-total-display">৳0.00</strong>
                            <input type="hidden" class="product-total" value="0.00">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-product" title="Remove Product">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                
                $('#products-container').append(newRow);
                
                // Reinitialize Select2 for the new element
                $(`tr[data-index="${productIndex}"] .select2-product`).select2({
                    placeholder: "Search and select a product...",
                    allowClear: true,
                    width: '250px'
                });
                
                updateRemoveButtons();
                console.log('Product added successfully, new index:', productIndex);
            });

            // Product selection change with attributes fetch
            $(document).on('change', '.product-select', function() {
                const select = $(this);
                const selectedOption = select.find('option:selected');
                const row = select.closest('.product-item');
                const img = row.find('.product-image');
                const attrsWrapper = row.find('.attributes-wrapper');
                const noAttrsSpan = row.find('.no-attributes');
                const attrSelects = row.find('.attribute-selects');
                const attrPriceInput = row.find('.attribute-price-input');
                const mainPriceInput = row.find('.main-price-input');

                // Reset
                attrSelects.empty();
                attrsWrapper.hide();
                noAttrsSpan.show();
                attrPriceInput.val(0);
                mainPriceInput.val(0);
                row.find('.product-price').val('');
                row.find('.product-total').val('0.00');
                row.find('.line-total-display').text('৳0.00');
                row.find('.unit-price-display').text('৳0.00');
                row.find('.current-attributes-display').html('None selected');
                row.find('input.dynamic-attr-name').remove();

                if(!selectedOption.val()) { updateOrderTotal(); return; }

                // Update image
                const photo = selectedOption.data('photo');
                if(photo) img.attr('src', photo);

                const productId = selectedOption.val();
                fetch(`${window.location.origin}/admin/order/product-details/${productId}`)
                    .then(r => r.json())
                    .then(data => {
                        if(data.error){ console.warn(data.error); return; }
                        const basePrice = parseFloat(data.price)||0;
                        mainPriceInput.val(basePrice.toFixed(2));
                        row.find('.product-price').val(basePrice.toFixed(2));
                        row.find('.unit-price-display').text('৳'+basePrice.toFixed(2));
                        updateProductTotal(row);
                        
                        if(Array.isArray(data.attributes) && data.attributes.length){
                            noAttrsSpan.hide();
                            attrsWrapper.show();
                            let html='';
                            data.attributes.forEach(attr => {
                                html += `<div class="col-6 mb-2"><label class="small d-block mb-1">${attr.name}</label><select class="form-control form-control-sm attribute-option" data-attr-name="${attr.name}"><option value="">Select ${attr.name}</option>`;
                                attr.options.forEach(opt => {
                                    html += `<option value="${opt.name}" data-price="${opt.price}">${opt.name}${opt.price? ' (+৳'+opt.price+')':''}</option>`;
                                });
                                html += '</select></div>';
                            });
                            attrSelects.html(html);
                        }
                    })
                    .catch(() => console.error('Failed to fetch product details'));
            });

            // Attribute selection change
            $(document).on('change', '.attribute-option', function(){
                const row = $(this).closest('.product-item');
                let attrPrice = 0; let badges='';
                const mainPrice = parseFloat(row.find('.main-price-input').val())||0;
                const idx = row.data('index');
                row.find('input.dynamic-attr-name').remove();
                row.find('.attribute-option').each(function(){
                    const sel = $(this); const val = sel.val();
                    if(val){
                        const price = parseFloat(sel.find('option:selected').data('price'))||0;
                        attrPrice += price; const label = sel.data('attr-name');
                        badges += `<span class="badge badge-success me-1">${label}: ${val}</span>`;
                        const inputName = `products[${idx}][attributes][${label.toLowerCase()}]`;
                        row.append(`<input type="hidden" class="dynamic-attr-name" name="${inputName}" value="${val}">`);
                    }
                });
                row.find('.current-attributes-display').html(badges || '<span class="text-muted">None selected</span>');
                row.find('.attribute-price-input').val(attrPrice.toFixed(2));
                const unitPrice = mainPrice + attrPrice;
                row.find('.product-price').val(unitPrice.toFixed(2));
                row.find('.unit-price-display').text('৳'+unitPrice.toFixed(2));
                updateProductTotal(row);
            });

            // Quantity change
            $(document).on('input change', '.product-qty', function() {
                console.log('Quantity changed');
                const row = $(this).closest('.product-item');
                updateProductTotal(row);
            });

            // Handle remove product
            $(document).on('click', '.remove-product', function() {
                const row = $(this).closest('.product-item');
                if (confirm('Are you sure you want to remove this product?')) {
                    row.remove();
                    updateOrderTotal();
                    updateRemoveButtons();
                }
            });

            // Initialize
            updateRemoveButtons();
            console.log('Initialization complete');
        });

        function initializeSelect2() {
            $('.select2-customer').select2({
                placeholder: "Search and select a customer...",
                allowClear: true,
                width: '100%'
            });
            
            $('.select2-product').select2({
                placeholder: "Search and select a product...",
                allowClear: true,
                width: '100%'
            });
        }

        function updateProductTotal(row) {
            const price = parseFloat(row.find('.product-price').val()) || 0;
            let qty = parseFloat(row.find('.product-qty').val());
            if(!qty || qty < 1){ qty = 1; row.find('.product-qty').val(1); }
            const total = price * qty;
            const formatted = total.toFixed(2);
            row.find('.product-total').val(formatted);
            row.find('.line-total-display').text('৳'+formatted);
            // Also set the data-price attribute for the qty input
            row.find('.product-qty').attr('data-price', price);
            updateOrderTotal();
        }

        function updateOrderTotal() {
            let subtotal = 0;
            $('.product-total').each(function() {
                subtotal += parseFloat($(this).val()) || 0;
            });
            
            const shipping = parseFloat($('#pathao_shipping_cost').val()) || 0;
            const total = subtotal + shipping;
            
            console.log('Order total updated - Subtotal:', subtotal, 'Shipping:', shipping, 'Total:', total);
            
            $('#cart-subtotal').text('৳' + subtotal.toFixed(2));
            $('#cart-total').text('৳' + total.toFixed(2));
        }

        function removeProduct(index) {
            console.log('Removing product with index:', index);
            $(`.product-item[data-index="${index}"]`).remove();
            updateOrderTotal();
            updateRemoveButtons();
            console.log('Product removed successfully');
        }

        function updateRemoveButtons() {
            const productItems = $('.product-item');
            console.log('Updating remove buttons, product count:', productItems.length);
            
            if (productItems.length > 1) {
                $('.remove-product').show();
            } else {
                $('.remove-product').hide();
            }
        }
        
        // Pathao Integration for Order Create
        $(document).ready(function() {
            // Load cities on page load
            loadPathaoCities();

            // Initialize Select2 for Pathao selects (match edit page behavior)
            if ($.fn.select2) {
                $('#pathao_city_id, #pathao_zone_id, #pathao_area_id').select2({
                    width: '100%',
                    placeholder: function(){ return $(this).data('placeholder') || 'Select'; },
                    allowClear: true,
                    minimumResultsForSearch: 0,
                    language: {
                        noResults: function() { return "কোনো ফলাফল পাওয়া যায়নি"; }
                    }
                });
            }
            
                // Load zones when city changes
                $('#pathao_city_id').change(function() {
                    const cityId = $(this).val();
                    $('#pathao_zone_id').html('<option value="">Select Zone</option>');
                    $('#pathao_area_id').html('<option value="">Select Area</option>');
                    $('#pathao_shipping_cost').val('');
                    // Recalculate totals because shipping cleared
                    updateOrderTotal();

                    if(cityId) {
                        loadPathaoZones(cityId);
                    }
                });

                // Load areas when zone changes
                $('#pathao_zone_id').change(function() {
                    const zoneId = $(this).val();
                    $('#pathao_area_id').html('<option value="">Select Area</option>');
                    $('#pathao_shipping_cost').val('');
                    // Recalculate totals because shipping cleared
                    updateOrderTotal();

                    if(zoneId) {
                        loadPathaoAreas(zoneId);
                        calculatePathaoPrice();
                    }
                });

                // Calculate price when area changes
                $('#pathao_area_id').change(function() {
                    calculatePathaoPrice();
                    updateOrderTotal();
                });

                function loadPathaoCities() {
                    $.get('{{ route("front.pathao.cities") }}')
                    .done(function(cities) {
                        console.log('Cities response (create):', cities);
                        if(Array.isArray(cities) && cities.length > 0) {
                            let options = '<option value="">Select City</option>';
                            cities.forEach(city => {
                                options += `<option value="${city.city_id}">${city.city_name}</option>`;
                            });
                            $('#pathao_city_id').html(options);
                            // Reinitialize Select2 after updating options
                            if ($.fn.select2) {
                                $('#pathao_city_id').select2({
                                    width: '100%',
                                    placeholder: 'Select City',
                                    allowClear: true,
                                    minimumResultsForSearch: 0
                                });
                            }
                        } else {
                            console.error('Invalid cities response:', cities);
                        }
                    })
                    .fail(function() {
                        alert('Failed to load cities');
                    });
                }

                function loadPathaoZones(cityId) {
                    $.get('{{ route("front.pathao.zones") }}', {city_id: cityId})
                    .done(function(zones) {
                        console.log('Zones response (create):', zones);
                        if(Array.isArray(zones) && zones.length > 0) {
                            let options = '<option value="">Select Zone</option>';
                            zones.forEach(zone => {
                                options += `<option value="${zone.zone_id}">${zone.zone_name}</option>`;
                            });
                            $('#pathao_zone_id').html(options);
                            // Reinitialize Select2 after updating options
                            if ($.fn.select2) {
                                $('#pathao_zone_id').select2({
                                    width: '100%',
                                    placeholder: 'Select Zone',
                                    allowClear: true,
                                    minimumResultsForSearch: 0
                                });
                            }
                        } else {
                            console.error('Invalid zones response:', zones);
                        }
                    })
                    .fail(function() {
                        alert('Failed to load zones');
                    });
                }

                function loadPathaoAreas(zoneId) {
                    $.get('{{ route("front.pathao.areas") }}', {zone_id: zoneId})
                    .done(function(areas) {
                        console.log('Areas response (create):', areas);
                        if(Array.isArray(areas) && areas.length > 0) {
                            let options = '<option value="">Select Area</option>';
                            areas.forEach(area => {
                                options += `<option value="${area.area_id}">${area.area_name}</option>`;
                            });
                            $('#pathao_area_id').html(options);
                            // Reinitialize Select2 after updating options
                            if ($.fn.select2) {
                                $('#pathao_area_id').select2({
                                    width: '100%',
                                    placeholder: 'Select Area',
                                    allowClear: true,
                                    minimumResultsForSearch: 0
                                });
                            }
                        } else {
                            console.error('Invalid areas response:', areas);
                        }
                    })
                    .fail(function() {
                        alert('Failed to load areas');
                    });
                }

                function calculatePathaoPrice() {
                    const cityId = $('#pathao_city_id').val();
                    const zoneId = $('#pathao_zone_id').val();
                    const areaId = $('#pathao_area_id').val();

                    if(cityId && zoneId) {
                        const params = { city_id: cityId, zone_id: zoneId };
                        if(areaId) params.area_id = areaId;

                        $.get('{{ route("front.pathao.price") }}', params)
                        .done(function(response) {
                            console.log('Pathao price response (create):', response);
                            // Normalize possible response shapes
                            let price = null;
                            if(response === null || response === undefined) {
                                price = null;
                            } else if(typeof response === 'number') {
                                price = response;
                            } else if(response.price !== undefined) {
                                price = response.price;
                            } else if(response.data && response.data.price !== undefined) {
                                price = response.data.price;
                            } else if(response.amount !== undefined) {
                                price = response.amount;
                            }

                            price = parseFloat(price) || 0;
                            // Set hidden input value
                            $('#pathao_shipping_cost').val(price);
                            // Update display text
                            try { $('#pathao_shipping_display').text('৳' + price.toFixed(2)); } catch(e){}
                            // Trigger change in case other handlers listen for it
                            try { $('#pathao_shipping_cost').trigger('change'); } catch(e){}
                            // Recalculate totals
                            updateOrderTotal();
                        })
                        .fail(function(xhr, status, error) {
                            console.error('Pathao price calculation error:', { xhr, status, error });
                            alert('Failed to calculate shipping price');
                        });
                    }
                }
        });
    </script>
@endsection
