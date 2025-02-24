<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('products')->insert([
            [
                'name' => 'Paracetamol 500mg',
                'category_id' => 1, // Medicine
                'unit' => 'Tablets',
                'description' => 'Pain reliever and fever reducer.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ibuprofen 400mg',
                'category_id' => 1, // Medicine
                'unit' => 'Tablets',
                'description' => 'Anti-inflammatory painkiller.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cough Syrup 100ml',
                'category_id' => 1, // Medicine
                'unit' => 'Bottle',
                'description' => 'Relieves cough and throat irritation.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Vitamin C 1000mg',
                'category_id' => 2, // Supplements
                'unit' => 'Capsules',
                'description' => 'Immune system booster.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Calcium Tablets 500mg',
                'category_id' => 2, // Supplements
                'unit' => 'Tablets',
                'description' => 'Supports bone health.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Thermometer',
                'category_id' => 3, // Equipment
                'unit' => 'Piece',
                'description' => 'Digital thermometer for body temperature measurement.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Blood Pressure Monitor',
                'category_id' => 3, // Equipment
                'unit' => 'Piece',
                'description' => 'Automatic digital BP monitor for home use.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hand Sanitizer 500ml',
                'category_id' => 4, // Personal Care
                'unit' => 'Bottle',
                'description' => 'Kills 99.9% of germs without water.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Face Mask (50 pcs)',
                'category_id' => 4, // Personal Care
                'unit' => 'Box',
                'description' => 'Disposable 3-layer protective masks.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
