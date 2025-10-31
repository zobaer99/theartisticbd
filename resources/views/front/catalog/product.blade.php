@extends('master.front')

@section('title')
    {{ $item->name }}
@endsection

@section('meta')
    <meta name="tile" content="{{ $item->title }}">
    <meta name="keywords" content="{{ $item->meta_keywords }}">
    <meta name="description" content="{{ $item->meta_description }}">

    <meta name="twitter:title" content="{{ $item->title }}">
    <meta name="twitter:image" content="{{ url('/storage/images/' . $item->photo) }}">
    <meta name="twitter:description" content="{{ $item->meta_description }}">

    <meta name="og:title" content="{{ $item->title }}">
    <meta name="og:image" content="{{ url('/storage/images/' . $item->photo) }}">
    <meta name="og:description" content="{{ $item->meta_description }}">
@endsection

@section('css')
<style>
/* Attribute Radio Button Styling */
.attribute-title {
    font-weight: bold !important;
    margin-bottom: 10px !important;
    display: block !important;
    color: #333;
    font-size: 14px;
}

.form-check {
    margin-bottom: 5px !important;
    display: flex !important;
    align-items: center !important;
}

.form-check-input {
    margin-right: 8px !important;
    margin-top: 0 !important;
}

.form-check-label {
    margin-left: 5px !important;
    cursor: pointer !important;
    font-size: 13px !important;
    line-height: 1.4 !important;
}

.form-check-label:hover {
    color: #007bff !important;
}

.form-check-input:checked + .form-check-label {
    font-weight: 600 !important;
    color: #007bff !important;
}

/* Color attribute special styling */
.form-check-input[data-attribute-name="color"]:checked + .form-check-label,
.form-check-input[data-attribute-name="colour"]:checked + .form-check-label {
    background-color: #e3f2fd;
    /* padding: 2px 8px; */
    /* border-radius: 15px; */
    transition: all 0.3s ease;
}

/* Price addition styling */
.text-muted {
    font-size: 11px !important;
    font-weight: normal !important;
}

/* Attribute section spacing */
.margin-top-1x .form-group {
    margin-bottom: 20px !important;
}

/* ===== NEW BUTTON STYLE ATTRIBUTES ===== */

/* Title styling */
.title-lable {
    font-size: 16px !important;
    font-weight: 600 !important;
    color: #333 !important;
    margin-bottom: 15px !important;
    text-transform: capitalize;
}

/* Color Options Styling - Updated for clean display */
.color-options-container {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 10px !important;
    margin-bottom: 15px !important;
}

.color-option, .color-option-item {
    margin: 0 !important;
    position: relative !important;
    cursor: pointer !important;
    border: 2px solid #ddd !important;
    /* border-radius: 8px !important; */
    /* padding: 4px !important; */
    background: #fff !important;
    transition: all 0.3s ease !important;
    display: inline-flex !important;
}

.color-option-item.selected {
    border-color: #007bff !important;
    box-shadow: 0 4px 12px rgba(0,123,255,0.25) !important;
    background: #fff !important;
}

.color-option input[type="radio"],
.color-radio {
    width: 18px;
    height: 18px;
    margin-right: 10px;
    border-radius: 50%;
    border: 2px solid #ccc;
    display: inline-block;
    position: relative;
}

.color-option-item.selected .color-radio {
    border-color: #0ea45b;
    background: #0ea45b;
}

.color-option-button {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 70px;
    text-align: center;
}

.color-option-button:hover {
    transform: translateY(-2px);
}

.color-option input:checked + .color-option-button {
    color: #0ea45b;
}

.color-thumbnail {
    width: 50px !important;
    height: 80px !important;
    border-radius: 6px !important;
    overflow: hidden !important;
    border: 1px solid #ddd !important;
    position: relative !important;
}

.color-thumbnail img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    transition: transform 0.3s ease !important;
    border-radius: 5px !important;
}

.color-thumbnail.selected-color {
    border: 2px solid #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.2);
}

.color-thumbnail.selected-color img {
    transform: scale(1.05);
}

.color-option-button.active-color {
    border-color: #007bff !important;
    background-color: #f0f8ff !important;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1) !important;
}

.color-name {
    font-size: 12px;
    font-weight: 500;
    color: #333;
    margin-bottom: 2px;
}

.color-price {
    font-size: 10px;
    color: #666;
}

/* Size Options Styling */
.size-options-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 15px;
}

.size-option {
    margin: 0 !important;
    position: relative;
}

.size-option input[type="radio"] {
    display: none !important;
}

.size-option-button {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 10px 12px;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    background: #fff;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 45px;
    min-height: 45px;
    text-align: center;
    font-weight: 500;
    font-size: 14px;
    color: #333;
}

.size-option-button:hover {
    border-color: #007bff;
    background-color: #f8f9ff;
}

.size-option input:checked + .size-option-button {
    border-color: #007bff;
    background-color: #007bff;
    color: #fff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.3);
}

.size-option-button small {
    font-size: 10px;
    margin-top: 2px;
    opacity: 0.8;
}

/* Other Options Styling */
.other-options-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 15px;
}

.other-option {
    margin: 0 !important;
    position: relative;
}

.other-option input[type="radio"] {
    display: none !important;
}

.other-option-button {
    display: inline-block;
    padding: 8px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 20px;
    background: #fff;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    font-weight: 500;
    font-size: 13px;
    color: #333;
}

.other-option-button:hover {
    border-color: #007bff;
    background-color: #f8f9ff;
}

.other-option input:checked + .other-option-button {
    border-color: #007bff;
    background-color: #007bff;
    color: #fff;
}

.other-option-button small {
    font-size: 11px;
    margin-left: 5px;
    opacity: 0.9;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .color-options-container,
    .size-options-container,
    .other-options-container {
        gap: 6px;
    }
    
    .color-option-button {
        min-width: 60px;
        padding: 6px;
    }
    
    .color-thumbnail {
        width: 35px;
        height: 35px;
    }
    
    .size-option-button {
        min-width: 40px;
        min-height: 40px;
        padding: 8px 10px;
        font-size: 13px;
    }
    
    .title-lable {
        font-size: 15px !important;
        margin-bottom: 12px !important;
    }
}

/* Additional CSS for enhanced attribute items */
.attribute-option-item {
    transition: all 0.3s ease;
    cursor: pointer;
}

