<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity_purchased',
        'purchase_price',
        'selling_price',
        'expiry_date',
        'batch_code',
    ];

    public static function boot()
    {
        parent::boot();

        // Add a hook after a PurchaseItem is created
        static::created(function ($purchaseItem) {
            Inventory::create([
                'purchase_id' => $purchaseItem->purchase_id,
                'product_id' => $purchaseItem->product_id,
                'quantity_received' => $purchaseItem->quantity_purchased,
                'quantity_available' => $purchaseItem->quantity_purchased,
                'batch_code' => $purchaseItem->batch_code,
            ]);
        });
    }


    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    /**
     * Define a relationship to Inventory using batch_code.
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class, 'purchase_id');
    }

    public static function getMonthlyPurchaseData($months = 3)
    {
        return self::selectRaw('MONTH(created_at) as month, SUM(quantity_purchased) as total_in')
            ->whereBetween('created_at', [Carbon::now()->subMonths($months)->startOfMonth(), Carbon::now()->endOfMonth()])
            ->groupBy('month')
            ->pluck('total_in', 'month'); // ['9' => 50, '10' => 30]
    }
}
