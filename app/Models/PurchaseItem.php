<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity_purchased',
        'purchase_price',
        'expiry_date',
    ];

    public static function boot()
    {
        parent::boot();

        // Add a hook after a PurchaseItem is created
        static::created(function ($purchaseItem) {
            // Check if a batch for the same product and expiry date already exists
            $existing = Inventory::where('product_id', $purchaseItem->product_id)
                ->where('expiry_date', $purchaseItem->expiry_date)
                ->first();

            if ($existing) {
                // Update the quantity of the existing batch
                $existing->increment('quantity_received', $purchaseItem->quantity_purchased);
            } else {
                // Create a new batch
                Inventory::create([
                    'product_id' => $purchaseItem->product_id,
                    'quantity_received' => $purchaseItem->quantity_purchased,
                    'quantity_available' => $purchaseItem->quantity_purchased,
                    // 'purchase_price' => $purchaseItem->purchase_price,
                    'expiry_date' => $purchaseItem->expiry_date,
                    'purchase_date' => $purchaseItem->purchase->purchase_date,
                ]);
            }
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
}
