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
            $table->string('code')->nullable()->after('name');
            $table->foreignId('parent_id')->nullable()->after('code')->constrained('accounts')->nullOnDelete();
            $table->boolean('is_group')->default(false)->after('parent_id');
            // Rename existing 'type' to 'category' to avoid confusion with Accounting 'type'
            $table->renameColumn('type', 'category');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->enum('type', ['Asset', 'Liability', 'Income', 'Expense'])->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['code', 'parent_id', 'is_group', 'type']);
            if (Schema::hasColumn('accounts', 'category')) {
                $table->renameColumn('category', 'type');
            }
        });
    }
};
