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
        if (Schema::hasTable('sales') && !Schema::hasColumn('sales', 'deleted_by')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->unsignedBigInteger('deleted_by')->nullable()->after('created_by');
                if (Schema::hasTable('users')) {
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
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'deleted_by')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('deleted_by');
            });
        }
    }
};
