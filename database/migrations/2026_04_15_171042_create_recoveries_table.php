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
        Schema::create('recoveries', function (Blueprint $table) {
            $table->id();
            $table->string('recovery_number')->unique();
            $table->foreignId('purchase_invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->decimal('commission_deducted', 15, 2)->default(0);
            $table->decimal('net_amount_transfered', 15, 2)->default(0);
            $table->foreignId('director_account_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recoveries');
    }
};
