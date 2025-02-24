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
        $sale = Sale::create([
            'sale_date' => now(),
            'invoice_number' => Sale::generateInvoiceNumber(),
            'customer_name' => 'John Doe',
            'payment_method' => 'cash'
        ]);

        // Add items to the sale
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => 1, // Ensure product exists
            'inventory_id' => 1, // Ensure inventory exists
            'sale_quantity' => 100,
            'selling_price' => 50.00,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => 2,
            'inventory_id' => 2,
            'sale_quantity' => 100,
            'selling_price' => 50.00,
        ]);

        $sale = Sale::create([
            'sale_date' => now(),
            'invoice_number' => Sale::generateInvoiceNumber(),
            'customer_name' => 'Jane Smith',
            'payment_method' => 'cash'
        ]);

        // Add items to the sale
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => 1, // Ensure product exists
            'inventory_id' => 1, // Ensure inventory exists
            'sale_quantity' => 100,
            'selling_price' => 50.00,
        ]);
    }
}
