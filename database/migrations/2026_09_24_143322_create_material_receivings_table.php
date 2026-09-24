<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_receivings', function (Blueprint $table) {
            $table->id('receiving_id');
            $table->unsignedBigInteger('purchase_request_id')->nullable();
            $table->string('material_name');
            $table->string('supplier_name');
            $table->decimal('quantity_received', 10, 2);
            $table->string('unit');
            $table->date('received_date');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_receivings');
    }
};