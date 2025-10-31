<?php

namespace App\Observers;

use App\Helpers\CacheHelper;
use App\Models\Setting;

class SettingObserver
{
    /**
     * Handle the Setting "created" event.
     */
    public function created(Setting $setting): void
    {
        CacheHelper::clearSettingsCache();
    }

    /**
     * Handle the Setting "updated" event.
     */
    public function updated(Setting $setting): void
    {
        CacheHelper::clearSettingsCache();
    }

    /**
     * Handle the Setting "deleted" event.
     */
    public function deleted(Setting $setting): void
    {
        CacheHelper::clearSettingsCache();
    }
}
