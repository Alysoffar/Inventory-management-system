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
        Schema::table('purchases', function (Blueprint $table) {
            $table->date('order_date')->nullable()->after('supplier_id');
            $table->date('expected_date')->nullable()->after('order_date');
            $table->text('notes')->nullable()->after('status');
            $table->dropColumn('purchase_date'); // Remove old column and use order_date instead
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['order_date', 'expected_date', 'notes']);
            $table->date('purchase_date')->after('supplier_id'); // Add back the old column
        });
    }
};
