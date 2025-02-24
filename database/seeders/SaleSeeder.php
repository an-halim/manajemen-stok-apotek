<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $purchase = Sale::create([
            'sale_date' => now(),
            'invoice_number' => Sale::generateInvoiceNumber(),
            'customer_name' => 'John Doe',
        ]);

        // Add items to the purchase
        SaleItem::create([
            'sale_id' => $purchase->id,
            'product_id' => 1, // Ensure product exists
            'inventory_id' => 1, // Ensure inventory exists
            'sale_quantity' => 100,
            'selling_price' => 50.00,
        ]);

        SaleItem::create([
            'sale_id' => $purchase->id,
            'product_id' => 2,
            'inventory_id' => 2,
            'sale_quantity' => 100,
            'selling_price' => 50.00,
        ]);

        $purchase = Sale::create([
            'sale_date' => now(),
            'invoice_number' => Sale::generateInvoiceNumber(),
            'customer_name' => 'Jane Smith',
        ]);

        // Add items to the purchase
        SaleItem::create([
            'sale_id' => $purchase->id,
            'product_id' => 1, // Ensure product exists
            'inventory_id' => 1, // Ensure inventory exists
            'sale_quantity' => 100,
            'selling_price' => 50.00,
        ]);
    }
}
