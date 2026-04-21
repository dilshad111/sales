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
        Schema::table('company_settings', function (Blueprint $table) {
            $table->string('bill_prefix')->default('BILL')->after('currency_symbol')->nullable();
            $table->string('challan_prefix')->default('DC')->after('bill_prefix')->nullable();
            $table->string('pv_prefix')->default('PV')->after('challan_prefix')->nullable();
            $table->string('rv_prefix')->default('RV')->after('pv_prefix')->nullable();
            $table->string('jv_prefix')->default('JV')->after('rv_prefix')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn(['bill_prefix', 'challan_prefix', 'pv_prefix', 'rv_prefix', 'jv_prefix']);
        });
    }
};
