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
        Schema::create('purchase_delivery_challans', function (Blueprint $table) {
            $table->id();
            $table->string('challan_number')->unique();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('vehicle_number')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status')->default('received');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('purchase_delivery_challan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_delivery_challan_id')->constrained('purchase_delivery_challans')->onDelete('cascade')->name('pdc_item_pdc_id_foreign');
            $table->foreignId('purchase_item_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 15, 2);
            $table->string('unit')->nullable();
            $table->decimal('rate', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_delivery_challan_items');
        Schema::dropIfExists('purchase_delivery_challans');
    }
};
