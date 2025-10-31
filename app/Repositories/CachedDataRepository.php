<?php

namespace App\Repositories;

use App\Models\Item;
use App\Models\CampaignItem;
use App\Models\Brand;
use App\Models\Post;
use App\Models\Service;
use App\Models\Slider;

class CachedDataRepository
{
    /**
     * Get all homepage data with aggressive caching
     */
    public static function getHomepageData()
    {
        return cache()->remember('complete_homepage_data', 900, function () {
            return [
                'products' => Item::with(['category', 'subcategory', 'brand', 'reviews'])
                    ->whereStatus(1)
                    ->orderBy('id', 'DESC')
                    ->limit(100)
                    ->get(),
                'campaignItems' => CampaignItem::with(['item.category', 'item.reviews'])
                    ->whereStatus(1)
                    ->whereIsFeature(1)
                    ->orderby('id', 'desc')
                    ->limit(20)
                    ->get(),
                'brands' => Brand::orderby('id', 'desc')->limit(12)->get(),
                'posts' => Post::orderby('id', 'desc')->limit(6)->get(),
                'services' => Service::orderby('id', 'desc')->get(),
            ];
        });
    }

    /**
     * Get products by type from cached data
     */
    public static function getProductsByType($products, $type, $limit = 20)
    {
        return $products->where('is_type', $type)->take($limit);
    }

    /**
     * Get flash deal products from cached data
     */
    public static function getFlashDealProducts($products, $limit = 20)
    {
        return $products->where('is_type', 'flash_deal')
            ->whereNotNull('date')
            ->take($limit);
    }
}
