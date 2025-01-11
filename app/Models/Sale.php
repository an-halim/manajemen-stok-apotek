<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'customer_sales';

    protected $primaryKey = 'sale_id';

    protected $fillable = [
        'batch_id',
        'quantity_sold',
        'total_price',
    ];


    // Relationship: A sale is linked to a batch
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
