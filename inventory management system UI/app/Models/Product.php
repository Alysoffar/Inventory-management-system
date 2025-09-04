<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\LowStockAlert;
use App\Mail\AutoRestockNotification;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'price',
        'cost_price',
        'stock_quantity',
        'sku',
        'minimum_stock_level',
        'maximum_stock_level',
        'location',
        'latitude',
        'longitude',
        'auto_reorder',
        'reorder_quantity',
        'supplier_id',
        'status',
        'last_restocked_at'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'minimum_stock_level' => 'integer',
        'maximum_stock_level' => 'integer',
        'reorder_quantity' => 'integer',
        'auto_reorder' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'last_restocked_at' => 'datetime'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // Scopes
    public function scopeLowStock($query, $threshold = null)
    {
        return $query->whereRaw('stock_quantity <= minimum_stock_level');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAtLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    // Helper methods
    public function isLowStock()
    {
        return $this->stock_quantity <= $this->minimum_stock_level;
    }

    public function isOutOfStock()
    {
        return $this->stock_quantity <= 0;
    }

    public function needsRestock()
    {
        return $this->isLowStock() && $this->auto_reorder;
    }

    public function updateStock($quantity, $type = 'adjustment', $referenceType = null, $referenceId = null, $notes = null, $location = null)
    {
        $oldQuantity = $this->stock_quantity;
        $this->stock_quantity = $quantity;
        $this->save();

        // Log the inventory change
        InventoryLog::create([
            'product_id' => $this->id,
            'type' => $type,
            'quantity_before' => $oldQuantity,
            'quantity_after' => $quantity,
            'quantity_changed' => $quantity - $oldQuantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'location_name' => $location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);

        // Check if stock is low and send notification
        if ($this->isLowStock() && $oldQuantity > $this->minimum_stock_level) {
            $this->sendLowStockAlert();
        }

        return $this;
    }

    public function autoRestock()
    {
        if (!$this->needsRestock()) {
            return false;
        }

        $oldQuantity = $this->stock_quantity;
        $newQuantity = $this->stock_quantity + $this->reorder_quantity;
        $this->stock_quantity = $newQuantity;
        $this->last_restocked_at = now();
        $this->save();

        // Log the auto restock
        InventoryLog::create([
            'product_id' => $this->id,
            'type' => 'auto_restock',
            'quantity_before' => $oldQuantity,
            'quantity_after' => $newQuantity,
            'quantity_changed' => $this->reorder_quantity,
            'notes' => 'Automatic restock triggered due to low inventory',
            'location_name' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);

        // Send notification
        $this->sendAutoRestockNotification();

        return true;
    }

    public function sendLowStockAlert()
    {
        // Create notification record
        $notification = Notification::create([
            'type' => 'low_stock',
            'title' => 'Low Stock Alert',
            'message' => "Product '{$this->name}' is running low. Current stock: {$this->stock_quantity}, Minimum level: {$this->minimum_stock_level}",
            'data' => json_encode([
                'product_id' => $this->id,
                'current_stock' => $this->stock_quantity,
                'minimum_level' => $this->minimum_stock_level
            ])
        ]);

        // Send email
        try {
            Mail::to('alysoffar06@gmail.com')->send(new LowStockAlert($this));
            $notification->update([
                'email_sent_to' => 'alysoffar06@gmail.com',
                'email_sent_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send low stock alert email: ' . $e->getMessage());
        }
    }

    public function sendAutoRestockNotification()
    {
        // Create notification record
        $notification = Notification::create([
            'type' => 'auto_restock',
            'title' => 'Auto Restock Completed',
            'message' => "Product '{$this->name}' has been automatically restocked. New stock level: {$this->stock_quantity}",
            'data' => json_encode([
                'product_id' => $this->id,
                'restocked_quantity' => $this->reorder_quantity,
                'new_stock_level' => $this->stock_quantity
            ])
        ]);

        // Send email
        try {
            Mail::to('alysoffar06@gmail.com')->send(new AutoRestockNotification($this));
            $notification->update([
                'email_sent_to' => 'alysoffar06@gmail.com',
                'email_sent_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send auto restock notification email: ' . $e->getMessage());
        }
    }
}
