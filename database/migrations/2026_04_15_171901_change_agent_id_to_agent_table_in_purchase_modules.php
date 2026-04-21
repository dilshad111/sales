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
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('set null');
        });

        Schema::table('recoveries', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('recoveries', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
