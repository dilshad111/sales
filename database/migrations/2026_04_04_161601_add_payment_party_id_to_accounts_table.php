<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignId('payment_party_id')->nullable()->constrained()->nullOnDelete();
        });

        // Auto-create accounts for existing payment parties
        $parties = DB::table('payment_parties')->get();
        foreach ($parties as $party) {
            // Check if account already exists with same name to avoid duplicates
            $exists = DB::table('accounts')->where('payment_party_id', $party->id)->exists();
            if (!$exists) {
                DB::table('accounts')->insert([
                    'name' => $party->name,
                    'type' => 'supplier', // Payment parties are generally suppliers/other parties
                    'payment_party_id' => $party->id,
                    'status' => $party->status ?? 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_party_id');
        });
    }
};
