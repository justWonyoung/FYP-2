
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Avoid adding the same column twice.
        if (!Schema::hasColumn('purchase_requests', 'received_quantity')) {

            Schema::table('purchase_requests', function (Blueprint $table) {

                $table->decimal('received_quantity', 8, 2)
                    ->default(0)
                    ->after('quantity');

            });
        }
    }

    public function down(): void
    {
        // Keep this column because it is already defined
        // in the original purchase_requests migration.
        // This also protects existing receiving data.
    }
};
