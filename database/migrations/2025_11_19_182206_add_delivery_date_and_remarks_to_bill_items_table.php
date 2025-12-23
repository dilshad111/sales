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
        Schema::table('bill_items', function (Blueprint $table) {
            if (!Schema::hasColumn('bill_items', 'delivery_date')) {
                $table->date('delivery_date')->nullable()->after('total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_items', function (Blueprint $table) {
            if (Schema::hasColumn('bill_items', 'delivery_date')) {
                $table->dropColumn('delivery_date');
            }
        });
    }
};
