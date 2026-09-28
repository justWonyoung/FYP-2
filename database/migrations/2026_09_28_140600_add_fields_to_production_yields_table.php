<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_yields', function (Blueprint $table) {


            $table->unsignedBigInteger('production_id')
                  ->after('id');


            $table->integer('planned_quantity')
                  ->after('production_id');


            $table->integer('actual_quantity')
                  ->after('planned_quantity');


            $table->decimal(
                'yield_percentage',
                5,
                2
            )
            ->after('actual_quantity');



            $table->foreign('production_id')
                  ->references('production_id')
                  ->on('productions')
                  ->cascadeOnDelete();


        });
    }



    public function down(): void
    {
        Schema::table('production_yields', function (Blueprint $table) {


            $table->dropForeign([
                'production_id'
            ]);


            $table->dropColumn([
                'production_id',
                'planned_quantity',
                'actual_quantity',
                'yield_percentage'
            ]);

        });
    }
};