<?php

namespace App\Observers;

use App\Helpers\CacheHelper;
use App\Models\ExtraSetting;

class ExtraSettingObserver
{
    /**
     * Handle the ExtraSetting "created" event.
     */
    public function created(ExtraSetting $extraSetting): void
    {
        CacheHelper::clearSettingsCache();
    }

    /**
     * Handle the ExtraSetting "updated" event.
     */
    public function updated(ExtraSetting $extraSetting): void
    {
        CacheHelper::clearSettingsCache();
    }

    /**
     * Handle the ExtraSetting "deleted" event.
     */
    public function deleted(ExtraSetting $extraSetting): void
    {
        CacheHelper::clearSettingsCache();
    }
}
