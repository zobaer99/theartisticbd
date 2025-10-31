<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CachedItem extends Model
{
    /**
     * Get popular products with caching
     */
    public static function getPopularProducts($limit = 20)
    {
        return cache()->remember('popular_products_' . $limit, 1800, function () use ($limit) {
            return Item::with(['category', 'subcategory', 'brand', 'reviews'])
                ->whereStatus(1)
                ->where('is_type', 'best')
                ->orderBy('id', 'DESC')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get new products with caching
     */
    public static function getNewProducts($limit = 20)
    {
        return cache()->remember('new_products_' . $limit, 1800, function () use ($limit) {
            return Item::with(['category', 'subcategory', 'brand', 'reviews'])
                ->whereStatus(1)
                ->where('is_type', 'new')
                ->orderBy('id', 'DESC')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get featured products with caching
     */
    public static function getFeaturedProducts($limit = 20)
    {
        return cache()->remember('featured_products_' . $limit, 1800, function () use ($limit) {
            return Item::with(['category', 'subcategory', 'brand', 'reviews'])
                ->whereStatus(1)
                ->where('is_type', 'feature')
                ->orderBy('id', 'DESC')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get top products with caching
     */
    public static function getTopProducts($limit = 20)
    {
        return cache()->remember('top_products_' . $limit, 1800, function () use ($limit) {
            return Item::with(['category', 'subcategory', 'brand', 'reviews'])
                ->whereStatus(1)
                ->where('is_type', 'top')
                ->orderBy('id', 'DESC')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get flash deal products with caching
     */
    public static function getFlashDealProducts($limit = 20)
    {
        return cache()->remember('flash_deal_products_' . $limit, 1800, function () use ($limit) {
            return Item::with(['category', 'subcategory', 'brand', 'reviews'])
                ->whereStatus(1)
                ->where('is_type', 'flash_deal')
                ->whereNotNull('date')
                ->orderBy('id', 'DESC')
                ->limit($limit)
                ->get();
        });
    }
}
