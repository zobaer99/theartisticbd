<ul class="nav">

    <!-- Dashboard -->
    <li class="nav-item">
        <a href="{{ route('back.dashboard') }}">
            <i class="fas fa-home"></i>
            <p>{{ __('Dashboard') }}</p>
        </a>
    </li>

    <!-- Categories Management -->
    <li class="nav-item">
        <a data-toggle="collapse" href="#category">
            <i class="fas fa-list-alt"></i>
            <p>{{ __('Manage Categories') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="category">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.category.index') }}">
                        <span class="sub-item">{{ __('Categories') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.subcategory.index') }}">
                        <span class="sub-item">{{ __('Sub categories') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.childcategory.index') }}">
                        <span class="sub-item">{{ __('Child categories') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Product Management -->
    <li class="nav-item">
        <a data-toggle="collapse" href="#items">
            <i class="fab fa-product-hunt"></i>
            <p>{{ __('Manage Products') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="items">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.brand.index') }}">
                        <span class="sub-item">{{ __('Brands') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.item.add') }}">
                        <span class="sub-item">{{ __('Add Product') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.item.index') }}">
                        <span class="sub-item">{{ __('All Products') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.item.stock.out') }}">
                        <span class="sub-item">{{ __('Stock Out Products') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.campaign.index') }}">
                        <span class="sub-item">{{ __('Campaign Offer') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.bulk.product.index') }}">
                        <span class="sub-item">{{ __('CSV Import & Export') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.review.index') }}">
                        <span class="sub-item">{{ __('Product Reviews') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Orders Management -->
    <li class="nav-item {{ request()->is('orders/*') ? 'submenu' : '' }}">
        <a data-toggle="collapse" href="#order">
            <i class="fab fa-first-order"></i>
            <p>{{ __('Manage Orders') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="order">
            <ul class="nav nav-collapse">
                <li class="{{ !request()->input('type') && request()->is('admin/orders') ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('back.order.index') }}">
                        <span class="sub-item">{{ __('All Orders') }}</span>
                    </a>
                </li>
                <li class="{{ request()->input('type') == 'Pending' ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('back.order.index') . '?type=Pending' }}">
                        <span class="sub-item">{{ __('Pending Orders') }}</span>
                    </a>
                </li>
                <li class="{{ request()->input('type') == 'In Progress' ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('back.order.index') . '?type=In Progress' }}">
                        <span class="sub-item">{{ __('Progress Orders') }}</span>
                    </a>
                </li>
                <li class="{{ request()->input('type') == 'Delivered' ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('back.order.index') . '?type=Delivered' }}">
                        <span class="sub-item">{{ __('Delivered Orders') }}</span>
                    </a>
                </li>
                <li class="{{ request()->input('type') == 'Canceled' ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('back.order.index') . '?type=Canceled' }}">
                        <span class="sub-item">{{ __('Canceled Orders') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Transactions -->
    <li class="nav-item">
        <a href="{{ route('back.transaction.index') }}">
            <i class="fas fa-random"></i>
            <p>{{ __('Transactions') }}</p>
        </a>
    </li>

    <!-- Ecommerce Settings -->
    <li class="nav-item">
        <a data-toggle="collapse" href="#ecommerce">
            <i class="fas fa-newspaper"></i>
            <p>{{ __('Ecommerce') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="ecommerce">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.code.index') }}">
                        <span class="sub-item">{{ __('Set Coupons') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.shipping.index') }}">
                        <span class="sub-item">{{ __('Shipping') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.shipping-location.index') }}">
                        <span class="sub-item">{{ __('Address-based Shipping') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.tax.index') }}">
                        <span class="sub-item">{{ __('Tax') }}</span>
                    </a>
                </li>
                {{-- Currency management commented out - using fixed BDT currency
                <li>
                    <a class="sub-link" href="{{ route('back.currency.index') }}">
                        <span class="sub-item">{{ __('Currency') }}</span>
                    </a>
                </li>
                --}}
                <li>
                    <a class="sub-link" href="{{ route('back.setting.payment') }}">
                        <span class="sub-item">{{ __('Payment') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Customers & Tickets -->
    <li class="nav-item">
        <a href="{{ route('back.user.index') }}">
            <i class="fas fa-users"></i>
            <p>{{ __('Customer List') }}</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('back.ticket.index') }}">
            <i class="fas fa-comments"></i>
            <p>{{ __('Manages Tickets') }}</p>
        </a>
    </li>

    <!-- Site Management -->
    <li class="nav-item">
        <a data-toggle="collapse" href="#content">
            <i class="fas fa-tasks"></i>
            <p>{{ __('Manage Site') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="content">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.setting.system') }}">
                        <span class="sub-item">{{ __('General Settings') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.menu.index') }}">
                        <span class="sub-item">{{ __('Menu Builder') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.homePage') }}">
                        <span class="sub-item">{{ __('Home Page') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.slider.index') }}">
                        <span class="sub-item">{{ __('Sliders') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.service.index') }}">
                        <span class="sub-item">{{ __('Services') }}</span>
                    </a>
                </li>
                {{-- <li>
                    <a class="sub-link" href="{{ route('back.setting.section') }}">
                        <span class="sub-item">{{ __('Visibility') }}</span>
                    </a>
                </li> --}}

                <li>
                    <a class="sub-link" href="{{ route('back.video-stories.index') }}">
                        <span class="sub-item">{{ __('Videos Stories') }}</span>
                    </a>
                </li>

                <li>
                    <a class="sub-link" href="{{ route('back.setting.storage') }}">
                        <span class="sub-item">{{ __('Storage Settings') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.setting.social') }}">
                        <span class="sub-item">{{ __('Social Login') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.setting.email') }}">
                        <span class="sub-item">{{ __('Email Settings') }}</span>
                    </a>
                </li>
                {{-- <li>
                    <a class="sub-link" href="{{ route('back.setting.sms') }}">
                        <span class="sub-item">{{ __('SMS Settings') }}</span>
                    </a>
                </li> --}}
                <li>
                    <a class="sub-link" href="{{ route('back.subscribers.announcement') }}">
                        <span class="sub-item">{{ __('Announcement') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.cookie.alert') }}">
                        <span class="sub-item">{{ __('Cookies Alert') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.setting.maintainance') }}">
                        <span class="sub-item">{{ __('Maintainance') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('admin.sitemap.index') }}">
                        <span class="sub-item">{{ __('Sitemap') }}</span>
                    </a>
                </li>
                {{-- <li>
                    <a class="sub-link" href="{{ route('back.language.index') }}">
                        <span class="sub-item">{{ __('Language') }}</span>
                    </a>
                </li> --}}
            </ul>
        </div>
    </li>

    <!-- FAQ Management -->
    <li class="nav-item">
        <a data-toggle="collapse" href="#faqs">
            <i class="fas fa-question-circle"></i>
            <p>{{ __('Manage Faqs') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="faqs">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.fcategory.index') }}">
                        <span class="sub-item">{{ __('Categories') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.faq.index') }}">
                        <span class="sub-item">{{ __('Faqs') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Pages & Subscribers -->
    <li class="nav-item">
        <a href="{{ route('back.page.index') }}">
            <i class="fas fa-book"></i>
            <p>{{ __('Manages Pages') }}</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('back.subscribers.index') }}">
            <i class="fab fa-telegram-plane"></i>
            <p>{{ __('Subscribers List') }}</p>
        </a>
    </li>

    <!-- System Users -->
    <li class="nav-item">
        <a data-toggle="collapse" href="#user">
            <i class="far fa-user"></i>
            <p>{{ __('System User') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="user">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.role.index') }}">
                        <span class="sub-item">{{ __('Role') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.staff.index') }}">
                        <span class="sub-item">{{ __('System User') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Cache Clear -->
    <li class="nav-item">
        <a href="{{ route('front.cache.clear') }}">
            <i class="fas fa-broom"></i>
            <p>{{ __('Cache Clear') }}</p>
        </a>
    </li>

</ul>

{{-- <style>
    .nav-group-title {
        font-weight: 600;
        font-size: 0.75rem; /* aro choto size */
        /* ekhane size komano hoyeche */
        padding: 8px 15px;
        color: #f8feff;
        text-transform: uppercase;
        border-bottom: 1px solid #ddd;
        display: block;
    }
</style> --}}
