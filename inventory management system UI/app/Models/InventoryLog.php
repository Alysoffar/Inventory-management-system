<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'quantity_before',
        'quantity_after',
        'quantity_changed',
        'reference_type',
        'reference_id',
        'notes',
        'user_id',
        'latitude',
        'longitude',
        'location_name'
    ];

    protected $casts = [
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
        'quantity_changed' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope for different log types
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'sale' => 'red',
            'purchase', 'auto_restock', 'manual_restock' => 'green',
            'adjustment' => 'blue',
            'return' => 'yellow',
            'damaged', 'expired' => 'orange',
            default => 'gray'
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'sale' => '📤',
            'purchase', 'auto_restock', 'manual_restock' => '📥',
            'adjustment' => '⚙️',
            'return' => '↩️',
            'damaged' => '🔧',
            'expired' => '⏰',
            default => '📋'
        };
    }
}
