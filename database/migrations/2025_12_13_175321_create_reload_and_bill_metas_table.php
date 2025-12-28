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
        if (!Schema::hasTable('reload_and_bill_metas')) {
            Schema::create('reload_and_bill_metas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sale_item_id')->nullable();
                $table->string('transaction_type')->nullable();
                $table->string('account_number')->nullable();
                $table->decimal('commission', 12, 2)->default(0);
                $table->decimal('additional_commission', 12, 2)->default(0);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reload_and_bill_metas');
    }
};