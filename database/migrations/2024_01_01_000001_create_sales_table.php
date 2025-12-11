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
        if (!Schema::hasTable('sales')) {
            Schema::create('sales', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->nullable()->index();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->string('sale_type')->default('sale'); // sale | return
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('contact_id')->nullable()->index();
                $table->date('sale_date')->index();
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->decimal('discount', 12, 2)->default(0);
                $table->decimal('amount_received', 12, 2)->default(0);
                $table->decimal('profit_amount', 12, 2)->default(0);
                $table->string('status')->default('pending'); // completed | pending | refunded
                $table->string('payment_status')->default('pending'); // completed | pending | pending_mpesa
                $table->text('note')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Add foreign keys only if referenced tables exist
                if (Schema::hasTable('stores')) {
                    $table->foreign('store_id')->references('id')->on('stores')->onDelete('restrict');
                }
                if (Schema::hasTable('contacts')) {
                    $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
                }
                if (Schema::hasTable('users')) {
                    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

