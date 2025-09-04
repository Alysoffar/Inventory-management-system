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
    
    // If you have sale items, uncomment this:
    // public function saleItems()
    // {
    //     return $this->hasMany(SaleItem::class);
    // }
}
