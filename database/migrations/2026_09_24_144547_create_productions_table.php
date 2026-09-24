<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id('production_id');
            $table->string('batch_number')->unique();
            $table->string('customer_order_no');
            $table->string('product_name');
            $table->decimal('planned_output', 10, 2);
            $table->decimal('actual_output', 10, 2)->default(0.00);
            $table->string('unit')->default('pcs');
            $table->enum('status', ['in_progress', 'completed', 'cancelled'])->default('in_progress');
            $table->date('start_date');
            $table->date('completion_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};