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
        Schema::table('payment_parties', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('name');
            $table->string('email')->nullable()->after('phone');
            $table->string('address')->nullable()->after('email');
            $table->decimal('opening_balance', 15, 2)->default(0)->after('address');
        });

        // Data Migration: Copy from external_parties to payment_parties
        $externalParties = DB::table('external_parties')->get();
        foreach ($externalParties as $party) {
            $newId = DB::table('payment_parties')->insertGetId([
                'name' => $party->name,
                'phone' => $party->phone,
                'email' => $party->email,
                'address' => $party->address,
                'opening_balance' => $party->opening_balance ?? 0,
                'status' => $party->status,
                'created_at' => $party->created_at,
                'updated_at' => $party->updated_at,
            ]);

            // Update accounts to point to new payment_party_id
            DB::table('accounts')
                ->where('external_party_id', $party->id)
                ->update([
                    'payment_party_id' => $newId,
                    'external_party_id' => null,
                    'type' => 'payment_party' // Standardizing the type
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_parties', function (Blueprint $table) {
            $table->dropColumn(['phone', 'email', 'address', 'opening_balance']);
        });
    }
};
