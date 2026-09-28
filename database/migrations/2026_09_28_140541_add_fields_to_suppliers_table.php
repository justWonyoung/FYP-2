<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {

            $table->string('supplier_name')
                  ->after('id');

            $table->string('phone')
                  ->nullable()
                  ->after('supplier_name');

            $table->string('email')
                  ->nullable()
                  ->after('phone');

            $table->text('address')
                  ->nullable()
                  ->after('email');

        });
    }


    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {

            $table->dropColumn([
                'supplier_name',
                'phone',
                'email',
                'address'
            ]);

        });
    }
};