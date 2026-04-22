<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('so_number')->unique();
            $table->string('po_number')->nullable();
            $table->date('po_date')->nullable();
            $table->date('so_date');
            $table->foreignId('customer_id')->constrained('customers');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status')->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->date('delivery_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
    }
};
