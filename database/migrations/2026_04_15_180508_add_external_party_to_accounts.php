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
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('type')->change(); // From enum to string for flexibility (director, friend, agent, etc.)
            $table->foreignId('external_party_id')->nullable()->after('agent_id')->constrained('external_parties')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['external_party_id']);
            $table->dropColumn('external_party_id');
        });
    }
};
