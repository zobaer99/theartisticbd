<?php

namespace App\Providers;

use Illuminate\{
    Support\ServiceProvider,
    Support\Facades\DB,
    Support\Facades\Log
};
use Illuminate\Pagination\Paginator;
use App\Models\Setting;
use App\Models\ExtraSetting;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Item;
use App\Models\Slider;
use App\Observers\SettingObserver;
use App\Observers\ExtraSettingObserver;
use App\Observers\MenuObserver;
use App\Observers\CategoryObserver;
use App\Observers\BrandObserver;
use App\Observers\ItemObserver;
use App\Observers\SliderObserver;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register model observers for automatic cache invalidation
        Setting::observe(SettingObserver::class);
        ExtraSetting::observe(ExtraSettingObserver::class);
        Menu::observe(MenuObserver::class);
        Category::observe(CategoryObserver::class);
        Brand::observe(BrandObserver::class);
        Item::observe(ItemObserver::class);
        Slider::observe(SliderObserver::class);

        // Enable aggressive query optimization
        if (config('app.debug')) {
            DB::listen(function ($query) {
                if ($query->time > 50) { // Log slow queries
                    Log::info('Slow Query: ' . $query->sql . ' [' . $query->time . 'ms]');
                }
            });
        }

        // Reduce query logging overhead in production
        if (!config('app.debug')) {
            DB::enableQueryLog(false);
        }

        // Cache global data for all views with cache tags for better invalidation
        // Use singleton pattern to ensure data is loaded only once per request
        view()->composer('*', function ($view) {
            static $globalData = null;
            
            if ($globalData === null) {
                $globalData = [
                    'setting' => cache()->remember('global_setting', 7200, function () {
                        return Setting::first();
                    }),
                    'extra_settings' => cache()->remember('global_extra_settings', 7200, function () {
                        return ExtraSetting::first();
                    }),
                    'menus' => cache()->remember('global_menus', 7200, function () {
                        return Menu::first();
                    }),
                    'default_language' => cache()->remember('default_language', 7200, function () {
                        return DB::table('languages')->where('is_default', 1)->first();
                    }),
                    'website_languages' => cache()->remember('website_languages', 7200, function () {
                        return DB::table('languages')->whereType('Website')->get();
                    }),
                    'currencies' => cache()->remember('all_currencies', 7200, function () {
                        return DB::table('currencies')->get();
                    }),
                    'allCategories' => cache()->remember('active_categories', 3600, function () {
                        return \App\Models\Category::with(['subcategory.childcategory'])
                            ->whereStatus(1)
                            ->orderBy('serial', 'asc')
                            ->get();
                    }),
                    'footer_pages' => cache()->remember('footer_pages', 7200, function () {
                        return DB::table('pages')->whereIn('pos', [0, 1, 2])->get();
                    }),
                    'payment_settings' => cache()->remember('payment_settings', 7200, function () {
                        return DB::table('payment_settings')->get()->keyBy('unique_keyword');
                    }),
                    'allBrands' => cache()->remember('active_brands', 3600, function () {
                        return Brand::whereStatus(1)->get();
                    }),
                    'globalProducts' => cache()->remember('theme1_global_products', 1800, function () {
                        return \App\Models\Item::with(['category', 'subcategory', 'brand'])
                            ->whereStatus(1)
                            ->orderBy('id', 'DESC')
                            ->limit(30) // Reduced for theme1 only usage
                            ->get();
                    }),
                    'sliders' => cache()->remember('theme1_sliders_only', 7200, function () {
                        return \App\Models\Slider::where('home_page', 'theme1')->limit(5)->get(); // Only theme1 sliders
                    }),
                    'services' => cache()->remember('theme1_global_services', 7200, function () {
                        return \App\Models\Service::limit(6)->get(); // Reduced for theme1
                    }),
                    'recentPosts' => cache()->remember('theme1_recent_posts', 7200, function () {
                        return \App\Models\Post::orderBy('id', 'desc')->limit(4)->get(); // Reduced for theme1
                    }),
                    'topBrands' => cache()->remember('theme1_top_brands', 3600, function () {
                        return \App\Models\Brand::orderBy('id', 'desc')->limit(6)->get(); // Reduced for theme1
                    })
                ];
            }
            
            $view->with($globalData);
        });
        // if (!file_exists('core/storage/installed') && !request()->is('install') && !request()->is('install/*')) {
         
        //     header("Location: install/");
        //     exit;
        // }
    }
}
