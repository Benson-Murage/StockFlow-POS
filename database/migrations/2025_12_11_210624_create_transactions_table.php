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
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sales_id')->nullable()->index();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('contact_id')->nullable()->index();
                $table->timestamp('transaction_date')->index();
                $table->decimal('amount', 12, 2);
                $table->string('payment_method');
                $table->string('transaction_type')->nullable();
                $table->text('note')->nullable();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Add foreign keys only if referenced tables exist
                if (Schema::hasTable('sales')) {
                    $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade');
                }
                if (Schema::hasTable('stores')) {
                    $table->foreign('store_id')->references('id')->on('stores')->onDelete('restrict');
                }
                if (Schema::hasTable('contacts')) {
                    $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
                }
                if (Schema::hasTable('users')) {
                    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                    $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                    $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};