<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_usages', function (Blueprint $table) {
            $table->id('usage_id');
            $table->unsignedBigInteger('production_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('quantity_used', 10, 2);
            $table->timestamps();

            $table->foreign('production_id')->references('production_id')->on('productions')->onDelete('cascade');
            $table->foreign('material_id')->references('material_id')->on('materials')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_usages');
    }
};