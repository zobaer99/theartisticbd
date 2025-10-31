<?php

namespace App\Observers;

use App\Helpers\CacheHelper;
use App\Models\Menu;

class MenuObserver
{
    /**
     * Handle the Menu "created" event.
     */
    public function created(Menu $menu): void
    {
        CacheHelper::clearMenusCache();
    }

    /**
     * Handle the Menu "updated" event.
     */
    public function updated(Menu $menu): void
    {
        CacheHelper::clearMenusCache();
    }

    /**
     * Handle the Menu "deleted" event.
     */
    public function deleted(Menu $menu): void
    {
        CacheHelper::clearMenusCache();
    }
}
