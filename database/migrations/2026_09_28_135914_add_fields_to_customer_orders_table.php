<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {


            $table->unsignedBigInteger('customer_id')
                  ->after('id');


            $table->string('order_no')
                  ->unique()
                  ->after('customer_id');


            $table->date('order_date')
                  ->after('order_no');


            $table->enum(
                'status',
                [
                    'pending',
                    'production',
                    'completed',
                    'cancelled'
                ]
            )
            ->default('pending')
            ->after('order_date');


            $table->decimal(
                'total_amount',
                10,
                2
            )
            ->default(0)
            ->after('status');



            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->cascadeOnDelete();


        });
    }


    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {


            $table->dropForeign([
                'customer_id'
            ]);


            $table->dropColumn([
                'customer_id',
                'order_no',
                'order_date',
                'status',
                'total_amount'
            ]);

        });
    }
};