<?php

namespace App\Observers;

use App\Helpers\CacheHelper;
use App\Models\Category;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        CacheHelper::clearCategoriesCache();
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        CacheHelper::clearCategoriesCache();
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        CacheHelper::clearCategoriesCache();
    }
}
