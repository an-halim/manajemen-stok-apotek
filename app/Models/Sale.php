<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'customer_sales';

    protected $fillable = [
        'sale_date',
        'invoice_number',
        'customer_name',
        'payment_method',
        'medicine_redeemtion',
        'prescriber',
        'instructions',
        'remarks',
        'prescription'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            foreach ($sale->items as $item) {
                $remainingQuantity = $item->sale_quantity;

                $inventories = Inventory::select('inventory.*', 'purchase_items.expiry_date')
                    ->join('purchase_items', 'inventory.purchase_id', '=', 'purchase_items.purchase_id')
                    ->where('inventory.product_id', $item->product_id)
                    ->where('inventory.quantity_available', '>', 0)
                    ->orderBy('purchase_items.expiry_date')
                    ->orderBy('inventory.created_at')
                    ->get();

                $totalAvailable = $inventories->sum('quantity_available');
                if ($totalAvailable < $remainingQuantity) {
                    // ✅ Show notification on Filament
                    Notification::make()
                        ->title('Inventory Error 🚫')
                        ->body("Insufficient inventory for product ID: {$item->product_id}. Sale creation stopped.")
                        ->danger()
                        ->persistent()
                        ->send();

                    // ⛔ Prevent creation with validation error
                    throw ValidationException::withMessages([
                        'items' => "Insufficient inventory for product ID: {$item->product_id}.",
                    ]);
                }
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public static function generateInvoiceNumber()
    {
        return 'INV-' . now()->format('YmdHis');
    }

    public static function getTotalEarnings($startDate = null, $endDate = null)
    {
        return self::query()
            ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
            ->withSum('items as total_earning', \DB::raw('sale_quantity * selling_price'))
            ->get()
            ->sum('total_earning');
    }
}
