<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'Medicine', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Supplements', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Equipment', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Personal Care', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
