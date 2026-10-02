<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id('purchase_request_id');
            $table->string('request_no')->unique();
            $table->string('customer_order_no');
            $table->string('supplier_name');
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->string('material_item');
$table->decimal('quantity', 8, 2);

$table->decimal('received_quantity', 8, 2)
      ->default(0);

$table->enum('delivery_status', [
    'pending',
    'partial',
    'received'
])
->default('pending');


$table->string('unit');

$table->decimal('estimated_cost', 10, 2);

$table->enum('finance_status', [
    'pending',
    'approved',
    'rejected'
])
->default('pending');


$table->enum('approval_status', [
    'pending',
    'approved',
    'rejected'
])
->default('pending');
            $table->text('finance_remark')->nullable();
            $table->text('admin_remark')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};