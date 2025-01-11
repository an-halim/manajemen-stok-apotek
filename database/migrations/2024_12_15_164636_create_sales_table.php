<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customer_sales', function (Blueprint $table) {
            $table->id('sale_id');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('batch_id')->constrained('inventory')->onDelete('cascade');
            $table->integer('sale_quantity');
            $table->decimal('selling_price', 10, 2);
            $table->date('sale_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_sales');
    }
};
