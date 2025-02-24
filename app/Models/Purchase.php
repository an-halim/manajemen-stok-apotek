<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'supplier_purchases';

    protected $fillable = [
        'supplier_id',
        'product_id',
        'purchase_date',
        'payment_method',
    ];

    // Relationship: A purchase belongs to a supplier
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relationship: A purchase is linked to a batch
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'purchase_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id');
    }
}
