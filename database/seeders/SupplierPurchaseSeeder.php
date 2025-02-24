<?php

namespace Database\Seeders;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierPurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $purchase = Purchase::create([
            'supplier_id' => 1, // Ensure supplier exists
            'purchase_date' => now(),
            'payment_method' => 'cash'
        ]);

        // Add items to the purchase
        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => 1, // Ensure product exists
            'quantity_purchased' => 100,
            'purchase_price' => 50.00,
            'expiry_date' => now()->addMonths(12),
            'batch_code' => 'BATCH-001',
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => 2, // Ensure product exists
            'quantity_purchased' => 50,
            'purchase_price' => 25.00,
            'expiry_date' => now()->addMonths(6),
            'batch_code' => 'BATCH-002',
        ]);

        $purchase = Purchase::create([
            'supplier_id' => 2, // Ensure supplier exists
            'purchase_date' => now(),
            'payment_method' => 'cash'
        ]);

        // Add items to the purchase
        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => 1, // Ensure product exists
            'quantity_purchased' => 100,
            'purchase_price' => 50.00,
            'expiry_date' => now()->addMonths(12),
            'batch_code' => 'BATCH-001',
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => 2, // Ensure product exists
            'quantity_purchased' => 50,
            'purchase_price' => 25.00,
            'expiry_date' => now()->addMonths(6),
            'batch_code' => 'BATCH-002',
        ]);
    }
}
