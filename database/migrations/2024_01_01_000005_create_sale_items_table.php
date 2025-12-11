<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('sale_items')) {
            Schema::create('sale_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sale_id')->nullable();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->unsignedBigInteger('batch_id')->nullable();
                $table->decimal('quantity', 12, 2)->default(0);
                $table->decimal('unit_price', 12, 2)->default(0);
                $table->decimal('unit_cost', 12, 2)->default(0);
                $table->decimal('discount', 12, 2)->default(0);
                $table->date('sale_date')->nullable();
                $table->text('description')->nullable();
                $table->text('note')->nullable();
                $table->boolean('is_free')->default(false);
                $table->json('meta_data')->nullable();
                $table->decimal('flat_discount', 12, 2)->nullable();
                $table->decimal('free_quantity', 12, 2)->nullable();
                $table->string('item_type')->nullable();
                $table->unsignedBigInteger('charge_id')->nullable();
                $table->string('charge_type')->nullable();
                $table->decimal('rate_value', 12, 2)->nullable();
                $table->string('rate_type')->nullable();
                $table->decimal('base_amount', 12, 2)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
