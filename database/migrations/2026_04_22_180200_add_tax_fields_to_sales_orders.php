<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->decimal('tax_percent', 5, 2)->default(0)->after('total_amount');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_percent');
            $table->decimal('grand_total', 15, 2)->default(0)->after('tax_amount');
        });
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn(['tax_percent', 'tax_amount', 'grand_total']);
        });
    }
};
