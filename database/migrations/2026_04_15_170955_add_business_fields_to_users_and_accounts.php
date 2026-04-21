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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('commission_percentage', 5, 2)->default(0)->after('role');
        });

        // For MySQL, we can update the ENUM
        DB::statement("ALTER TABLE accounts MODIFY COLUMN type ENUM('customer', 'supplier', 'general', 'agent', 'director') NOT NULL DEFAULT 'general'");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('commission_percentage');
        });
        
        DB::statement("ALTER TABLE accounts MODIFY COLUMN type ENUM('customer', 'supplier', 'general') NOT NULL DEFAULT 'general'");
    }
};
