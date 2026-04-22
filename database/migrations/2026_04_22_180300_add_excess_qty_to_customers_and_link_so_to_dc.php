<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('excess_qty_percent', 5, 2)->default(0)->after('status');
        });

        Schema::table('delivery_challans', function (Blueprint $table) {
            $table->foreignId('sales_order_id')->nullable()->constrained('sales_orders')->onDelete('set null')->after('bill_id');
        });

        Schema::table('delivery_challan_items', function (Blueprint $table) {
            $table->foreignId('sales_order_item_id')->nullable()->constrained('sales_order_items')->onDelete('set null')->after('item_id');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('excess_qty_percent');
        });

        Schema::table('delivery_challans', function (Blueprint $table) {
            $table->dropForeign(['sales_order_id']);
            $table->dropColumn('sales_order_id');
        });

        Schema::table('delivery_challan_items', function (Blueprint $table) {
            $table->dropForeign(['sales_order_item_id']);
            $table->dropColumn('sales_order_item_id');
        });
    }
};
