@extends('master.front')

@section('title')
    {{ __('Page') }}
@endsection

@section('content')
    <!-- Page Title-->
    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a> </li>
                        <li class="separator">&nbsp;</li>
                        <li>{{ $page->title }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Content-->
    <div class="">
        <div class="container other-page-data">
            <!-- Categories-->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="page-card-title">
                        <h4 class="title"><b>{{ $page->title }}</b></h4>
                    </div>

                    <div class="card-two text-area-card">
                        <div class="d-page-content">
                            {!! $page->details !!}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
