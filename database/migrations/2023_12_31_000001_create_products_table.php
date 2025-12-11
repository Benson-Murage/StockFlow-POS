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
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('sku')->nullable();
                $table->string('barcode')->nullable();
                $table->string('image_url')->nullable();
                $table->string('unit')->nullable();
                $table->decimal('quantity', 12, 2)->default(0);
                $table->decimal('alert_quantity', 12, 2)->default(0);
                $table->boolean('is_stock_managed')->default(true);
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('brand_id')->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->decimal('discount', 12, 2)->default(0);
                $table->boolean('is_featured')->default(false);
                $table->string('product_type')->nullable();
                $table->text('meta_data')->nullable();
                $table->unsignedBigInteger('attachment_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
                // Foreign keys can be skipped; uncomment if required tables exist.
                // $table->foreign('brand_id')->references('id')->on('brands')->nullOnDelete();
                // $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
                // $table->foreign('attachment_id')->references('id')->on('attachments')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