.attribute-option-item:hover {
    border-color: #007bff !important;
    background-color: #f8f9fa !important;
}

.attribute-option-item.selected {
    border-color: #007bff !important;
    background-color: #e7f3ff !important;
    box-shadow: 0 2px 6px rgba(0,123,255,0.2);
}

.attribute-option-item.selected .form-check-label {
    color: #007bff;
    font-weight: 600;
}

/* Enhanced Color Selection Styling */
.attribute-option-item[data-attribute-name="color"] {
    /* border-radius: 15px !important; */
    /* padding: 10px !important; */
    display: flex !important;
    align-items: center !important;
    transition: all 0.3s ease !important;
    min-height: 50px !important;
    cursor: pointer !important;
}

.attribute-option-item[data-attribute-name="color"] .form-check-input {
    display: none !important; /* Hide radio buttons for colors */
}

.attribute-option-item[data-attribute-name="color"] .form-check-label {
    display: flex !important;
    align-items: center !important;
    width: 100% !important;
    margin-left: 0 !important;
    cursor: pointer !important;
    /* padding: 5px !important; */
    /* border-radius: 10px !important; */
    transition: all 0.3s ease !important;
}

.attribute-option-item[data-attribute-name="color"] .form-check-label img {
    border: 3px solid transparent !important;
    transition: all 0.3s ease !important;
    flex-shrink: 0 !important;
}

