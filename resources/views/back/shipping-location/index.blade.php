@extends('master.back')

@section('content')
<div class="container-fluid">

    <div class="card mb-4">
        <div class="card-body d-flex align-items-center justify-content-between">
            <h3 class="mb-0"><b>{{ __('Address-based Shipping') }}</b></h3>
            <a class="btn btn-primary btn-sm" href="{{ route('back.shipping-location.create') }}"><i class="fas fa-plus"></i> {{ __('Add') }}</a>
        </div>
    </div>

    <div class="card o-hidden border-0 shadow-lg">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table id="shippingLocationTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Location') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @include('back.shipping-location.table', compact('datas'))
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(function(){
            if ($.fn.DataTable) {
        $('#shippingLocationTable').DataTable({
                    pageLength: 25,
                    order: [],
                    columnDefs: [
            { orderable: false, searchable: false, targets: -1 }
                    ]
                });
            }
        });
    </script>
    @endpush
            </div>
        </div>
    </div>

</div>
@endsection
{{-- Removed --}}
