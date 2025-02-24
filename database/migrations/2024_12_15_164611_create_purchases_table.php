<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('supplier_purchases', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->date('purchase_date');
            $table->string('payment_method');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_purchases');
    }
};
