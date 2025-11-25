<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('product_name'); // Nome do produto no momento da compra
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->text('product_notes')->nullable();
            $table->timestamps();
            
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        // Remover a chave estrangeira 'order_id' antes de excluir a tabela
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);  // Remove a chave estrangeira para 'order_id'
            $table->dropForeign(['product_id']);  // Remove a chave estrangeira para 'product_id' se necessário
        });

        // Excluir a tabela 'order_items'
        Schema::dropIfExists('order_items');
    }
};
