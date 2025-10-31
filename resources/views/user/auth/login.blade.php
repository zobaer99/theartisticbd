@extends('master.front')
@section('title')
    {{ __('Login') }}
@endsection
@section('content')
    <!-- Page Title-->
    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a> </li>
                        <li class="separator"></li>
                        <li>{{ __('Login') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Content-->

    <div class="container padding-bottom-3x mb-1">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="auth-form-card">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <span class="login-icon"><i class="far fa-user"></i></span>
                        <h4 class="margin-bottom-1x text-center">{{ __('Login') }}</h4>
                    </div>

                    <form class="login-form" method="post" action="{{ route('user.login.submit') }}">
                        @csrf
                        <div class="auth-form-card-body ">
                            <div class="row justify-content-center align-items-center g-4 or-sign-in-with-row">
                                {{-- Form-part --}}
                                <div class="col-md-6">
                                    <label class="auth-form-input-label" for="login_email">Email / Phone *</label>
                                    <div class="form-group input-group">
                                        <input class="form-control" type="email" name="login_email"
                                            placeholder="{{ __('Email') }}" value="{{ old('login_email') }}"><span
                                            class="input-group-addon"><i class="icon-mail"></i></span>
                                    </div>
                                    @error('login_email')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                    <label class="auth-form-input-label" for="login_password">Password *</label>
                                    <div class="form-group input-group">
                                        <input class="form-control" type="password" name="login_password"
                                            placeholder="{{ __('Password') }}"><span class="input-group-addon"><i
                                                class="icon-lock"></i></span>
                                    </div>
                                    @error('login_password')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror

                                    <div class="form-bottom-part">
                                        <div class="d-flex flex-wrap justify-content-between">
                                            <div class="custom-control custom-checkbox">
                                            </div><a class="navi-link text-base-color"
                                                href="{{ route('user.forgot') }}">{{ __('Forgot password?') }}</a>
                                        </div>
                                        <div class="register">
                                            <p>{{ __('Not registered ?') }} <a class="text-base-color"
                                                    href="{{ route('user.register') }}">{{ __('Register now') }}</a></p>
                                        </div>
                                    </div>


                                    <div class="text-center">
                                        <button class="btn btn-primary margin-bottom-none w-100"
                                            type="submit"><span>{{ __('Login') }}</span></button>
                                    </div>
                                </div>
                                {{-- middle-Part --}}
                                <div class="or-sign-in-with"><span>Or Sign in with</span></div>
                                {{-- google-sing-in-part --}}
                                <div class="col-md-6">
                                  <div class="d-flex justify-content-center flex-column align-items-center my-3 gap-3">
                                    @if ($setting->facebook_check == 1)
                                        <a class="social-media-login-btn" href="{{ route('social.provider', 'facebook') }}">
                                            <img alt="Facebook" src="/single-vendor-ecommerce/public/storage/images/facebook.png">
                                            <span class="text">{{ __('Facebook login') }}</span>
                                        </a>
                                    @endif
                                    @if ($setting->google_check == 1)
                                        <a class="social-media-login-btn" href="{{ route('social.provider', 'google') }}">
                                            <img alt="Google" src="/single-vendor-ecommerce/public/storage/images/google.png">
                                            <span class="text">{{ __('Google login') }}</span>
                                        </a>
                                    @endif
                                  </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>


            </div>
        </div>
    </div>


    <!-- Site Footer-->
@endsection
