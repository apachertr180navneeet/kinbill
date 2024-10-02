<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_book_items', function (Blueprint $table) {
            $table->string('sreturn', 255)->default('0')->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_book_items', function (Blueprint $table) {
            $table->dropColumn('sreturn');
        });
    }
};
