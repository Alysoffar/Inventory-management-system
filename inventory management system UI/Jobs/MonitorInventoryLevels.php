<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class MonitorInventoryLevels implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting inventory level monitoring...');

        // Get all active products that need restocking
        $lowStockProducts = Product::active()
            ->whereRaw('stock_quantity <= minimum_stock_level')
            ->where('auto_reorder', true)
            ->get();

        Log::info('Found ' . $lowStockProducts->count() . ' products that need restocking');

        foreach ($lowStockProducts as $product) {
            try {
                Log::info("Processing auto restock for product: {$product->name} (ID: {$product->id})");
                
                // Check if product was already restocked recently (within last hour)
                $recentlyRestocked = $product->last_restocked_at && 
                    $product->last_restocked_at->diffInMinutes(now()) < 60;

                if (!$recentlyRestocked) {
                    $product->autoRestock();
                    Log::info("Auto restock completed for product: {$product->name}");
                } else {
                    Log::info("Product {$product->name} was recently restocked, skipping");
                }
            } catch (\Exception $e) {
                Log::error("Failed to restock product {$product->name}: " . $e->getMessage());
            }
        }

        // Also check for products that are low but don't have auto-reorder enabled
        $lowStockNoAutoReorder = Product::active()
            ->whereRaw('stock_quantity <= minimum_stock_level')
            ->where('auto_reorder', false)
            ->get();

        foreach ($lowStockNoAutoReorder as $product) {
            // Only send alert if we haven't sent one recently
            $recentAlert = \App\Models\Notification::where('type', 'low_stock')
                ->whereJsonContains('data->product_id', $product->id)
                ->where('created_at', '>=', now()->subHours(24))
                ->exists();

            if (!$recentAlert) {
                $product->sendLowStockAlert();
                Log::info("Low stock alert sent for product: {$product->name}");
            }
        }

        Log::info('Inventory level monitoring completed');
    }
}
