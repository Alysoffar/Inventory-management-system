<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'sale_date',
        'total_amount',
        'status'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'sale_date' => 'date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, SaleItem::class, 'sale_id', 'id', 'id', 'product_id');
    }
}
