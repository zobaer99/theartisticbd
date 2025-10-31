<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Slider;
use Illuminate\Support\Facades\Cache;

class CleanUnusedThemeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:clean-unused';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean unused theme data and optimize for theme1 only';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning unused theme data...');
        
        // Check for sliders not being used (theme2, theme3, theme4)
        $unusedSliders = Slider::whereIn('home_page', ['theme2', 'theme3', 'theme4'])->get();
        
        if ($unusedSliders->count() > 0) {
            $this->info("Found {$unusedSliders->count()} sliders for unused themes:");
            
            foreach ($unusedSliders as $slider) {
                $this->line("- ID: {$slider->id}, Theme: {$slider->home_page}, Title: '{$slider->title}'");
            }
            
            if ($this->confirm('Do you want to delete these unused theme sliders?')) {
                $deletedCount = Slider::whereIn('home_page', ['theme2', 'theme3', 'theme4'])->delete();
                $this->info("Deleted {$deletedCount} unused theme sliders.");
            }
        } else {
            $this->info('No unused theme sliders found.');
        }
        
        // Clear all old caches
        $this->info('Clearing old caches...');
        Cache::forget('global_sliders');
        Cache::forget('homepage_all_products');
        Cache::forget('campaign_items');
        Cache::forget('services');
        Cache::forget('recent_posts');
        Cache::forget('brands');
        Cache::forget('video_stories');
        Cache::forget('latest_categories');
        Cache::forget('global_products');
        Cache::forget('global_services');
        Cache::forget('top_brands');
        
        // Clear theme-specific caches
        for ($i = 2; $i <= 4; $i++) {
            Cache::forget("sliders_theme{$i}");
        }
        
        $this->info('Cleanup completed! System optimized for theme1 only.');
        
        // Show current theme1 sliders
        $theme1Sliders = Slider::where('home_page', 'theme1')->get();
        $this->info("Current theme1 sliders: {$theme1Sliders->count()}");
        
        foreach ($theme1Sliders as $slider) {
            $this->line("- ID: {$slider->id}, Title: '{$slider->title}', Details: '{$slider->details}'");
        }
    }
}
