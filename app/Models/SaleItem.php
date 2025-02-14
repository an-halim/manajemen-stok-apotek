<?php

namespace App\Models;

use App\Filament\Clusters\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $table = 'sale_items';

    protected $fillable = [
        'sale_id',
        'product_id',
        'batch_id',
        'sale_quantity',
        'selling_price',
    ];

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
}