.attribute-option-item[data-attribute-name="color"].selected {
    border-color: #007bff !important;
    background: linear-gradient(135deg, #e7f3ff 0%, #f0f8ff 100%) !important;
    box-shadow: 0 4px 12px rgba(0,123,255,0.15) !important;
    transform: translateY(-2px) !important;
}

.attribute-option-item[data-attribute-name="color"].selected .form-check-label {
    background: rgba(0,123,255,0.1) !important;
    color: #007bff !important;
    font-weight: 700 !important;
}

.attribute-option-item[data-attribute-name="color"].selected .form-check-label img {
    border-color: #007bff !important;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.2) !important;
    transform: scale(1.1) !important;
}

.attribute-option-item[data-attribute-name="color"]:hover .form-check-label {
    background: rgba(0,123,255,0.05) !important;
}

.attribute-option-item[data-attribute-name="color"]:hover .form-check-label img {
    transform: scale(1.05) !important;
    border-color: #007bff !important;
}

/* Size options styling */
.attribute-option-item[data-attribute-name="size"] {
    border-radius: 8px !important;
    text-align: center !important;
    min-height: 45px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
}

.attribute-option-item[data-attribute-name="size"] .form-check-input {
    display: none !important; /* Hide radio buttons for sizes */
}

.attribute-option-item[data-attribute-name="size"] .form-check-label {
    width: 100% !important;
    text-align: center !important;
    margin: 0 !important;
    cursor: pointer !important;
    padding: 8px !important;
    border-radius: 6px !important;
    transition: all 0.3s ease !important;
}

.attribute-option-item[data-attribute-name="size"].selected {
     border-color: #007bff !important;
    background: linear-gradient(135deg, #e7f3ff 0%, #f0f8ff 100%) !important;
    box-shadow: 0 4px 12px rgba(0,123,255,0.15) !important;
    transform: translateY(-2px) !important;
}

.attribute-option-item[data-attribute-name="size"].selected .form-check-label {
    background: rgba(0,123,255,0.1) !important;
    color: #007bff !important;
    font-weight: 700 !important;
    
}

.attribute-option-item[data-attribute-name="size"]:hover {
    background: rgba(0,123,255,0.1) !important;
    border-color: #007bff !important;
}
</style>
@endsection



@section('content')
    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a>
                        </li>
                        <li class="separator"></li>
                        <li><a href="{{ route('front.catalog') }}">{{ __('Shop') }}</a>
                        </li>
                        <li class="separator"></li>
                        <li>{{ $item->name }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Content-->
    <div class="container padding-bottom-1x mb-1">
        <div class="row">
            <!-- Poduct Gallery-->
            <div class="col-xxl-5 col-lg-6 col-md-6">
                <div class="product-gallery">
                    @if ($item->video)
                        <div class="gallery-wrapper">
                            <div class="gallery-item video-btn text-center">
                                <a href="{{ $item->video }}" title="Watch video"></a>
                            </div>
                        </div>
                    @endif
                    @if ($item->is_stock())
                        <span
                            class="product-badge
                        @if ($item->is_type == 'feature') bg-warning
                        @elseif($item->is_type == 'new')
                        bg-success
                        @elseif($item->is_type == 'top')
                        bg-info
                        @elseif($item->is_type == 'best')
                        bg-dark
                        @elseif($item->is_type == 'flash_deal')
                            bg-success @endif
                        ">{{ __($item->is_type != 'undefine' ? ucfirst(str_replace('_', ' ', $item->is_type)) : '') }}</span>
                    @else
                        <span class="product-badge bg-secondary border-default text-body">{{ __('out of stock') }}</span>
                    @endif

                    @if ($item->previous_price && $item->previous_price != 0)
                        <div class="product-badge bg-goldenrod  ppp-t"> -{{ PriceHelper::DiscountPercentage($item) }}</div>
                    @endif

                    <div class="product-thumbnails insize">
                        <div class="product-details-slider owl-carousel">
                            <div class="item"><img id="main_product_image" src="{{ url('/storage/images/' . $item->photo) }}" alt="Product" data-current-color="" />
                            </div>
                            @foreach ($galleries as $key => $gallery)
                                <div class="item"><img src="{{ url('/storage/images/' . $gallery->photo) }}"
                                        alt="zoom" /></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <!-- Product Info-->
            <div class="col-xxl-7 col-lg-6 col-md-6">
                <div class="details-page-top-right-content d-flex align-items-center">
                    <div class="div w-100">
                        <input type="hidden" id="item_id" value="{{ $item->id }}">
                        <input type="hidden" id="demo_price"
                            value="{{ PriceHelper::setConvertPrice($item->discount_price) }}">
                        <input type="hidden" value="{{ PriceHelper::setCurrencySign() }}" id="set_currency">
                        <input type="hidden" value="{{ PriceHelper::setCurrencyValue() }}" id="set_currency_val">
                        <input type="hidden" value="{{ $setting->currency_direction }}" id="currency_direction">
                        <h4 class="mb-2 p-title-main">{{ $item->name }}</h4>
                        <div class="mb-3">
                            <div class="rating-stars d-inline-block gmr-3">
                                {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                            </div>
                            @if ($item->is_stock())
                                <span class="text-success  d-inline-block">{{ __('In Stock') }} <b>({{ $item->stock }}
                                        @lang('items'))</b></span>
                            @else
                                <span class="text-danger  d-inline-block">{{ __('Out of stock') }}</span>
                            @endif
                        </div>


                        @if ($item->is_type == 'flash_deal')
                            @if (date('d-m-y') != \Carbon\Carbon::parse($item->date)->format('d-m-y'))
                                <div class="countdown countdown-alt mb-3" data-date-time="{{ $item->date }}">
                                </div>
                            @endif
                        @endif

                        <span class="h3 d-block price-area">
                            @if ($item->previous_price != 0)
                                <small
                                    class="d-inline-block"><del>{{ PriceHelper::setPreviousPrice($item->previous_price) }}</del></small>
                            @endif
                            <span id="main_price" class="main-price">{{ PriceHelper::grandCurrencyPrice($item) }}</span>
                        </span>

                        <p class="text-muted">{{ $item->sort_details }} <a href="#details"
                                class="scroll-to">{{ __('Read more') }}</a></p>


                            {{-- Restored original attribute display with enhanced styling --}}
                            <div class="row margin-top-1x">
                            @foreach ($attributes as $attribute)
                                @if ($attribute->options->count() != 0)
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="attribute-title" style="font-weight: bold; margin-bottom: 10px; display: block;">{{ $attribute->name }}:</label>
                                           {{-- @dd($attribute->options) --}}
                                            {{-- Enhanced attribute styling based on type --}}
                                            @if (strtolower($attribute->name) == 'color' || strtolower($attribute->name) == 'colour' || strtolower($attribute->name) == 'বডি কালার' || strtolower($attribute->name) == 'color (colour)' || strtolower($attribute->name) == 'color/colour' || strtolower($attribute->name) == 'color / colour' || strtolower($attribute->name) == 'কালার' || strtolower($attribute->name) == 'রং')
                                                {{-- Color Attribute with Image Thumbnails Only --}}
                                                <div class="color-options-container">
                                                    @foreach ($attribute->options->where('stock', '!=', '0') as $option)
                                                        <label class="form-check color-option attribute-option-item" data-attribute-name="color">
                                                            <input class="form-check-input attribute_option color-attribute" type="radio"
                                                                name="attribute_{{ $attribute->id }}"
                                                                id="option_{{ $option->id }}" 
                                                                value="{{ $option->name }}"
                                                                data-type="{{ $attribute->id }}"
                                                                data-href="{{ $option->id }}"
                                                                data-target="{{ PriceHelper::setConvertPrice($option->price) }}"
                                                                data-attribute-name="color"
                                                                data-option-name="{{ $option->name }}"
                                                                data-validation="color"
                                                                required
                                                                {{ $loop->first ? 'checked' : '' }}>
                                                            <div class="color-option-button" for="option_{{ $option->id }}">
                                                                {{-- Color thumbnail image with multiple fallbacks --}}
                                                                <div class="color-thumbnail">
                                                                    @php
                                                                        $colorImagePath = isset($option->color_image) && !empty($option->color_image) 
                                                                            ? url('/storage/images/color_options/' . $option->color_image)
                                                                            : url('/storage/images/' . $item->photo);
                                                                    @endphp
                                                                    <img src="{{ $colorImagePath }}" 
                                                                         alt="{{ $option->name }}"
                                                                         onerror="this.src='{{ url('/storage/images/' . $item->photo) }}'; console.log('Color image fallback');"
                                                                         data-main-image="{{ $colorImagePath }}"
                                                                         data-color="{{ $option->name }}">
                                                                </div>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @elseif (strtolower($attribute->name) == 'size' || strtolower($attribute->name) == 'বডি সাইজ' || strtolower($attribute->name) == 'SIZE' || strtolower($attribute->name) == 'সাইজ' || strtolower($attribute->name) == 'বডি')
                                                {{-- Size Attribute with Button Style --}}
                                                <div class="size-options-container">
                                                    @foreach ($attribute->options->where('stock', '!=', '0') as $option)
                                                        <label class="form-check size-option attribute-option-item" data-attribute-name="size">
                                                            <input class="form-check-input attribute_option size-attribute" type="radio"
                                                                name="attribute_{{ $attribute->id }}"
                                                                id="option_{{ $option->id }}" value="{{ $option->name }}"
                                                                data-type="{{ $attribute->id }}"
                                                                data-href="{{ $option->id }}"
                                                                data-target="{{ PriceHelper::setConvertPrice($option->price) }}"
                                                                data-attribute-name="size"
                                                                data-option-name="{{ $option->name }}"
                                                                data-validation="size"
                                                                required
                                                                {{ $loop->first ? 'checked' : '' }}>
                                                            <div class="size-option-button" for="option_{{ $option->id }}">
                                                                {{ $option->name }}
                                                                @if($option->price > 0)
                                                                    {{-- <small>+{{ PriceHelper::setCurrencyPrice($option->price) }}</small> --}}
                                                                @endif
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @else
                                                {{-- Other Attributes with Default Style --}}
                                                @foreach ($attribute->options->where('stock', '!=', '0') as $option)
                                                    <div class="form-check attribute-option-item" 
                                                         style="margin-bottom: 8px; padding: 8px; border: 1px solid #eee; border-radius: 5px; background: #fff;"
                                                         data-attribute-name="{{ strtolower($attribute->name) }}">
                                                        <input class="form-check-input attribute_option" type="radio"
                                                            name="attribute_{{ $attribute->id }}"
                                                            id="option_{{ $option->id }}" 
                                                            value="{{ $option->name }}"
                                                            data-type="{{ $attribute->id }}"
                                                            data-href="{{ $option->id }}"
                                                            data-target="{{ PriceHelper::setConvertPrice($option->price) }}"
                                                            data-attribute-name="{{ strtolower($attribute->name) }}"
                                                            data-option-name="{{ $option->name }}"
                                                            {{ $loop->first ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="option_{{ $option->id }}" style="margin-left: 5px; padding: 8px; cursor: pointer; display: flex; align-items: center;">
                                                            <span>{{ $attribute->name }}: {{ $option->name }}</span>
                                                            @if($option->price > 0)
                                                                <span class="text-muted" style="margin-left: 8px;">(+{{ PriceHelper::setCurrencyPrice($option->price) }})</span>
                                                            @endif
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        
                        
                        {{-- Enhanced Button Style Attributes - Temporarily Hidden --}}
                        <div class="row margin-top-1x enhanced-attributes" style="display: none;">
                            @foreach ($attributes as $attribute)
                                @if ($attribute->options->count() != 0)
                                    <div class="col-sm-12">
                                        <div class="form-group product-dt-form-group">
                                            <h3 class="d-block title-lable attribute-title">{{ $attribute->name }}:</h3>
                                            
                                            {{-- Color Attribute with Image Thumbnails --}}
                                            @if (strtolower($attribute->name) == 'color' || strtolower($attribute->name) == 'colour')
                                                <div class="color-options-container">
                                                    @foreach ($attribute->options->where('stock', '!=', '0') as $option)
                                                        <label class="form-check color-option color-option-item" data-color-option="{{ $option->id }}" data-color-name="{{ strtolower($option->name) }}">
                                                            <input class="form-check-input attribute_option color-attribute color-radio" type="radio"
                                                                name="attribute_{{ $attribute->id }}" required
                                                                id="option_{{ $option->id }}" value="{{ $option->name }}"
                                                                data-type="{{ $attribute->id }}"
                                                                data-href="{{ $option->id }}"
                                                                data-target="{{ PriceHelper::setConvertPrice($option->price) }}"
                                                                data-attribute-name="color"
                                                                data-option-name="{{ strtolower($option->name) }}"
                                                                {{ $loop->first ? 'checked' : '' }}>
                                                            <div class="color-option-button" for="option_{{ $option->id }}">
                                                                {{-- Color thumbnail image with multiple fallbacks --}}
                                                                <div class="color-thumbnail">
                                                                    {{-- Use explicit color_image if present, otherwise fall back to main product photo. Avoid constructing slug-color filenames to prevent 404 errors. --}}
                                                                    @php
                                                                        $colorImageFile = $option->color_image ?? null;
                                                                        $colorImageSrc = $colorImageFile
                                                                            ? url('/storage/images/color_options/' . $colorImageFile)
                                                                            : url('/storage/images/' . $item->photo);
                                                                    @endphp
                                                                    <img src="{{ $colorImageSrc }}"
                                                                         alt="{{ $option->name }}"
                                                                         data-main-image="{{ $colorImageSrc }}"
                                                                         data-color="{{ strtolower($option->name) }}">
                                                                </div>
                                                                <span class="color-name">{{ $option->name }}</span>
                                                                @if($option->price > 0)
                                                                    <span class="color-price">+{{ PriceHelper::setCurrencyPrice($option->price) }}</span>
                                                                @endif
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            
                                            {{-- Size Attribute with Button Style --}}
                                            @elseif (strtolower($attribute->name) == 'size')
                                                <div class="size-options-container">
                                                    @foreach ($attribute->options->where('stock', '!=', '0') as $option)
                                                        <label class="form-check size-option">
                                                            <input class="form-check-input attribute_option size-attribute" type="radio"
                                                                name="attribute_{{ $attribute->id }}"
                                                                id="option_{{ $option->id }}" value="{{ $option->name }}"
                                                                data-type="{{ $attribute->id }}"
                                                                data-href="{{ $option->id }}" required
                                                                data-target="{{ PriceHelper::setConvertPrice($option->price) }}"
                                                                data-attribute-name="size"
                                                                data-option-name="{{ $option->name }}"
                                                                {{ $loop->first ? 'checked' : '' }}>
                                                            <div class="size-option-button" for="option_{{ $option->id }}">
                                                                {{ $option->name }}
                                                                @if($option->price > 0)
                                                                    <small>+{{ PriceHelper::setCurrencyPrice($option->price) }}</small>
                                                                @endif
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            
                                            {{-- Other Attributes with Default Button Style --}}
                                            @else
                                                <div class="other-options-container">
                                                    @foreach ($attribute->options->where('stock', '!=', '0') as $option)
                                                        <label class="form-check other-option">
                                                            <input class="form-check-input attribute_option" type="radio"
                                                                name="attribute_{{ $attribute->id }}"
                                                                id="option_{{ $option->id }}" value="{{ $option->name }}"
                                                                data-type="{{ $attribute->id }}"
                                                                data-href="{{ $option->id }}"
                                                                data-target="{{ PriceHelper::setConvertPrice($option->price) }}"
                                                                data-attribute-name="{{ strtolower($attribute->name) }}"
                                                                data-option-name="{{ $option->name }}"
                                                                {{ $loop->first ? 'checked' : '' }}>
                                                            <div class="other-option-button" for="option_{{ $option->id }}">
                                                                {{ $option->name }}
                                                                @if($option->price > 0)
                                                                    <small>+{{ PriceHelper::setCurrencyPrice($option->price) }}</small>
                                                                @endif
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="row align-items-end pb-4">
                            <div class="col-sm-12">
                                @if ($item->item_type == 'normal')
                                    <div class="qtySelector product-quantity">
                                        <span class="decreaseQty subclick"><i class="fas fa-minus "></i></span>
                                        <input type="text" class="qtyValue cart-amount" value="1">
                                        <span class="increaseQty addclick"><i class="fas fa-plus"></i></span>
                                        <input type="hidden" value="3333" id="current_stock">
                                    </div>
                                @endif
                                <div class="p-action-button">
                                    @if ($item->item_type != 'affiliate')
                                        @if ($item->is_stock())
                                            <button class="btn btn-primary m-0 a-t-c-mr" id="add_to_cart"><i
                                                    class="icon-bag"></i><span>{{ __('অ্যাড টু কার্ট') }}</span></button>
                                            <button class="btn btn-primary m-0" id="but_to_cart"><i
                                                    class="icon-bag"></i><span>{{ __('কিনুন এখনি') }}</span></button>
                                        @else
                                            <button class="btn btn-primary m-0"><i
                                                    class="icon-bag"></i><span>{{ __('Out of stock') }}</span></button>
                                        @endif
                                    @else
                                        <a href="{{ $item->affiliate_link }}" target="_blank"
                                            class="btn btn-primary m-0"><span><i
                                                    class="icon-bag"></i>{{ __('Buy Now') }}</span></a>
                                    @endif

                                </div>

                            </div>
                        </div>

                        <div class="div">
                            <div class="t-c-b-area">
                                @if ($item->brand_id)
                                    <div class="pt-1 mb-1"><span class="text-medium">{{ __('Brand') }}:</span>
                                        <a
                                            href="{{ route('front.catalog') . '?brand=' . $item->brand->slug }}">{{ $item->brand->name }}</a>
                                    </div>
                                @endif

                                <div class="pt-1 mb-1"><span class="text-medium">{{ __('Categories') }}:</span>
                                    <a class="ct-item"
                                        href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                    @if ($item->subcategory->name)
                                        /
                                    @endif
                                    <a class="ct-item"
                                        href="{{ route('front.catalog') . '?subcategory=' . $item->subcategory->slug }}">{{ $item->subcategory->name }}</a>
                                    @if ($item->childcategory->name)
                                        /
                                    @endif
                                    <a class="ct-item"
                                        href="{{ route('front.catalog') . '?childcategory=' . $item->childcategory->slug }}">{{ $item->childcategory->name }}</a>
                                </div>
                                <div class="pt-1 mb-1"><span class="text-medium">{{ __('Tags') }}:</span>
                                    @if ($item->tags)
                                        @foreach (explode(',', $item->tags) as $tag)
                                            @if ($loop->last)
                                                <a
                                                    href="{{ route('front.catalog') . '?tag=' . $tag }}">{{ $tag }}</a>
                                            @else
                                                <a
                                                    href="{{ route('front.catalog') . '?tag=' . $tag }}">{{ $tag }}</a>,
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                                @if ($item->item_type == 'normal')
                                    <div class="pt-1 mb-4"><span class="text-medium">{{ __('SKU') }}:</span>
                                        #{{ $item->sku }}</div>
                                @endif
                            </div>

                            <div class="mt-4 p-d-f-area">
                                <div class="left">
                                    <a class="btn btn-primary btn-sm wishlist_store wishlist_text"
                                        href="{{ route('user.wishlist.store', $item->id) }}"><span><i
                                                class="icon-heart"></i></span>
                                        @if (Auth::check() && in_array($item->id, $userWishlists ?? []))
                                            <span>{{ __('Added To Wishlist') }}</span>
                                        @else
                                            <span class="wishlist1">{{ __('Wishlist') }}</span>
                                            <span class="wishlist2 d-none">{{ __('Added To Wishlist') }}</span>
                                        @endif
                                    </a>
                                    <button class="btn btn-primary btn-sm  product_compare"
                                        data-target="{{ route('fornt.compare.product', $item->id) }}"><span><i
                                                class="icon-repeat"></i>{{ __('Compare') }}</span></button>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="text-muted mr-1">{{ __('Share') }}: </span>
                                    <div class="d-inline-block a2a_kit">
                                        <a class="facebook  a2a_button_facebook" href="">
                                            <span><i class="fab fa-facebook-f"></i></span>
                                        </a>
                                        <a class="twitter  a2a_button_twitter" href="">
                                            <span><i class="fab fa-twitter"></i></span>
                                        </a>
                                        <a class="linkedin  a2a_button_linkedin" href="">
                                            <span><i class="fab fa-linkedin-in"></i></span>
                                        </a>
                                        <a class="pinterest   a2a_button_pinterest" href="">
                                            <span><i class="fab fa-pinterest"></i></span>
                                        </a>
                                    </div>
                                    <script async src="https://static.addtoany.com/menu/page.js"></script>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class=" padding-top-3x mb-3" id="details">
                <div class="col-lg-12">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                data-bs-target="#description" type="button" role="tab" aria-controls="description"
                                aria-selected="true">{{ __('Descriptions') }}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="specification-tab" data-bs-toggle="tab"
                                data-bs-target="#specification" type="button" role="tab"
                                aria-controls="specification" aria-selected="false">{{ __('Specifications') }}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews"
                                type="button" role="tab" aria-controls="reviews"
                                aria-selected="false">{{ __('Latest Reviews') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content card">
                        <div class="tab-pane fade show active" id="description" role="tabpanel"
                            aria-labelledby="description-tab">
                            {!! $item->details !!}
                        </div>
                        <div class="tab-pane fade show" id="specification" role="tabpanel"
                            aria-labelledby="specification-tab">
                            <div class="comparison-table">
                                <table class="table table-bordered">
                                    <thead class="bg-secondary">
                                    </thead>
                                    <tbody>
                                        <tr class="bg-secondary">
                                            <th class="text-uppercase">{{ __('Specifications') }}</th>
                                            <td><span class="text-medium">{{ __('Descriptions') }}</span></td>
                                        </tr>
                                        @if ($sec_name)
                                            @foreach (array_combine($sec_name, $sec_details) as $sname => $sdetail)
                                                <tr>
                                                    <th>{{ $sname }}</th>
                                                    <td>{{ $sdetail }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="text-center">
                                                <td colspan="2">{{ __('No Specifications') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- New Reviews Tab -->
                        <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                            <!-- Reviews-->
                            <div class="review-area">
                                <div class="row">
                                    <div class="col-md-8">
                                        @forelse ($reviews as $review)
                                            <div class="single-review">
                                                <div class="comment">
                                                    <div class="comment-author-ava"><img class="lazy"
                                                            data-src="{{ url('/storage/images/' . $review->user->photo) }}"
                                                            alt="Comment author">
                                                    </div>
                                                    <div class="comment-body">
                                                        <div
                                                            class="comment-header d-flex flex-wrap justify-content-between">
                                                            <div>
                                                                <h4 class="comment-title mb-1">{{ $review->subject }}</h4>
                                                                <span>{{ $review->user->first_name }}</span>
                                                                <span
                                                                    class="ml-3">{{ $review->created_at->format('M d, Y') }}</span>
                                                            </div>
                                                            <div class="mb-2">
                                                                <div class="rating-stars">
                                                                    @php
                                                                        for ($i = 0; $i < $review->rating; $i++) {
                                                                            echo "<i class = 'far fa-star filled'></i>";
                                                                        }
                                                                    @endphp
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="comment-text  mt-2">{{ $review->review }}</p>

                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="card p-5">
                                                {{ __('No Review') }}
                                            </div>
                                        @endforelse
                                        <div class="row mt-15">
                                            <div class="col-lg-12 text-center">
                                                {{ $reviews->links() }}
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="text-center">
                                                    <div class="d-inline align-baseline display-3 mr-1">
                                                        {{ round($item->reviews->avg('rating'), 2) }}</div>
                                                    <div class="d-inline align-baseline text-sm text-warning mr-1">
                                                        <div class="rating-stars">
                                                            {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="pt-3">
                                                    <label class="text-medium text-sm review-stars">
                                                        @foreach ([1, 2, 3, 4, 5] as $star)
                                                            <i class = 'fas fa-star'></i>
                                                        @endforeach <span class="text-muted">-
                                                            {{ $item->reviews->where('status', 1)->where('rating', 5)->count() }}</span>
                                                    </label>
                                                    <div class="progress margin-bottom-1x">
                                                        <div class="progress-bar bg-warning" role="progressbar"
                                                            style="width: {{ $item->reviews->where('status', 1)->where('rating', 5)->sum('rating') * 20 }}%; height: 2px;"
                                                            aria-valuenow="100"
                                                            aria-valuemin="{{ $item->reviews->where('rating', 5)->sum('rating') * 20 }}"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                    <label class="text-medium text-sm review-stars">
                                                        @foreach ([1, 2, 3, 4] as $star)
                                                            <i class = 'fas fa-star'></i>
                                                        @endforeach <span class="text-muted">-
                                                            {{ $item->reviews->where('status', 1)->where('rating', 4)->count() }}</span>
                                                    </label>
                                                    <div class="progress margin-bottom-1x">
                                                        <div class="progress-bar bg-warning" role="progressbar"
                                                            style="width: {{ $item->reviews->where('status', 1)->where('rating', 4)->sum('rating') * 20 }}%; height: 2px;"
                                                            aria-valuenow="{{ $item->reviews->where('rating', 4)->sum('rating') * 20 }}"
                                                            aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <label class="text-medium text-sm review-stars">
                                                        @foreach ([1, 2, 3] as $star)
                                                            <i class = 'fas fa-star'></i>
                                                        @endforeach <span class="text-muted">-
                                                            {{ $item->reviews->where('status', 1)->where('rating', 3)->count() }}</span>
                                                    </label>
                                                    <div class="progress margin-bottom-1x">
                                                        <div class="progress-bar bg-warning" role="progressbar"
                                                            style="width: {{ $item->reviews->where('rating', 3)->sum('rating') * 20 }}%; height: 2px;"
                                                            aria-valuenow="{{ $item->reviews->where('rating', 3)->sum('rating') * 20 }}"
                                                            aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <label class="text-medium text-sm review-stars">
                                                        @foreach ([1, 2] as $star)
                                                            <i class = 'fas fa-star'></i>
                                                        @endforeach <span class="text-muted">-
                                                            {{ $item->reviews->where('status', 1)->where('rating', 2)->count() }}</span>
                                                    </label>
                                                    <div class="progress margin-bottom-1x">
                                                        <div class="progress-bar bg-warning" role="progressbar"
                                                            style="width: {{ $item->reviews->where('status', 1)->where('rating', 2)->sum('rating') * 20 }}%; height: 2px;"
                                                            aria-valuenow="{{ $item->reviews->where('rating', 2)->sum('rating') * 20 }}"
                                                            aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <label class="text-medium text-sm review-stars"><i
                                                            class = 'fas fa-star'></i><span class="text-muted">-
                                                            {{ $item->reviews->where('status', 1)->where('rating', 1)->count() }}</span></label>
                                                    <div class="progress mb-2">
                                                        <div class="progress-bar bg-warning" role="progressbar"
                                                            style="width: {{ $item->reviews->where('status', 1)->where('rating', 1)->sum('rating') * 20 }}; height: 2px;"
                                                            aria-valuenow="0"
                                                            aria-valuemin="{{ $item->reviews->where('rating', 1)->sum('rating') * 20 }}"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                @if (Auth::user())
                                                    <div class="pb-2"><a class="btn btn-primary btn-block"
                                                            href="#" data-bs-toggle="modal"
                                                            data-bs-target="#leaveReview"><span>{{ __('Leave a Review') }}</span></a>
                                                    </div>
                                                @else
                                                    <div class="pb-2"><a class="btn btn-primary btn-block"
                                                            href="{{ route('user.login') }}"><span>{{ __('Login') }}</span></a>
                                                    </div>
                                                @endif
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


    @if (count($related_items) > 0)
        <div class="relatedproduct-section container padding-bottom-3x mb-1 s-pt-30">
            <!-- Related Products Carousel-->
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2 class="h3">{{ __('Related Products') }}</h2>
                    </div>
                </div>
            </div>
            <!-- Carousel-->
            <div class="row">
                <div class="col-lg-12">
                    <div class="relatedproductslider owl-carousel">
                        @foreach ($related_items as $related)
                            <div class="slider-item">
                                <div class="product-card">

                                    @if ($related->is_stock())
                                        @if ($related->is_type == 'new')
                                        @else
                                            <div
                                                class="product-badge
                                    @if ($related->is_type == 'feature') bg-warning

                                    @elseif($related->is_type == 'top')
                                    bg-info
                                    @elseif($related->is_type == 'best')
                                    bg-dark
                                    @elseif($related->is_type == 'flash_deal')
                                    bg-success @endif
                                    ">
                                                {{ $related->is_type != 'undefine' ? ucfirst(str_replace('_', ' ', $related->is_type)) : '' }}
                                            </div>
                                        @endif
                                    @else
                                        <div
                                            class="product-badge bg-secondary border-default text-body
                                    ">
                                            {{ __('out of stock') }}</div>
                                    @endif
                                    @if ($related->previous_price && $related->previous_price != 0)
                                        <div class="product-badge product-badge2 bg-info">
                                            -{{ PriceHelper::DiscountPercentage($related) }}</div>
                                    @endif

                                    @if ($related->previous_price && $related->previous_price != 0)
                                        <div class="product-badge product-badge2 bg-info">
                                            -{{ PriceHelper::DiscountPercentage($related) }}</div>
                                    @endif
                                    <div class="product-thumb">
                                        <a href="{{ route('front.product', $related->slug) }}">
                                            <img class="lazy for_height"
                                                data-src="{{ url('/storage/images/' . $related->thumbnail) }}"
                                                alt="Product">
                                        </a>
                                        <div class="product-button-group">
                                            <a class="product-button wishlist_store"
                                                href="{{ route('user.wishlist.store', $related->id) }}"
                                                title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                            <a class="product-button product_compare" href="javascript:;"
                                                data-target="{{ route('fornt.compare.product', $related->id) }}"
                                                title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                            @include('includes.item_footer', ['sitem' => $related])
                                        </div>
                                    </div>
                                    <div class="product-card-body">
                                        <div class="product-category"><a
                                                href="{{ route('front.catalog') . '?category=' . $related->category->slug }}">{{ $related->category->name }}</a>
                                        </div>
                                        <h3 class="product-title"><a
                                                href="{{ route('front.product', $related->slug) }}">
                                                {{ Str::limit($related->name, 35) }}
                                            </a></h3>
                                        <h4 class="product-price">
                                            @if ($related->previous_price != 0)
                                                <del>{{ PriceHelper::setPreviousPrice($related->previous_price) }}</del>
                                            @endif
                                            {{ PriceHelper::grandCurrencyPrice($related) }}
                                        </h4>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif


    @if ($setting->is_service == 1)
        <section class="service-section">
            <div class="container">
                <div class="row">
                    @foreach ($services as $service)
                        <div class="col-lg-3 col-sm-6 text-center mb-30">
                            <div class="single-service single-service2">
                                <img src="{{ url('/storage/images/' . $service->photo) }}" alt="Shipping">
                                <div class="content">
                                    <h6 class="mb-2">{{ $service->title }}</h6>
                                    <p class="text-sm text-muted mb-0">{{ $service->details }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @auth
        <form class="modal fade ratingForm" action="{{ route('front.review.submit') }}" method="post" id="leaveReview"
            tabindex="-1">
            @csrf
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Leave a Review') }}</h4>
                        <button class="close modal_close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        @php
                            $user = Auth::user();
                        @endphp
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="review-name">{{ __('Your Name') }}</label>
                                    <input class="form-control" type="text" id="review-name"
                                        value="{{ $user->first_name }}" required>
                                </div>
                            </div>
                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="review-email">{{ __('Your Email') }}</label>
                                    <input class="form-control" type="email" id="review-email"
                                        value="{{ $user->email }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="review-subject">{{ __('Subject') }}</label>
                                    <input class="form-control" type="text" name="subject" id="review-subject" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="review-rating">{{ __('Rating') }}</label>
                                    <select name="rating" class="form-control" id="review-rating">
                                        <option value="5">5 {{ __('Stars') }}</option>
                                        <option value="4">4 {{ __('Stars') }}</option>
                                        <option value="3">3 {{ __('Stars') }}</option>
                                        <option value="2">2 {{ __('Stars') }}</option>
                                        <option value="1">1 {{ __('Star') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="review-message">{{ __('Review') }}</label>
                            <textarea class="form-control" name="review" id="review-message" rows="8" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit"><span>{{ __('Submit Review') }}</span></button>
                    </div>
                </div>
            </div>
        </form>
    @endauth

<!-- Attribute Validation Script -->
<script>
// Wait for the page to fully load including jQuery
window.addEventListener('load', function() {
    // Double check jQuery is available
    if (typeof jQuery !== 'undefined') {
        if (typeof initializeAttributeValidation === 'function') {
            initializeAttributeValidation();
        } else {
            console.error('initializeAttributeValidation function not found on first attempt.');
        }
    } else {
        // Fallback: wait a bit more and try again
        setTimeout(function() {
            if (typeof jQuery !== 'undefined') {
                if (typeof initializeAttributeValidation === 'function') {
                    initializeAttributeValidation();
                } else {
                    console.error('initializeAttributeValidation function still not available after delay.');
                }
            } else {
                console.error('jQuery is not available. Attribute validation cannot be initialized.');
            }
        }, 500);
    }
});

function initializeAttributeValidation() {
    const $ = jQuery; // Ensure we're using jQuery
    
    $(document).ready(function() {
        console.log('Attribute validation script initialized');
        
        // Enhanced click handling for all attribute options - click anywhere on the card
        $('.attribute-option-item').on('click', function(e) {
            e.preventDefault();
            const $radioButton = $(this).find('.attribute_option');
            if (!$radioButton.prop('checked')) {
                $radioButton.prop('checked', true).trigger('change');
            }
        });
        
        // Enhanced click handling for color options - click anywhere on the card
        $('.attribute-option-item[data-attribute-name="color"]').on('click', function(e) {
            e.preventDefault();
            const $radioButton = $(this).find('.attribute_option');
            if (!$radioButton.prop('checked')) {
                $radioButton.prop('checked', true).trigger('change');
            }
        });
        
        // Simplified attribute option handling with enhanced color selection
        $('.attribute_option').on('change', function() {
            console.log('Attribute changed:', $(this).val());
            
            const $selectedItem = $(this).closest('.attribute-option-item');
            const attributeName = $(this).data('attribute-name');
            
            // Update selection styling for current attribute group
            const $attributeGroup = $(this).closest('.form-group');
            $attributeGroup.find('.attribute-option-item').removeClass('selected');
            $selectedItem.addClass('selected');
            
            // Special handling for color attributes
            if (attributeName === 'color' || attributeName === 'colour') {
                console.log('Color attribute changed to:', $(this).val());
                
                // Get image data from the selected color option
                const $img = $selectedItem.find('img');
                if ($img.length > 0) {
                    // Try to get color-specific image
                    let colorImageSrc = $img.data('main-image') || $img.attr('src');
                    const colorName = $img.data('color') || $(this).val();
                    
                    console.log('Color image source:', colorImageSrc);
                    console.log('Color name:', colorName);
                    
                    // Change the main product image
                    changeMainProductImage(colorImageSrc, colorName);
                } else {
                    console.log('No image found for color option');
                }
            }
            
            // Calculate and update price
            updateProductPrice();
        });

    // Persist last selected color image globally so re-renders or other handlers can reuse
    window.__lastColorImage = window.__lastColorImage || null;
    window.__lastColorName = window.__lastColorName || null;
    let galleryOverrideActive = false; // when true, a gallery click has overridden color choice

        // Override color change handling to remember image
        $('.attribute_option[data-attribute-name="color"]').on('change.colorImageMemory', function() {
            const $selectedItem = $(this).closest('.attribute-option-item');
            const $img = $selectedItem.find('img');
            if ($img.length) {
                window.__lastColorImage = $img.data('main-image') || $img.attr('src');
                window.__lastColorName = $img.data('color') || $(this).val();
                galleryOverrideActive = false; // reset when user explicitly chooses a color
            }
        });

        // Gallery click handler: update main image, but if a color is currently selected, allow re-selecting color to restore it
        $(document).on('click', '.product-details-slider .item img', function(e) {
            // Ignore if this is already the main image element
            if (this.id === 'main_product_image') return;
            const newSrc = $(this).attr('src');
            if (newSrc) {
                galleryOverrideActive = true;
                changeMainProductImage(newSrc, window.__lastColorName || '');
            }
        });

        // Helper to (future) reinitialize zoom plugin if added
        function reinitZoom() {
            // Placeholder: if a zoom library is added later, clean previous instance and reattach here.
            // e.g., if ($('#main_product_image').data('elevateZoom')) { $('.zoomContainer').remove(); $('#main_product_image').removeData('elevateZoom'); }
            // Then re-init plugin with $('#main_product_image').elevateZoom({...});
        }
        
        // Function to update main product image
        function changeMainProductImage(colorImageSrc, colorName) {
            try {
                console.log('Attempting to change main image for color:', colorName);
                
                const $mainImage = $('#main_product_image');
                
                if ($mainImage.length && colorImageSrc) {
                    console.log('Changing main image from:', $mainImage.attr('src'));
                    console.log('Changing main image to:', colorImageSrc);
                    
                    // Add fade effect for smooth transition
                    $mainImage.fadeOut(200, function() {
                        $(this).attr('src', colorImageSrc)
                               .attr('alt', 'Product - ' + colorName)
                               .attr('data-current-color', colorName || '')
                               .fadeIn(300);
                    });
                    
                    // If there is a separate visible first carousel item different from direct image reference, sync it
                    try {
                        const $owl = $('.product-details-slider');
                        if ($owl.hasClass('owl-loaded')) {
                            // Update every instance (original + cloned) of the main image
                            $owl.find('.owl-item').each(function(){
                                const $img = $(this).find('img#main_product_image');
                                if ($img.length) {
                                    $img.attr('src', colorImageSrc).attr('alt', 'Product - ' + colorName);
                                }
                            });
                        } else {
                            const $firstCarouselImg = $('.product-details-slider .item:first img').not($mainImage);
                            $firstCarouselImg.attr('src', colorImageSrc).attr('alt', 'Product - ' + colorName);
                        }
                        // Sync first thumbnail if exists
                        $('.product-thumbnails .item:first img').attr('src', colorImageSrc).attr('alt', 'Product - ' + colorName);
                    } catch (syncErr) {
                        console.log('Carousel sync error:', syncErr);
                    }
                    
                    console.log('Main image updated successfully');
                    // Reinitialize zoom after image load if plugin present in future
                    reinitZoom();
                } else {
                    console.log('No main image element found or invalid source');
                }
            } catch (error) {
                console.log('Error changing product image:', error);
            }
        }
        
        // Function to update product price
        function updateProductPrice() {
            try {
                // Get base price from hidden input
                let basePrice = parseFloat($('#demo_price').val()) || 0;
                
                // Calculate total extra price from selected attributes
                let totalExtraPrice = 0;
                $('.attribute_option:checked').each(function() {
                    let optionPrice = parseFloat($(this).data('target')) || 0;
                    totalExtraPrice += optionPrice;
                });
                
                // Calculate final price
                let finalPrice = basePrice + totalExtraPrice;
                
                // Update the price display
                let currencySign = $('#set_currency').val() || '৳';
                let currencyDirection = $('#currency_direction').val() || 'left';
                
                let priceText = currencyDirection === 'left' 
                    ? currencySign + finalPrice.toFixed(2)
                    : finalPrice.toFixed(2) + currencySign;
                    
                $('#main_price').text(priceText);
                
                console.log('Price updated - Base:', basePrice, 'Extra:', totalExtraPrice, 'Final:', finalPrice);
            } catch (error) {
                console.log('Error updating price:', error);
            }
        }
            
            // Removed stray debug log that referenced variables out of scope (basePrice, totalExtraPrice, finalPrice)
        });
        
        // Initialize pricing and selection states on page load
        setTimeout(function() {
            try {
                // Set initial selected states for first options
                $('.form-group').each(function() {
                    const $firstOption = $(this).find('.attribute_option:first');
                    if ($firstOption.length && $firstOption.prop('checked')) {
                        $firstOption.closest('.attribute-option-item').addClass('selected');
                    }
                });
                
                // Trigger initial price calculation
                updateProductPrice();
                
                console.log('Attribute options initialized successfully');
            } catch (error) {
                console.log('Error initializing attributes:', error);
            }
        }, 100);
        
        // Function to validate all required attribute options
        function validateAttributeOptions() {
            let isValid = true;
            let errorMessages = [];
            
            $('.attribute_option[required]').each(function() {
                if ($(this).val() === '' || $(this).val() === null) {
                    let attributeName = $(this).closest('.form-group').find('label').text().replace(':', '').trim();
                    errorMessages.push(attributeName);
                    isValid = false;
                }
            });
            
            // Display error message using toaster notification
            if (!isValid) {
                // Remove any existing error messages from DOM
                $('.attribute-error').remove();
                
                let errorMessage;
                if (errorMessages.length === 1) {
                    errorMessage = `Please select ${errorMessages[0]}`;
                } else {
                    errorMessage = `Please select: ${errorMessages.join(', ')}`;
                }
                
                // Show toaster notification for error
                if (typeof DangerNotification === 'function') {
                    DangerNotification(errorMessage);
                } else {
                    // Fallback to console if notification function is not available
                    console.error('Validation Error:', errorMessage);
                    alert(errorMessage);
                }
            } else {
                // Remove any existing error messages when validation passes
                $('.attribute-error').remove();
            }
            
            return isValid;
        }
        
        // Validate on Add to Cart button click
        $('#add_to_cart').on('click', function(e) {
            console.log('Add to cart clicked');
            if ($('.attribute_option[required]').length > 0) {
                if (!validateAttributeOptions()) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Validation failed - prevented add to cart');
                    return false;
                }
            }
            console.log('Validation passed - proceeding with add to cart');
        });
        
        // Validate on Buy Now button click
        $('#but_to_cart').on('click', function(e) {
            console.log('Buy now clicked');
            if ($('.attribute_option[required]').length > 0) {
                if (!validateAttributeOptions()) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Validation failed - prevented buy now');
                    return false;
                }
            }
            console.log('Validation passed - proceeding with buy now');
        });

    }        
            </script>

@endsection
