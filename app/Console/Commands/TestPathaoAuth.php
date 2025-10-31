<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PathaoService;

class TestPathaoAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pathao:test-auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Pathao authentication and API calls';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Testing Pathao authentication...');
        
        try {
            $pathaoService = new PathaoService();
            
            // Test cities
            $this->info('Testing getCities...');
            $cities = $pathaoService->getCities();
            $this->info('Cities fetched: ' . count($cities) . ' cities');
            
            // Test with a real order
            $this->info('Testing with a real order...');
            $order = \App\Models\Order::latest()->first();
            
            if ($order) {
                $this->info('Testing order creation for Order #' . $order->id);
                $orderResult = $pathaoService->createOrder($order);
                $this->info('Order creation result: ' . json_encode($orderResult));
            } else {
                $this->warn('No orders found in database');
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
        }
        
        $this->info('Check Laravel logs for detailed authentication info.');
        
        return 0;
    }
}
