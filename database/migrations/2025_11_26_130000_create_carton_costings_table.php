<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carton_costings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('fefco_code');
            $table->unsignedTinyInteger('ply');
            $table->decimal('length', 10, 2);
            $table->decimal('width', 10, 2);
            $table->decimal('height', 10, 2);
            $table->decimal('wastage_rate', 5, 2);
            $table->decimal('overhead_rate', 5, 2);
            $table->decimal('profit_rate', 5, 2);
            $table->decimal('sheet_width', 10, 2);
            $table->decimal('sheet_length', 10, 2);
            $table->decimal('sheet_width_m', 12, 4);
            $table->decimal('sheet_length_m', 12, 4);
            $table->decimal('sheet_area', 15, 6);
            $table->decimal('total_paper_cost', 15, 4);
            $table->decimal('wastage_amount', 15, 4);
            $table->decimal('cost_after_wastage', 15, 4);
            $table->decimal('overhead_amount', 15, 4);
            $table->decimal('cost_before_profit', 15, 4);
            $table->decimal('profit_amount', 15, 4);
            $table->decimal('final_carton_cost', 15, 4);
            $table->longText('layers');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carton_costings');
    }
};
