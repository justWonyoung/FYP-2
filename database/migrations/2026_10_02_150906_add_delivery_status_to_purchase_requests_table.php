
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Avoid adding the same column twice.
        if (!Schema::hasColumn('purchase_requests', 'delivery_status')) {

            Schema::table('purchase_requests', function (Blueprint $table) {

                $table->enum('delivery_status', [
                    'pending',
                    'partial',
                    'received',
                ])
                ->default('pending')
                ->after('approval_status');

            });
        }
    }

    public function down(): void
    {
        // Keep this column because it is already defined
        // in the original purchase_requests migration.
        // This also protects existing delivery data.
    }
};
