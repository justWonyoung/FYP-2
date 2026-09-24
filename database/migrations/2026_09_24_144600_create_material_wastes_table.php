<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_wastes', function (Blueprint $table) {
            $table->id('waste_id');
            $table->unsignedBigInteger('production_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('quantity_wasted', 10, 2);
            $table->string('waste_reason')->nullable(); // e.g., Spillage, Machine residue, Packaging defect
            $table->timestamps();

            $table->foreign('production_id')->references('production_id')->on('productions')->onDelete('cascade');
            $table->foreign('material_id')->references('material_id')->on('materials')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_wastes');
    }
};