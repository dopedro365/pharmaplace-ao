<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('license_number')->unique();
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('municipality');
            $table->string('province');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();
            $table->json('opening_hours')->nullable(); // {"monday": "08:00-18:00", ...}
            $table->string('logo')->nullable();
            $table->json('images')->nullable(); // Array de imagens da farmácia
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('accepts_delivery')->default(true);
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->integer('delivery_time_minutes')->default(60); // Tempo estimado de entrega
            $table->decimal('minimum_order', 8, 2)->default(0);
            $table->timestamps();
            
            $table->index(['municipality', 'province']);
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacies');
    }
};
