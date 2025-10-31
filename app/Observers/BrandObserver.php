<?php

namespace App\Observers;

use App\Helpers\CacheHelper;
use App\Models\Brand;

class BrandObserver
{
    /**
     * Handle the Brand "created" event.
     */
    public function created(Brand $brand): void
    {
        CacheHelper::clearBrandsCache();
    }

    /**
     * Handle the Brand "updated" event.
     */
    public function updated(Brand $brand): void
    {
        CacheHelper::clearBrandsCache();
    }

    /**
     * Handle the Brand "deleted" event.
     */
    public function deleted(Brand $brand): void
    {
        CacheHelper::clearBrandsCache();
    }
}
