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
        Schema::table('carton_costings', function (Blueprint $table) {
            $table->decimal('separator_cost', 15, 4)->default(0)->after('paper_tax_rate');
            $table->decimal('honeycomb_cost', 15, 4)->default(0)->after('separator_cost');
        });
    }

    public function down(): void
    {
        Schema::table('carton_costings', function (Blueprint $table) {
            $table->dropColumn(['separator_cost', 'honeycomb_cost']);
        });
    }
};
