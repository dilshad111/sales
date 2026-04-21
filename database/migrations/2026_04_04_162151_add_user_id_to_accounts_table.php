<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        });

        // Auto-create accounts for existing Agents
        $agents = DB::table('users')->where('role', 'Agent')->get();
        foreach ($agents as $agent) {
            $exists = DB::table('accounts')->where('user_id', $agent->id)->exists();
            if (!$exists) {
                DB::table('accounts')->insert([
                    'name' => $agent->name,
                    'type' => 'general',
                    'user_id' => $agent->id,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
