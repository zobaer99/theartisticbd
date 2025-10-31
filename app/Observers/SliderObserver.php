<?php

namespace App\Observers;

use App\Helpers\CacheHelper;
use App\Models\Slider;

class SliderObserver
{
    /**
     * Handle the Slider "created" event.
     */
    public function created(Slider $slider): void
    {
        CacheHelper::clearSlidersCache();
    }

    /**
     * Handle the Slider "updated" event.
     */
    public function updated(Slider $slider): void
    {
        CacheHelper::clearSlidersCache();
    }

    /**
     * Handle the Slider "deleted" event.
     */
    public function deleted(Slider $slider): void
    {
        CacheHelper::clearSlidersCache();
    }
}
