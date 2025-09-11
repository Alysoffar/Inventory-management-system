<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'total_amount',
        'status',
        'order_date',
        'expected_date',
        'received_date',
        'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'order_date' => 'date',
        'expected_date' => 'date',
        'received_date' => 'date'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'purchase_items')
                    ->withPivot('quantity', 'unit_cost', 'total_cost')
                    ->withTimestamps();
    }
}
