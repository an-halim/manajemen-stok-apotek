<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customer_sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->string('customer_name');
            $table->date('sale_date');
            $table->string('payment_method');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_sales');
    }
};
