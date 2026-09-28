<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_order_items', function (Blueprint $table) {

            $table->unsignedBigInteger('customer_order_id')
                  ->after('id');

            $table->string('product_name')
                  ->after('customer_order_id');

            $table->integer('quantity')
                  ->after('product_name');

            $table->decimal('price', 10, 2)
                  ->after('quantity');

            $table->decimal('subtotal', 10, 2)
                  ->after('price');

            $table->foreign('customer_order_id')
                  ->references('id')
                  ->on('customer_orders')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('customer_order_items', function (Blueprint $table) {

            $table->dropForeign([
                'customer_order_id'
            ]);

            $table->dropColumn([
                'customer_order_id',
                'product_name',
                'quantity',
                'price',
                'subtotal'
            ]);
        });
    }
};