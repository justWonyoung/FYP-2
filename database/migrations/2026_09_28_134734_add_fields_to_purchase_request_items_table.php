<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_request_items', function (Blueprint $table) {


            $table->unsignedBigInteger('purchase_request_id')
                  ->after('id');


            $table->unsignedBigInteger('material_id')
                  ->after('purchase_request_id');


            $table->decimal(
                'quantity',
                10,
                2
            );


            $table->decimal(
                'unit_price',
                10,
                2
            )
            ->default(0);


            $table->decimal(
                'subtotal',
                10,
                2
            )
            ->default(0);



            $table->foreign('purchase_request_id')
                  ->references('purchase_request_id')
                  ->on('purchase_requests')
                  ->cascadeOnDelete();



            $table->foreign('material_id')
                  ->references('material_id')
                  ->on('materials')
                  ->cascadeOnDelete();


        });
    }


    public function down(): void
    {

        Schema::table('purchase_request_items', function (Blueprint $table) {


            $table->dropForeign([
                'purchase_request_id'
            ]);


            $table->dropForeign([
                'material_id'
            ]);


            $table->dropColumn([
                'purchase_request_id',
                'material_id',
                'quantity',
                'unit_price',
                'subtotal'
            ]);

        });

    }
};