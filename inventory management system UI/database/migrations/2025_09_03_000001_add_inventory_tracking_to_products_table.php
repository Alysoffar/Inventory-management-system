<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('minimum_stock_level')->default(10);
            $table->integer('maximum_stock_level')->default(1000);
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('auto_reorder')->default(true);
            $table->integer('reorder_quantity')->default(50);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->string('supplier_id')->nullable();
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->timestamp('last_restocked_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'minimum_stock_level',
                'maximum_stock_level',
                'location',
                'latitude',
                'longitude',
                'auto_reorder',
                'reorder_quantity',
                'cost_price',
                'supplier_id',
                'status',
                'last_restocked_at'
            ]);
        });
    }
};
