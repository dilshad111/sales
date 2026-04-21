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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('recipient_account_id')->nullable()->after('payment_party_id')->constrained('accounts')->nullOnDelete();
            $table->string('destination_type')->default('cash_bank')->after('recipient_account_id'); // cash_bank, director, friend
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['recipient_account_id']);
            $table->dropColumn(['recipient_account_id', 'destination_type']);
        });
    }
};
