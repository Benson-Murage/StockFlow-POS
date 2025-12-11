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
        if (Schema::hasTable('product_batches') && !Schema::hasColumn('product_batches', 'discount_percentage')) {
            Schema::table('product_batches', function (Blueprint $table) {
                $table->decimal('discount_percentage', 5, 2)->nullable()->after('discount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_batches') && Schema::hasColumn('product_batches', 'discount_percentage')) {
            Schema::table('product_batches', function (Blueprint $table) {
                $table->dropColumn('discount_percentage');
            });
        }
    }
};
