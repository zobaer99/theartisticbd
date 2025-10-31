@extends('master.back')

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-body d-flex align-items-center justify-content-between">
            <h3 class="mb-0"><b>{{ __('Add Address-based Shipping') }}</b></h3>
            <a class="btn btn-primary btn-sm" href="{{ route('back.shipping-location.index') }}"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card o-hidden border-0 shadow-lg">
                <div class="card-body">
                    <form class="admin-form" action="{{ route('back.shipping-location.store') }}" method="POST">
                        @include('back.shipping-location._form')
                        <div class="form-group">
                            <button type="submit" class="btn btn-secondary btn-block">{{ __('Submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
{{-- Removed --}}
