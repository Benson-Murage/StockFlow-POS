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
        Schema::create('mpesa_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id')->nullable()->index();
            $table->string('phone')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('pending'); // pending | success | failed
            $table->string('merchant_request_id')->nullable()->index();
            $table->string('checkout_request_id')->nullable()->index();
            $table->string('result_code')->nullable();
            $table->string('result_description')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        // Add foreign key constraint only if sales table exists
        if (Schema::hasTable('sales')) {
            Schema::table('mpesa_payments', function (Blueprint $table) {
                $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpesa_payments');
    }
};

