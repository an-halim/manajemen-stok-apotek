<?php

namespace App\Models;

use App\Filament\Clusters\Product;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $table = 'sale_items';

    protected $fillable = [
        'sale_id',
        'product_id',
        'inventory_id',
        'sale_quantity',
        'selling_price',
        'remarks',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $remainingQuantity = $item->sale_quantity;

            $inventories = Inventory::select('inventory.*', 'purchase_items.expiry_date')
                ->join('purchase_items', 'inventory.purchase_id', '=', 'purchase_items.purchase_id')
                ->where('inventory.product_id', $item->product_id)
                ->where('inventory.quantity_available', '>', 0)
                ->orderBy('purchase_items.expiry_date')
                ->orderBy('inventory.created_at')
                ->lockForUpdate() // Prevent race conditions
                ->get();

            $totalAvailable = $inventories->sum('quantity_available');
            if ($totalAvailable < $remainingQuantity) {
                Notification::make()
                ->title('Inventory Alert ⚠️')
                ->body("Insufficient inventory for product ID: {$item->product_id}. Please restock.")
                ->danger() // Shows as a red alert
                ->persistent() // Stays visible until dismissed
                ->send();

                return false; // Prevents creation if insufficient inventory
            }

            // Allocate inventory without recursive create
            foreach ($inventories as $inventory) {
                if ($remainingQuantity <= 0) break;

                $allocatable = min($inventory->quantity_available, $remainingQuantity);
                $inventory->quantity_available -= $allocatable;
                $inventory->save();

                // Set inventory_id and sale_quantity on the same item (prevent recursion)
                $item->inventory_id = $inventory->id;
                $item->sale_quantity = $allocatable;

                $remainingQuantity -= $allocatable;
            }

            if ($remainingQuantity > 0) {
                throw new \Exception("Insufficient inventory for product ID {$item->product_id}");
            }
        });
    }


    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    public function batch()
    {
        return $this->belongsTo(Inventory::class);
    }

    public static function getMonthlySalesData($months = 3)
    {
        return self::selectRaw('MONTH(created_at) as month, SUM(sale_quantity) as total_out')
            ->whereBetween('created_at', [Carbon::now()->subMonths($months)->startOfMonth(), Carbon::now()->endOfMonth()])
            ->groupBy('month')
            ->pluck('total_out', 'month'); // ['9' => 40, '10' => 25]
    }

    public static function getTopSellingProducts($limit = 5)
    {
        return self::selectRaw('product_id, SUM(sale_quantity) as total_sold')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->with('product') // Eager load product details
            ->get()
            ->map(function ($saleItem) {
                return [
                    'product_name' => $saleItem->product?->name ?? 'Unknown',
                    'total_sold' => $saleItem->total_sold,
                ];
            });
    }

}
