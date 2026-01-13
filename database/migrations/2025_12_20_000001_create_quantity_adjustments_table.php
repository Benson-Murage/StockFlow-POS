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
        Schema::create('quantity_adjustments', function (Blueprint $table) {
            $table->id();

            // Link to batch and stock
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('stock_id');

            // Quantities
            $table->decimal('previous_quantity', 15, 4)->default(0);
            $table->decimal('adjusted_quantity', 15, 4);

            // Reason for adjustment
            $table->string('reason')->nullable();

            // Userstamps (compatible with App\Traits\Userstamps)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes / foreign keys
            $table->index('batch_id');
            $table->index('stock_id');
            $table->index('created_by');

            // Foreign keys are optional here to avoid migration order issues,
            // but you can enable them if all referenced tables exist.
            // $table->foreign('batch_id')->references('id')->on('product_batches')->cascadeOnDelete();
            // $table->foreign('stock_id')->references('id')->on('product_stocks')->cascadeOnDelete();
            // $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quantity_adjustments');
    }
};


