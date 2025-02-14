<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity_received',
        'quantity_available',
        'batch_code'
    ];

    // Relationship: A batch belongs to a product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    // Relationship: A batch belongs to a product
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // Relationship: to Purchase
    public function purchase(): HasMany
    {
        return $this->HasMany(Purchase::class, 'purchase_id');
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class, 'product_id', 'product_id');
    }

    public function getRouteKeyName(): string
    {
        return 'product_id';
    }
}
