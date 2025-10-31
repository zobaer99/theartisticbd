@extends('master.front')
@section('meta')
    <meta name="keywords" content="{{ $setting->meta_keywords }}">
    <meta name="description" content="{{ $setting->meta_description }}">
@endsection

@section('content')

    @if ($extra_settings->is_t3_slider == 1)
        <div class="hero-area3">
            <div class="background"></div>
            <div class="heroarea-slider owl-carousel">
                @foreach ($sliders as $slider)
                    <div class="item" style="background: url('{{ $slider->photo ? url('/storage/images/' . $slider->photo) : url('/storage/images/placeholder.png') }}')">
                        <div class="container">
                            <div class="row">
                                <div class="col-xl-5 col-lg-6 d-flex align-self-center">
                                    <div class="left-content color-white">
                                        <div class="content">
                                            @if($slider->title)
                                            <div class="layer-1">
                                                <p class="subtitle">
                                                    {{ $slider->title }}
                                                </p>
                                            </div>
                                            @endif
                                            @if($slider->details)
                                            <div class="layer-2">
                                                <h1 class="title">
                                                    {{ $slider->details }}
                                                </h1>
                                            </div>
                                            @endif
                                            @if ($slider->link && $slider->link != '#')
                                                <div class="layer-3">
                                                    <div class="links">
                                                        <a href="{{ $slider->link }}" class="btn btn-primary">
                                                            <span>
                                                                {{ __('Buy Now') }}
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-7 col-lg-6 order-first order-lg-last">
                                    <div class="layer-4">
                                        <div class="right-img">
                                            @if($slider->logo)
                                            <img class="img-fluid full-img"
                                                src="{{ url('/storage/images/' . $slider->logo) }}" alt="">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif



    {{-- @if ($setting->is_slider == 1)
        <div class="slider-area-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Main Slider-->
                        <div class="hero-slider">
                            <div class="hero-slider-main owl-carousel dots-inside">
                                @foreach ($sliders as $slider)
                                    <div class="item
                                    @if ($default_language->rtl == 1) d-flex justify-content-end @endif
                                    "
                                        style="background: url('{{ url('/storage/images/' . $slider->photo) }}')">
                                        <div class="item-inner">
                                            <div class="from-bottom">
                                                @if ($slider->logo)
                                                    <img class="d-inline-block brand-logo"
                                                        src="{{ url('/storage/images/' . $slider->logo) }}" alt="logo">
                                                @endif
                                                <div class="title text-body">{{ $slider->title }}</div>
                                                <div class="subtitle text-body">{{ $slider->details }}</div>
                                            </div>
                                            @if ($slider->link != '#')
                                                <a class="btn btn-primary scale-up delay-1" href="{{ $slider->link }}">
                                                    <span>{{ __('Buy Now') }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if (isset($hero_banner))
                        <div class="col-lg-4 d-none d-lg-block">
                            <a href="{{ $hero_banner['url1'] }}" class="sright-image">
                                <img src="{{ url('/storage/images/' . $hero_banner['img1']) }}" alt="">
                                <div class="inner-content">

                                    @if (isset($hero_banner['subtitle1']))
                                        <p>{{ $hero_banner['subtitle1'] }}</p>
                                    @endif

                                    @if (isset($hero_banner['title1']))
                                        <h4>{{ $hero_banner['title1'] }}</h4>
                                    @endif
                                </div>
                            </a>
                            <a href="{{ $hero_banner['url2'] }}" class="sright-image mb-0">
                                <img src="{{ url('/storage/images/' . $hero_banner['img2']) }}" alt="">
                                <div class="inner-content">
                                    @if (isset($hero_banner['subtitle2']))
                                        <p>{{ $hero_banner['subtitle2'] }}</p>
                                    @endif
                                    @if (isset($hero_banner['title2']))
                                        <h4>{{ $hero_banner['title2'] }}</h4>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    @endif --}}


    {{-- category cards --}}

    <section class="service-section py-4">
        <div class="container">
            <div class="row g-2 g-md-3">
                @foreach ($latest_catgories as $category)
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="card h-100 category-card">
                            <a href="{{ route('front.catalog') . '?category=' . $category->slug }}" class="text-decoration-none">
                                <img class="card-img-top category-image lazy" alt="{{ $category->name }}"
                                    src="{{ url('/storage/images/' . $category->photo) }}" />
                                <div class="card-body text-center">
                                    <h6 class="card-title category-title mb-0">
                                        {{ $category->name }}
                                    </h6>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <style>
        .category-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            transition: all 0.3s ease;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .category-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-color: #667eea;
        }
        
        .category-image {
            width: 100%;
            height: 120px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .category-card:hover .category-image {
            transform: scale(1.02);
        }
        
        .category-card .card-body {
            padding: 15px 12px;
            background: #fff;
        }
        
        .category-title {
            font-size: 14px;
            font-weight: 500;
            color: #333;
            line-height: 1.3;
            transition: color 0.3s ease;
        }
        
        .category-card:hover .category-title {
            color: #12a14e;
        }
        
        /* Mobile specific styles */
        @media (max-width: 576px) {
            .service-section {
                padding: 15px 0;
            }
            
            .category-card {
                border-radius: 6px;
            }
            
            .category-image {
                height: 100px;
            }
            
            .category-card .card-body {
                padding: 12px 8px;
            }
            
            .category-title {
                font-size: 12px;
            }
            

        
        /* Tablet styles */
        @media (min-width: 577px) and (max-width: 991px) {
            .category-image {
                height: 130px;
            }
            
            .category-card .card-body {
                padding: 15px 10px;
            }
            
            .category-title {
                font-size: 13px;
            }
        }
        
        /* Desktop styles */
        @media (min-width: 992px) {
            .category-image {
                height: 140px;
            }
            
            .category-card .card-body {
                padding: 18px 15px;
            }
            
            .category-title {
                font-size: 15px;
            }
        }
    </style>



    @if ($setting->campaign_status == 1)
        <div class="deal-of-day-section mt-20">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <h2 class="h3">{{ $setting->campaign_title }}</h2>
                            <div class="right-area ">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3">

                    <div class="col-lg-3">
                        <div class="right-area">
                            <div class="countdown dtw-countdown countdown-alt"
                                data-date-time="{{ $setting->campaign_end_date }}">
                            </div>
                            <a class="right_link btn btn-primary mt-2" href="{{ route('front.campaign') }}">
                                <span>{{ __('View All') }} <i class="icon-chevron-right"></i></span></a>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="popular-category-slider owl-carousel">
                            @foreach ($campaign_items as $compaign_item)
                                <div class="slider-item">
                                    <div class="product-card">
                                        <div class="product-thumb">
                                            @if (!$compaign_item->item->is_stock())
                                                <div
                                                    class="product-badge bg-secondary border-default text-body
                                        ">
                                                    {{ __('out of stock') }}</div>
                                            @endif

                                            @if ($compaign_item->item->previous_price && $compaign_item->item->previous_price != 0)
                                                <div class="product-badge product-badge2 bg-info">
                                                    -{{ PriceHelper::DiscountPercentage($compaign_item->item) }}</div>
                                            @endif
                                            <a href="{{ route('front.product', $compaign_item->item->slug) }}">
                                            <img class="lazy for_height"
                                                data-src="{{ url('/storage/images/' . $compaign_item->item->thumbnail) }}"
                                                alt="Product"> </a>
                                            <div class="product-button-group"><a class="product-button wishlist_store"
                                                    href="{{ route('user.wishlist.store', $compaign_item->item->id) }}"
                                                    title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                <a data-target="{{ route('fornt.compare.product', $compaign_item->item->id) }}"
                                                    class="product-button product_compare" href="javascript:;"
                                                    title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                                @if ($compaign_item->item->is_stock())
                                                    <a class="product-button add_to_single_cart"
                                                        data-target="{{ $compaign_item->item->id }}" href="javascript:;"
                                                        title="{{ __('To Cart') }}"><i class="icon-shopping-cart"></i>
                                                    </a>
                                                @else
                                                    <a class="product-button"
                                                        href="{{ route('front.product', $compaign_item->item->slug) }}"
                                                        title="{{ __('Details') }}"><i class="icon-arrow-right"></i></a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="product-card-body">

                                            <div class="product-category"><a
                                                    href="{{ route('front.catalog') . '?category=' . $compaign_item->item->category->slug }}">{{ $compaign_item->item->category->name }}</a>
                                            </div>
                                            <h3 class="product-title"><a
                                                    href="{{ route('front.product', $compaign_item->item->slug) }}">
                                                    {{ Str::limit($compaign_item->item->name, 35) }}
                                                </a></h3>
                                            <div class="rating-stars">
                                                {!! Helper::renderStarRating($compaign_item->item->reviews->avg('rating')) !!}
                                            </div>
                                            <h4 class="product-price">
                                                @if ($compaign_item->item->previous_price != 0)
                                                    <del>{{ PriceHelper::setPreviousPrice($compaign_item->item->previous_price) }}</del>
                                                @endif

                                                {{ PriceHelper::grandCurrencyPrice($compaign_item->item) }}
                                            </h4>

                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>

                </div>
            </div>
        </div>
    @endif


    @if ($setting->is_three_c_b_first == 1)
        <div class="bannner-section mt-60">
            <div class="container ">
                <div class="row gx-3">
                    <div class="col-md-4">
                        <a href="{{ $banner_first['firsturl1'] }}" class="genius-banner">
                            <img src="{{ url('/storage/images/' . $banner_first['img1']) }}" alt="">
                            <div class="inner-content">
                                @if (isset($banner_first['subtitle1']))
                                    <p>{{ $banner_first['subtitle1'] }}</p>
                                @endif
                                @if (isset($banner_first['title1']))
                                    <h4>{{ $banner_first['title1'] }}</h4>
                                @endif
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ $banner_first['firsturl2'] }}" class="genius-banner">
                            <img src="{{ url('/storage/images/' . $banner_first['img2']) }}" alt="">
                            <div class="inner-content">
                                @if (isset($banner_first['subtitle2']))
                                    <p>{{ $banner_first['subtitle2'] }}</p>
                                @endif
                                @if (isset($banner_first['title2']))
                                    <h4>{{ $banner_first['title2'] }}</h4>
                                @endif
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ $banner_first['firsturl3'] }}" class="genius-banner">
                            <img src="{{ url('/storage/images/' . $banner_first['img3']) }}" alt="">
                            <div class="inner-content">
                                @if (isset($banner_first['subtitle3']))
                                    <p>{{ $banner_first['subtitle3'] }} </p>
                                @endif
                                @if (isset($banner_first['title3']))
                                    <h4>{{ $banner_first['title3'] }}</h4>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($setting->is_highlighted == 1)
        <section class="selected-product-section mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            {{-- <div class="links"> 
                                <a data-href="{{ route('front.get.product', 'new') }}" data-target="type_product_view"
                                    class="active" href="javascript:;">{{ __('New Arrival') }}</a>
                            </div> --}}
                            <h2 class="h3">{{ __('New Arrival') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="type_product_view d-none">
                        <img src="{{ url('/storage/images/ajax_loader.gif') }}" alt="">
                    </div>
                    <div class="col-lg-12" id="type_product_view">

                        <div class="features-slider  owl-carousel">
                            @foreach ($new_products as $item)
                                <div class="slider-item">
                                        <div class="product-card ">
                                            <div class="product-thumb">
                                                @if (!$item->is_stock())
                                                    <div
                                                        class="product-badge bg-secondary border-default text-body
                                                    ">
                                                        {{ __('out of stock') }}</div>
                                                @endif
                                                @if ($item->previous_price && $item->previous_price != 0)
                                                    <div class="product-badge product-badge2 bg-info">
                                                        -{{ PriceHelper::DiscountPercentage($item) }}</div>
                                                @endif
                                                <a href="{{ route('front.product', $item->slug) }}">
                                                    <img class="lazy for_height"
                                                        data-src="{{ url('/storage/images/' . $item->thumbnail) }}"
                                                        alt="Product">
                                                </a>
                                                <div class="product-button-group"><a class="product-button wishlist_store"
                                                        href="{{ route('user.wishlist.store', $item->id) }}"
                                                        title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                    <a data-target="{{ route('fornt.compare.product', $item->id) }}"
                                                        class="product-button product_compare" href="javascript:;"
                                                        title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                                    @include('includes.item_footer', ['sitem' => $item])
                                                </div>
                                            </div>
                                            <div class="product-card-inner">
                                                <div class="product-card-body">
                                                    <div class="product-category"><a
                                                            href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                                    </div>
                                                    <h3 class="product-title"><a
                                                            href="{{ route('front.product', $item->slug) }}">
                                                            {{ Str::limit($item->name, 35) }}
                                                        </a></h3>
                                                    <div class="rating-stars">
                                                        {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                                    </div>
                                                    <h4 class="product-price">
                                                        @if ($item->previous_price != 0)
                                                            <del>{{ PriceHelper::setPreviousPrice($item->previous_price) }}</del>
                                                        @endif
                                                        {{ PriceHelper::grandCurrencyPrice($item) }}
                                                    </h4>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </section>
    @endif
    @if ($setting->is_highlighted == 1)
        <section class="selected-product-section mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            {{-- <div class="links">
                                <a data-href="{{ route('front.get.product', 'feature') }}"
                                    data-target="type_product_view" href="javascript:;"
                                    class="active">{{ __('Featured') }}</a>
                            </div> --}}
                            <h2 class="h3">{{ __('Featured') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="type_product_view d-none">
                        <img src="{{ url('/storage/images/ajax_loader.gif') }}" alt="">
                    </div>
                    <div class="col-lg-12" id="type_product_view">

                        <div class="features-slider  owl-carousel">
                            @foreach ($feature_products as $item)
                                    <div class="slider-item">
                                        <div class="product-card ">
                                            <div class="product-thumb">
                                                @if (!$item->is_stock())
                                                    <div
                                                        class="product-badge bg-secondary border-default text-body
                                                    ">
                                                        {{ __('out of stock') }}</div>
                                                @endif
                                                @if ($item->previous_price && $item->previous_price != 0)
                                                    <div class="product-badge product-badge2 bg-info">
                                                        -{{ PriceHelper::DiscountPercentage($item) }}</div>
                                                @endif
                                                <a href="{{ route('front.product', $item->slug) }}">
                                                    <img class="lazy for_height"
                                                        data-src="{{ url('/storage/images/' . $item->thumbnail) }}"
                                                        alt="Product">
                                                </a>
                                                <div class="product-button-group"><a class="product-button wishlist_store"
                                                        href="{{ route('user.wishlist.store', $item->id) }}"
                                                        title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                    <a data-target="{{ route('fornt.compare.product', $item->id) }}"
                                                        class="product-button product_compare" href="javascript:;"
                                                        title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                                    @include('includes.item_footer', ['sitem' => $item])
                                                </div>
                                            </div>
                                            <div class="product-card-inner">
                                                <div class="product-card-body">
                                                    <div class="product-category"><a
                                                            href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                                    </div>
                                                    <h3 class="product-title"><a
                                                            href="{{ route('front.product', $item->slug) }}">
                                                            {{ Str::limit($item->name, 35) }}
                                                        </a></h3>
                                                    <div class="rating-stars">
                                                        {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                                    </div>
                                                    <h4 class="product-price">
                                                        @if ($item->previous_price != 0)
                                                            <del>{{ PriceHelper::setPreviousPrice($item->previous_price) }}</del>
                                                        @endif
                                                        {{ PriceHelper::grandCurrencyPrice($item) }}
                                                    </h4>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </section>
    @endif
    @if ($setting->is_highlighted == 1)
        <section class="selected-product-section mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            {{-- <div class="links"> 
                                <a data-href="{{ route('front.get.product', 'top') }}" data-target="type_product_view"
                                    class="active" href="javascript:;">{{ __('Top Rated') }}</a> 
                            </div> --}}
                            <h2 class="h3">{{ __('Top Rated') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="type_product_view d-none">
                        <img src="{{ url('/storage/images/ajax_loader.gif') }}" alt="">
                    </div>
                    <div class="col-lg-12" id="type_product_view">

                        <div class="features-slider  owl-carousel">
                            @foreach ($top_products as $item)
                                    <div class="slider-item">
                                        <div class="product-card ">
                                            <div class="product-thumb">
                                                @if (!$item->is_stock())
                                                    <div
                                                        class="product-badge bg-secondary border-default text-body
                                                    ">
                                                        {{ __('out of stock') }}</div>
                                                @endif
                                                @if ($item->previous_price && $item->previous_price != 0)
                                                    <div class="product-badge product-badge2 bg-info">
                                                        -{{ PriceHelper::DiscountPercentage($item) }}</div>
                                                @endif
                                                <a href="{{ route('front.product', $item->slug) }}">
                                                    <img class="lazy for_height"
                                                        data-src="{{ url('/storage/images/' . $item->thumbnail) }}"
                                                        alt="Product">
                                                </a>
                                                <div class="product-button-group"><a class="product-button wishlist_store"
                                                        href="{{ route('user.wishlist.store', $item->id) }}"
                                                        title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                    <a data-target="{{ route('fornt.compare.product', $item->id) }}"
                                                        class="product-button product_compare" href="javascript:;"
                                                        title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                                    @include('includes.item_footer', ['sitem' => $item])
                                                </div>
                                            </div>
                                            <div class="product-card-inner">
                                                <div class="product-card-body">
                                                    <div class="product-category"><a
                                                            href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                                    </div>
                                                    <h3 class="product-title"><a
                                                            href="{{ route('front.product', $item->slug) }}">
                                                            {{ Str::limit($item->name, 35) }}
                                                        </a></h3>
                                                    <div class="rating-stars">
                                                        {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                                    </div>
                                                    <h4 class="product-price">
                                                        @if ($item->previous_price != 0)
                                                            <del>{{ PriceHelper::setPreviousPrice($item->previous_price) }}</del>
                                                        @endif
                                                        {{ PriceHelper::grandCurrencyPrice($item) }}
                                                    </h4>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </section>
    @endif
    @if ($setting->is_highlighted == 1)
        <section class="selected-product-section mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            {{-- <div class="links"> 
                                <a data-href="{{ route('front.get.product', 'best') }}" data-target="type_product_view"
                                    class="active" href="javascript:;">{{ __('Best Seller') }}</a> 
                            </div> --}}
                            <h2 class="h3">{{ __('Best Seller') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="type_product_view d-none">
                        <img src="{{ url('/storage/images/ajax_loader.gif') }}" alt="">
                    </div>
                    <div class="col-lg-12" id="type_product_view">

                        <div class="features-slider  owl-carousel">
                            @foreach ($best_products as $item)
                                    <div class="slider-item">
                                        <div class="product-card ">
                                            <div class="product-thumb">
                                                @if (!$item->is_stock())
                                                    <div
                                                        class="product-badge bg-secondary border-default text-body
                                                    ">
                                                        {{ __('out of stock') }}</div>
                                                @endif
                                                @if ($item->previous_price && $item->previous_price != 0)
                                                    <div class="product-badge product-badge2 bg-info">
                                                        -{{ PriceHelper::DiscountPercentage($item) }}</div>
                                                @endif
                                                <a href="{{ route('front.product', $item->slug) }}">
                                                    <img class="lazy for_height"
                                                        data-src="{{ url('/storage/images/' . $item->thumbnail) }}"
                                                        alt="Product">
                                                </a>
                                                <div class="product-button-group"><a class="product-button wishlist_store"
                                                        href="{{ route('user.wishlist.store', $item->id) }}"
                                                        title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                    <a data-target="{{ route('fornt.compare.product', $item->id) }}"
                                                        class="product-button product_compare" href="javascript:;"
                                                        title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                                    @include('includes.item_footer', ['sitem' => $item])
                                                </div>
                                            </div>
                                            <div class="product-card-inner">
                                                <div class="product-card-body">
                                                    <div class="product-category"><a
                                                            href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                                    </div>
                                                    <h3 class="product-title"><a
                                                            href="{{ route('front.product', $item->slug) }}">
                                                            {{ Str::limit($item->name, 35) }}
                                                        </a></h3>
                                                    <div class="rating-stars">
                                                        {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                                    </div>
                                                    <h4 class="product-price">
                                                        @if ($item->previous_price != 0)
                                                            <del>{{ PriceHelper::setPreviousPrice($item->previous_price) }}</del>
                                                        @endif
                                                        {{ PriceHelper::grandCurrencyPrice($item) }}
                                                    </h4>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </section>
    @endif

    {{-- flash deal --}}
    @if ($extra_settings->is_t1_falsh == 1)
        <div class="flash-sell-new-section mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="section-title">
                            <h2 class="h3">{{ __('Flash Deal') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-content">
                            <div class="flash-deal-slider owl-carousel">
                                @foreach ($flash_deal_products as $item)
                                        <div class="slider-item">
                                            <div class="product-card ">
                                                <div class="product-thumb">
                                                    @if (!$item->is_stock())
                                                        <div class="product-badge bg-secondary border-default text-body">
                                                            {{ __('out of stock') }}</div>
                                                    @endif
                                                    @if ($item->previous_price && $item->previous_price != 0)
                                                        <div class="product-badge product-badge2 bg-info">
                                                            -{{ PriceHelper::DiscountPercentage($item) }}</div>
                                                    @endif
                                                    <a href="{{ route('front.product', $item->slug) }}">
                                                        <img class="lazy for_height"
                                                            data-src="{{ url('/storage/images/' . $item->thumbnail) }}"
                                                            alt="Product">
                                                    </a>
                                                    <div class="product-button-group">
                                                        <a class="product-button wishlist_store"
                                                            href="{{ route('user.wishlist.store', $item->id) }}"
                                                            title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                        <a data-target="{{ route('fornt.compare.product', $item->id) }}"
                                                            class="product-button product_compare" href="javascript:;"
                                                            title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                                        @include('includes.item_footer', [
                                                            'sitem' => $item,
                                                        ])
                                                    </div>
                                                </div>
                                                <div class="product-card-inner">
                                                    <div class="product-card-body">

                                                        <div class="product-category"><a
                                                                href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                                        </div>
                                                        <h3 class="product-title"><a
                                                                href="{{ route('front.product', $item->slug) }}">
                                                                {{ Str::limit($item->name, 50) }}
                                                            </a></h3>
                                                        <div class="rating-stars">
                                                            {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                                        </div>
                                                        <h4 class="product-price">
                                                            @if ($item->previous_price != 0)
                                                                <del>{{ $item->previous_price }}</del>
                                                            @endif

                                                            {{ PriceHelper::grandCurrencyPrice($item) }}
                                                        </h4>
                                                        @if (date('d-m-y') != \Carbon\Carbon::parse($item->date)->format('d-m-y'))
                                                            <div class="countdown countdown-alt mb-3"
                                                                data-date-time="{{ $item->date }}">
                                                            </div>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

   

    {{-- Video Gallery --}}
    <div class="video-section-h page_section mt-50 mb-30">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2 class="h3">Video Gallery</h2>
                    </div>
                </div>
            </div>

            <div class="row g-4 justify-content-center">

                {{-- Main video player left side --}}
                <div class="col-lg-8">
                    <div class="main-video-wrap ratio ratio-16x9">
                        @if ($video_stories->count() > 0)
                            @php
                                function youtubeEmbedUrl($url)
                                {
                                    preg_match(
                                        '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|embed)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i',
                                        $url,
                                        $matches,
                                    );
                                    return isset($matches[1]) ? 'https://www.youtube.com/embed/' . $matches[1] : '';
                                }
                            @endphp
                            <iframe id="main-video-player" class="video"
                                src="{{ youtubeEmbedUrl($video_stories->first()->youtube_url) }}"
                                title="{{ $video_stories->first()->title }}" allowfullscreen>
                            </iframe>
                        @else
                            <p>No videos available</p>
                        @endif
                    </div>
                </div>

                {{-- Playlist on right side --}}
                <div class="col-lg-4">
                    <div class="side-video-list">
                        <div class="video-top-title">
                            <h2 class="title">Playlist</h2>
                            <h3 class="total-video">{{ $video_stories->count() }} videos</h3>
                        </div>

                        <div class="side-video-wrap">
                            <div class="single-video">
                                  @if ($video_stories->count() > 0)
                                @php
                                    function youtubeVideoId($url)
                                    {
                                        preg_match(
                                            '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|embed)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i',
                                            $url,
                                            $matches,
                                        );
                                        return $matches[1] ?? '';
                                    }
                                @endphp

                                @foreach ($video_stories as $video)
                                    <a href="javascript:void(0);" class="single-video-list"
                                        data-embed-url="{{ youtubeEmbedUrl($video->youtube_url) }}"
                                        data-title="{{ $video->title }}">
                                        <div class="empty">
                                            <div class="img-wrap">
                                                <img class="video-thumbnail"
                                                    src="https://img.youtube.com/vi/{{ youtubeVideoId($video->youtube_url) }}/hqdefault.jpg"
                                                    alt="Thumbnail">
                                                <span class="play-icon">
                                                    <i class="fas fa-play-circle"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <h3 class="video-title">{{ $video->title }}</h3>
                                        {{-- <p class="total-duration">{{ $video->duration ?? '00:00' }}</p> --}}
                                    </a>
                                @endforeach
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>



    {{-- @if ($setting->is_blogs == 1)
        <div class="blog-section-h page_section mt-50 mb-30">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <h2 class="h3">{{ __('Our Blog') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="home-blog-slider owl-carousel">
                            @foreach ($posts as $post)
                                <div class="slider-item">
                                    <a href="{{ route('front.blog.details', $post->slug) }}" class="blog-post">
                                        <div class="post-thumb">
                                            <img class="lazy"
                                                data-src="{{ url('/storage/images/' . json_decode($post->photo, true)[array_key_first(json_decode($post->photo, true))]) }}"
                                                alt="Blog Post">
                                        </div>
                                        <div class="post-body">

                                            <h3 class="post-title"> {{ Str::limit($post->title, 55) }}
                                            </h3>
                                            <ul class="post-meta">

                                                <li><i class="icon-user"></i>{{ __('Admin') }}</li>
                                                <li><i
                                                        class="icon-clock"></i>{{ date('jS F, Y', strtotime($post->created_at)) }}
                                                </li>
                                            </ul>
                                            <p>{{ Str::limit(strip_tags($post->details), 120) }}
                                            </p>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif --}}

    @if ($setting->is_popular_brand == 1)
        <section class="brand-section mt-30 mb-60">
            <div class="container ">
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="section-title">
                            <h2 class="h3">{{ __('Popular Brands') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="brand-slider owl-carousel">
                            @foreach ($brands as $brand)
                                <div class="slider-item">
                                    <a class="text-center"
                                        href="{{ route('front.catalog') . '?brand=' . $brand->slug }}">
                                        <img class="d-block hi-50 lazy"
                                            data-src="{{ url('/storage/images/' . $brand->photo) }}"
                                            alt="{{ $brand->name }}" title="{{ $brand->name }}">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const videoLinks = document.querySelectorAll('.single-video-list');
            const mainPlayer = document.getElementById('main-video-player');

            videoLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    const newUrl = this.getAttribute('data-embed-url');
                    const newTitle = this.getAttribute('data-title');

                    if (newUrl && mainPlayer) {
                        mainPlayer.src = newUrl;
                        mainPlayer.title = newTitle;
                    }

                    // Optional: highlight selected video
                    videoLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>

    <script>
        // Initialize heroarea slider responsively if owlCarousel is available
        (function(){
            if (typeof jQuery === 'undefined' || !jQuery().owlCarousel) return;
            var $ = jQuery;
            $(function(){
                var $hero = $('.heroarea-slider');
                if ($hero.length && !$hero.hasClass('owl-loaded')){
                    $hero.owlCarousel({
                        items: 1,
                        loop: true,
                        autoplay: true,
                        autoplayTimeout: 6000,
                        autoplayHoverPause: true,
                        nav: true,
                        dots: true,
                        navText: ['<i class="fas fa-chevron-left"></i>','<i class="fas fa-chevron-right"></i>'],
                        responsive: {
                            0: { items:1 },
                            576: { items:1 },
                            768: { items:1 },
                            992: { items:1 }
                        }
                    });
                }
            });
        })();
    </script>

    <style>
        /* Make hero area background image responsive and content stack on small screens */
        .hero-area3 .heroarea-slider .item {
            background-size: cover;
            background-position: center center;
            display: flex;
            align-items: center;
            background-repeat: no-repeat;
            position: relative; /* allow absolute child positioning for mobile aspect-ratio trick */
        }

        /* Keep normal flow content sizing on desktop/tablet */
        .hero-area3 .left-content .title { font-size: 28px; }
        .hero-area3 .left-content .subtitle { font-size: 16px; }
        .hero-area3 .right-img .full-img { max-width: 100%; height: auto; display:block; margin: 0 auto; }

        @media (max-width: 991px){
            .hero-area3 .col-xl-5, .hero-area3 .col-lg-6, .hero-area3 .col-xl-7 { flex: 0 0 100%; max-width: 100%; }
            .hero-area3 .left-content { text-align: center; padding: 20px 0; }
            .hero-area3 .left-content .title { font-size: 20px; }
            .hero-area3 .right-img { margin-top: 15px; }
            .hero-area3 .heroarea-slider .item { min-height: 260px; }
        }

        /* Mobile: show full banner scaled (no cropping) and reduce vertical gap
           Use padding-top percentage to create a compact, predictable aspect area
           and absolutely position the inner .container so content centers within that box. */
        @media (max-width: 576px) {
            .hero-area3 .heroarea-slider .item {
                background-size: contain !important;
                background-position: center center !important; /* center vertically to avoid empty space */
                /* remove rigid min-height and use aspect-ratio like padding to show full banner */
                min-height: 0 !important;
                height: auto !important; /* override compiled stylesheet fixed height */
                max-height: none !important;
                padding-top: 14% !important; /* reduced visible height to remove big gap */
                padding-bottom: 0 !important;
                overflow: hidden; /* prevent any internal content from pushing the slide taller */
            }

            /* make the inside content overlay the padded area so it remains vertically centered */
            .hero-area3 .heroarea-slider .item > .container {
                position: absolute !important;
                top: 0; left: 0; right: 0; bottom: 0;
                height: 100%;
                display: flex;
                align-items: center;
                padding-top: 0 !important; /* avoid extra inner padding */
            }
            /* also clear theme fixed heights on hero-area3 variants */
            .hero-area3 .item { height: auto !important; max-height: none !important; }
            .hero-area3.hero-area4 .item { height: auto !important; max-height: none !important; }

            /* Limit right-side image so it won't expand the slide height */
            .hero-area3 .right-img .full-img { max-height: 120px; width: auto; display:block; margin:0 auto; }
            .hero-area3 .left-content { padding: 6px 0; }
            .hero-area3 .left-content .title { font-size: 15px; line-height:1.1; }
            .hero-area3 .left-content .subtitle { font-size: 12px; }
            .hero-area3 .container { padding-left: 8px; padding-right: 8px; }
            .hero-area3 .heroarea-slider .owl-dots{ margin-top:6px; }
            /* reduce dot container impact */
            .hero-area3 .heroarea-slider .owl-dots .owl-dot { height:8px; width:8px; }
            /* remove any extra spacing around the hero wrapper on mobile */
            .hero-area3 { padding-top: 0 !important; padding-bottom: 0 !important; margin-top: 0 !important; margin-bottom: 0 !important; }
            .hero-area3 .heroarea-slider { margin-top: 0 !important; margin-bottom: 0 !important; }
            .hero-area3 .background { display: none !important; }
        }

        @media (min-width: 992px){
            .hero-area3 .left-content .title { font-size: 34px; }
        }
    </style>

    <style>
        /* Optional highlight style */
        .single-video-list.active {
            border: 2px solid #007bff;
            background-color: #f0f8ff;
        }
    </style>

    <!-- Strong mobile box override: force fixed width & height on small devices -->
    <style>
        /* Fixed mobile hero box: centered, prevents full-bleed and large blank area */
        @media (max-width: 576px) {
            /* Outer wrapper boxed */
            .hero-area3 {
                max-width: 360px !important;
                margin-left: auto !important;
                margin-right: auto !important;
                width: 100% !important;
            }

            /* Slider and items constrained to the boxed width */
            .hero-area3 .heroarea-slider,
            .hero-area3 .heroarea-slider .item {
                max-width: 360px !important;
                width: 100% !important;
                margin-left: auto !important;
                margin-right: auto !important;
                box-sizing: border-box !important;
            }

            /* Force a fixed height so the slide is predictable on mobile */
            .hero-area3 .heroarea-slider .item {
                height: 180px !important; /* change this value if you want taller/shorter */
                padding-top: 0 !important; /* remove previous aspect trick */
                background-size: cover !important;
                background-position: center center !important;
                overflow: hidden !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Make inner content align vertically inside the fixed box */
            .hero-area3 .heroarea-slider .item > .container {
                position: relative !important;
                height: 100% !important;
                display: flex !important;
                align-items: center !important;
                padding-left: 0% !important;
                padding-right: 0px !important;
            }

            /* Ensure right-side image won't push height */
            .hero-area3 .right-img .full-img {
                max-height: 120px !important;
                width: auto !important;
                display: block !important;
                margin: 0 auto !important;
            }

            /* Tidy spacing */
            .hero-area3 { padding-top: 0 !important; padding-bottom: 0 !important; }
            .hero-area3 .background { display: none !important; }
        }

        /* Very narrow phones: allow slight shrink so content never overflows viewport */
        @media (max-width: 360px) {
            .hero-area3,
            .hero-area3 .heroarea-slider,
            .hero-area3 .heroarea-slider .item {
                max-width: calc(100% - 24px) !important;
                width: calc(100% - 24px) !important;
            }
        }
    </style>

@endsection
