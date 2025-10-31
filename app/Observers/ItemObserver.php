<?php

namespace App\Observers;

use App\Helpers\CacheHelper;
use App\Models\Item;

class ItemObserver
{
    /**
     * Handle the Item "created" event.
     */
    public function created(Item $item): void
    {
        CacheHelper::clearProductsCache();
    }

    /**
     * Handle the Item "updated" event.
     */
    public function updated(Item $item): void
    {
        CacheHelper::clearProductsCache();
    }

    /**
     * Handle the Item "deleted" event.
     */
    public function deleted(Item $item): void
    {
        CacheHelper::clearProductsCache();
    }
}
