<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    /**
     * Clear all global cache keys
     */
    public static function clearAllGlobalCache()
    {
        $cacheKeys = [
            'global_setting',
            'global_extra_settings',
            'global_menus',
            'default_language',
            'website_languages',
            'all_currencies',
            'active_categories',
            'footer_pages',
            'payment_settings',
            'active_brands',
            'theme1_global_products',
            'theme1_sliders_only',
            'theme1_global_services',
            'theme1_recent_posts',
            'theme1_top_brands'
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear setting related cache
     */
    public static function clearSettingsCache()
    {
        Cache::forget('global_setting');
        Cache::forget('global_extra_settings');
        Cache::forget('payment_settings');
    }

    /**
     * Clear category related cache
     */
    public static function clearCategoriesCache()
    {
        Cache::forget('active_categories');
        Cache::forget('theme1_global_products'); // Products depend on categories
    }

    /**
     * Clear brand related cache
     */
    public static function clearBrandsCache()
    {
        Cache::forget('active_brands');
        Cache::forget('theme1_top_brands');
        Cache::forget('theme1_global_products'); // Products depend on brands
    }

    /**
     * Clear product related cache
     */
    public static function clearProductsCache()
    {
        Cache::forget('theme1_global_products');
    }

    /**
     * Clear slider related cache
     */
    public static function clearSlidersCache()
    {
        Cache::forget('theme1_sliders_only');
    }

    /**
     * Clear service related cache
     */
    public static function clearServicesCache()
    {
        Cache::forget('theme1_global_services');
    }

    /**
     * Clear menu related cache
     */
    public static function clearMenusCache()
    {
        Cache::forget('global_menus');
    }

    /**
     * Clear language related cache
     */
    public static function clearLanguagesCache()
    {
        Cache::forget('default_language');
        Cache::forget('website_languages');
    }

    /**
     * Clear currency related cache
     */
    public static function clearCurrenciesCache()
    {
        Cache::forget('all_currencies');
    }

    /**
     * Clear page related cache
     */
    public static function clearPagesCache()
    {
        Cache::forget('footer_pages');
    }

    /**
     * Clear posts related cache
     */
    public static function clearPostsCache()
    {
        Cache::forget('theme1_recent_posts');
    }
}
