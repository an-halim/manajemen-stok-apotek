<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $casts = [
        'images' => 'array',
        'status' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'description',
        'unit',
        'images',
        'category_id',
        'status',
    ];

      // Define the relationship with categories
      public function category()
      {
          return $this->belongsTo(Category::class);
      }

    // Relationship: A product can have many batches
    public function batches(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }


     /**
     * Search for a product by partial keyword (case-insensitive)
     * and return the first batch based on FIFO and nearest expiry.
     *
     * @param string $keyword
     * @return \App\Models\Batch|null
     */
    public static function getBatchForSale(string $keyword)
    {
        return self::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($keyword) . '%']) // Case-insensitive partial match
            ->with(['batches' => function ($query) {
                $query->where('quantity', '>', 0) // Ensure stock is available
                      ->orderBy('purchase_date', 'asc') // FIFO: Earliest purchase first
                      ->orderBy('expiry_date', 'asc'); // Nearest expiry date
            }])
            ->first() // Take the first product match
            ?->batches // Fetch related batches
            ->first(); // Return the first suitable batch
    }

    /**
     * Get the total quantity of a product in stock.
     *
     * @return int
     */
    public function getStockQuantityAttribute(): int
    {
        return $this->batches->sum('quantity');
    }

    /**
     * Get the total purchase price of a product in stock.
     *
     * @return float
     */
    public function getStockPurchasePriceAttribute(): float
    {
        return $this->batches->sum(fn ($batch) => $batch->purchase_price * $batch->quantity);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

}
