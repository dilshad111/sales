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
            $table->json('flute_factors')->nullable()->after('layers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carton_costings', function (Blueprint $table) {
            $table->dropColumn('flute_factors');
        });
    }
};
