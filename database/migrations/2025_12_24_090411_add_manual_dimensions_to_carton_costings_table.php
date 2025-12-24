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
            $table->decimal('deckle_size', 10, 2)->nullable()->after('height');
            $table->decimal('sheet_length_manual', 10, 2)->nullable()->after('deckle_size');
        });
    }

    public function down(): void
    {
        Schema::table('carton_costings', function (Blueprint $table) {
            $table->dropColumn(['deckle_size', 'sheet_length_manual']);
        });
    }
};
