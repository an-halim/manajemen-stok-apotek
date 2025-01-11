<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('suppliers')->insert([
            [
                'supplier_name' => 'Pharma Supplier A',
                'contact_person' => 'John Doe',
                'email' => 'supplier_a@example.com',
                'phone_number' => '1234567890',
                'address' => '123 Main St, City A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supplier_name' => 'Medical Supplies B',
                'contact_person' => 'Jane Smith',
                'email' => 'supplier_b@example.com',
                'phone_number' => '0987654321',
                'address' => '456 High St, City B',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
