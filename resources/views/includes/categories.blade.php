{{--
 @Author: Anwarul
 @Date: 2025-08-13 15:10:28
 @LastEditors: Anwarul
 @LastEditTime: 2025-08-18 13:29:24
 @Description: Innova IT
 --}}
@php
    $categories = App\Models\Category::with('subcategory','subcategory.childcategory')->whereStatus(1)->orderby('serial', 'asc')->take(8)->get();
@endphp


<div class="left-category-area">
    <div class="category-header">
        <h4><svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M9.875 12.9195C9.875 12.422 9.6775 11.9452 9.32563 11.5939C8.97438 11.242 8.4975 11.0445 8 11.0445C6.75875 11.0445 4.86625 11.0445 3.625 11.0445C3.1275 11.0445 2.65062 11.242 2.29937 11.5939C1.9475 11.9452 1.75 12.422 1.75 12.9195V17.2945C1.75 17.792 1.9475 18.2689 2.29937 18.6202C2.65062 18.972 3.1275 19.1695 3.625 19.1695H8C8.4975 19.1695 8.97438 18.972 9.32563 18.6202C9.6775 18.2689 9.875 17.792 9.875 17.2945V12.9195ZM19.25 12.9195C19.25 12.422 19.0525 11.9452 18.7006 11.5939C18.3494 11.242 17.8725 11.0445 17.375 11.0445C16.1337 11.0445 14.2413 11.0445 13 11.0445C12.5025 11.0445 12.0256 11.242 11.6744 11.5939C11.3225 11.9452 11.125 12.422 11.125 12.9195V17.2945C11.125 17.792 11.3225 18.2689 11.6744 18.6202C12.0256 18.972 12.5025 19.1695 13 19.1695H17.375C17.8725 19.1695 18.3494 18.972 18.7006 18.6202C19.0525 18.2689 19.25 17.792 19.25 17.2945V12.9195ZM16.5131 9.66516L19.1206 7.05766C19.8525 6.32578 19.8525 5.13828 19.1206 4.4064L16.5131 1.79891C15.7813 1.06703 14.5937 1.06703 13.8619 1.79891L11.2544 4.4064C10.5225 5.13828 10.5225 6.32578 11.2544 7.05766L13.8619 9.66516C14.5937 10.397 15.7813 10.397 16.5131 9.66516ZM9.875 3.54453C9.875 3.04703 9.6775 2.57015 9.32563 2.2189C8.97438 1.86703 8.4975 1.66953 8 1.66953C6.75875 1.66953 4.86625 1.66953 3.625 1.66953C3.1275 1.66953 2.65062 1.86703 2.29937 2.2189C1.9475 2.57015 1.75 3.04703 1.75 3.54453V7.91953C1.75 8.41703 1.9475 8.89391 2.29937 9.24516C2.65062 9.59703 3.1275 9.79453 3.625 9.79453H8C8.4975 9.79453 8.97438 9.59703 9.32563 9.24516C9.6775 8.89391 9.875 8.41703 9.875 7.91953V3.54453Z"
                    fill="currentColor"></path>
            </svg> {{ __('Categories') }}</h4>
    </div>
    <div class="category-list">
        @foreach ($categories as $key => $pcategory)
            <div class="c-item">
                <a class="d-block navi-link" href="{{ route('front.catalog') . '?category=' . $pcategory->slug }}">
                    <img class="lazy" data-src="{{ url('/storage/images/' . $pcategory->photo) }}">
                    <span class="text-gray-dark">{{ $pcategory->name }}</span>
                    @if ($pcategory->subcategory->count() > 0)
                        <i class="icon-chevron-right"></i>
                    @endif
                </a>
                @if ($pcategory->subcategory->count() > 0)
                    <div class="sub-c-box">
                        @foreach ($pcategory->subcategory as $scategory)
                            <div class="child-c-box">
                                <a class="title" href="{{ route('front.catalog') . '?subcategory=' . $scategory->slug }}">
                                    {{ $scategory->name }}
                                    @if ($scategory->childcategory->count() > 0)
                                        <i class="icon-chevron-right"></i>
                                    @endif
                                </a>
                                @if ($scategory->childcategory->count() > 0)
                                    <div class="child-category">

                                        @foreach ($scategory->childcategory as $childcategory)
                                            <a
                                                href="{{ route('front.catalog') . '?childcategory=' . $childcategory->slug }}">{{ $childcategory->name }}</a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
        <a href="{{ route('front.catalog') }}" class="d-block navi-link view-all-category">
            <img class="lazy" data-src="{{ url('/storage/images/category.jpg') }}" alt="">
            <span class="text-gray-dark">{{ __('All Categories') }}</span>
        </a>
    </div>


</div>
