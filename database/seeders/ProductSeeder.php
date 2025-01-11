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
                'name' => 'Vitamin C 1000mg',
                'category_id' => 2, // Supplements
                'unit' => 'Capsules',
                'description' => 'Immune system booster.',
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
        ]);
    }
}
