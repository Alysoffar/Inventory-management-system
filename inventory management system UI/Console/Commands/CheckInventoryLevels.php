<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\MonitorInventoryLevels;

class CheckInventoryLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:check-levels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check inventory levels and trigger auto-restock for low stock products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting inventory level check...');
        
        // Dispatch the job
        MonitorInventoryLevels::dispatch();
        
        $this->info('Inventory monitoring job has been dispatched.');
        $this->info('Check the logs for detailed results.');
        
        return 0;
    }
}
