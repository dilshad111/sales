<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['customer', 'supplier', 'general'])->default('general');
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('customer_id');
        });

        // Auto-create accounts for existing customers
        $customers = DB::table('customers')->get();
        foreach ($customers as $customer) {
            DB::table('accounts')->insert([
                'name' => $customer->name,
                'type' => 'customer',
                'customer_id' => $customer->id,
                'phone' => $customer->phone ?? null,
                'email' => $customer->email ?? null,
                'address' => $customer->address ?? null,
                'opening_balance' => $customer->opening_balance ?? 0,
                'status' => $customer->status ?? 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
