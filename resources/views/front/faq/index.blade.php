@extends('master.front')
@section('meta')
    <meta name="keywords" content="{{ $setting->meta_keywords }}">
    <meta name="description" content="{{ $setting->meta_description }}">
@endsection
@section('title')
    {{ __('FAQ') }}
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
                        <li>{{ __('FAQ') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Content-->
    <div class="container">
        {{-- <div class="row">
        @foreach ($fcategories as $category)
            <div class="col-lg-4 col-md-6">
                <a href="{{route('front.faq.details',$category->slug)}}" class="card mb-4 faq-box">
                    <div class="card-body">
                        <h6 class="card-title">{{$category->name}}</h6>
                        <p class="card-text">{{$category->text}}</p>
                        <span class="text-sm text-muted link">{{ __('View Details') }} <i class="icon-chevron-right"></i></span>
                    </div>
                </a>
            </div>
        @endforeach
    </div> --}}
        <div class="faq-tab-content-wrap">
            <div class="row g-4">
                <!-- Left side: Category Tabs -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="nav flex-column nav-pills card-body" id="v-pills-tab" role="tablist"
                            aria-orientation="vertical">
                            <button class="nav-link active" id="v-pills-shipping-tab" data-bs-toggle="pill"
                                data-bs-target="#v-pills-shipping" type="button" role="tab"
                                aria-controls="v-pills-shipping" aria-selected="true">
                                Shipping & Delivery
                            </button>
                            <button class="nav-link" id="v-pills-payment-tab" data-bs-toggle="pill"
                                data-bs-target="#v-pills-payment" type="button" role="tab"
                                aria-controls="v-pills-payment" aria-selected="false">
                                Payment & Refund
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right side: FAQ Accordion -->
                <div class="col-md-9">

                    <div class="tab-content faq-tab-content" id="v-pills-tabContent">
                        <!-- Shipping FAQs -->
                        <div class="tab-pane fade show active" id="v-pills-shipping" role="tabpanel"
                            aria-labelledby="v-pills-shipping-tab">
                            <div class="accordion" id="shippingAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="shipQ1">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#shipA1" aria-expanded="true" aria-controls="shipA1">
                                            How many days will it take to receive delivery?
                                        </button>
                                    </h2>
                                    <div id="shipA1" class="accordion-collapse collapse show" aria-labelledby="shipQ1"
                                        data-bs-parent="#shippingAccordion">
                                        <div class="accordion-body">
                                            Delivery is usually within 3-5 business days. Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quas, harum labore! Ab harum dicta necessitatibus nostrum quas a minus ex, quia earum natus sequi voluptates? Non, sit omnis aliquam aspernatur itaque laborum quis, voluptate hic sunt optio voluptatibus sed. Magni, commodi laborum. Alias consequatur inventore enim labore recusandae architecto nobis!
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="shipQ2">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#shipA2" aria-expanded="false" aria-controls="shipA2">
                                            Can I change the delivery address?
                                        </button>
                                    </h2>
                                    <div id="shipA2" class="accordion-collapse collapse" aria-labelledby="shipQ2"
                                        data-bs-parent="#shippingAccordion">
                                        <div class="accordion-body">
                                            Yes, the address can be changed before the order is confirmed.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment FAQs -->
                        <div class="tab-pane fade" id="v-pills-payment" role="tabpanel"
                            aria-labelledby="v-pills-payment-tab">
                            <div class="accordion" id="paymentAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="payQ1">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#payA1" aria-expanded="true" aria-controls="payA1">
                                            What payment methods are available?
                                        </button>
                                    </h2>
                                    <div id="payA1" class="accordion-collapse collapse show" aria-labelledby="payQ1"
                                        data-bs-parent="#paymentAccordion">
                                        <div class="accordion-body">
                                            Cash on delivery, online payment and bank transfer.
                                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Doloribus, non. Culpa illo officia, necessitatibus modi dolores voluptatem suscipit iste consectetur. Inventore minima ea ullam pariatur, blanditiis, eveniet aliquam doloribus dolorum quam ipsa omnis. Blanditiis nulla beatae recusandae possimus eligendi eius assumenda repellendus error labore, corporis minus cumque distinctio placeat nostrum tempora cum unde autem ipsum praesentium alias quidem. Quos aperiam ex rerum earum recusandae vel ratione neque pariatur reiciendis laudantium natus hic soluta sequi commodi, dolor veniam provident tenetur nesciunt quis veritatis distinctio! Asperiores totam, sapiente optio maxime fugit laborum dolorum deserunt velit hic illum et cum veniam perferendis ab.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="payQ2">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#payA2" aria-expanded="false" aria-controls="payA2">
                                            How many days does the refund take?
                                        </button>
                                    </h2>
                                    <div id="payA2" class="accordion-collapse collapse" aria-labelledby="payQ2"
                                        data-bs-parent="#paymentAccordion">
                                        <div class="accordion-body">
                                            Refunds are usually completed within 7-10 business days.
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
@endsection
