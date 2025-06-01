<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_sales', function (Blueprint $table) {
            $table->boolean('medicine_redeemtion')->default(false)->after('payment_method');
            $table->string('prescriber')->nullable()->after('medicine_redeemtion');
            $table->string('instructions')->nullable()->after('prescriber');
            $table->string('remarks')->nullable()->after('instructions');
            $table->string('prescription')->nullable()->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_sales', function (Blueprint $table) {
            //
        });
    }
};
