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
            $table->decimal('paper_tax_rate', 5, 2)->default(18.00)->after('ups');
        });
    }

    public function down(): void
    {
        Schema::table('carton_costings', function (Blueprint $table) {
            $table->dropColumn('paper_tax_rate');
        });
    }
};
