<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')->constrained()->onDelete('cascade');
            $table->string('municipality');
            $table->string('province');
            $table->string('zone_name')->nullable(); // Nome específico da zona
            $table->decimal('delivery_fee', 8, 2);
            $table->integer('delivery_time_minutes'); // Tempo estimado em minutos
            $table->decimal('minimum_order', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['pharmacy_id', 'municipality', 'province']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};
