<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pharmacy_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2); // Preço no momento da adição
            $table->text('notes')->nullable(); // Observações do cliente
            $table->timestamps();
            
            $table->unique(['user_id', 'pharmacy_id', 'product_id']);
            $table->index(['user_id', 'pharmacy_id']);
        });
    }

    public function down(): void
    {
        
        Schema::dropIfExists('cart_items');
    }
};
