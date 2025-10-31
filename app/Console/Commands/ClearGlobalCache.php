<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\CacheHelper;

class ClearGlobalCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-global {--type= : Type of cache to clear (all, settings, categories, brands, products, sliders)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear global application cache with selective options';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type') ?? 'all';

        switch ($type) {
            case 'settings':
                CacheHelper::clearSettingsCache();
                $this->info('Settings cache cleared successfully!');
                break;
            case 'categories':
                CacheHelper::clearCategoriesCache();
                $this->info('Categories cache cleared successfully!');
                break;
            case 'brands':
                CacheHelper::clearBrandsCache();
                $this->info('Brands cache cleared successfully!');
                break;
            case 'products':
                CacheHelper::clearProductsCache();
                $this->info('Products cache cleared successfully!');
                break;
            case 'sliders':
                CacheHelper::clearSlidersCache();
                $this->info('Sliders cache cleared successfully!');
                break;
            case 'all':
            default:
                CacheHelper::clearAllGlobalCache();
                $this->info('All global cache cleared successfully!');
                break;
        }

        return 0;
    }
}
