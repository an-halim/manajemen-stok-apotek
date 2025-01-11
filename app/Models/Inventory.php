<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'product_id',
        'quantity_received',
        'quantity_available',
        'purchase_date',
        'expiry_date',
        // 'quantity',
        // 'cost_price',
        // 'selling_price',
    ];

    // Relationship: A batch belongs to a product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
