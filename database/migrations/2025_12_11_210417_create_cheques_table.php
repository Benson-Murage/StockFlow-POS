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
        if (!Schema::hasTable('cheques')) {
            Schema::create('cheques', function (Blueprint $table) {
                $table->id();
                $table->string('cheque_number')->nullable();
                $table->date('cheque_date')->nullable();
                $table->string('name')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->date('issued_date')->nullable();
                $table->string('bank')->nullable();
                $table->string('status')->default('pending'); // pending, cleared, bounced, etc.
                $table->text('remark')->nullable();
                $table->string('direction')->nullable(); // incoming, outgoing
                $table->unsignedBigInteger('store_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Foreign keys
                if (Schema::hasTable('stores')) {
                    $table->foreign('store_id')->references('id')->on('stores')->onDelete('set null');
                }
                if (Schema::hasTable('users')) {
                    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                    $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};
