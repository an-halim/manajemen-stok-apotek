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
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn(['purchase_date', 'expiry_date']);
            $table->string('batch_code')->unique();
            $table->foreignId('purchase_id')->constrained('supplier_purchases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->date('purchase_date');
            $table->date('expiry_date');
            $table->dropForeign(['batch_code']);
            $table->dropColumn('batch_code');
        });
    }
};
