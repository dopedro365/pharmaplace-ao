<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')->constrained('pharmacies')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            
            $table->string('name');
            $table->string('slug'); // Slug agora será único por farmácia
            $table->text('description')->nullable();
            $table->text('composition')->nullable();
            $table->text('indications')->nullable();
            $table->text('contraindications')->nullable();
            $table->text('dosage')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('barcode')->nullable();
            $table->string('image')->nullable(); // Caminho da imagem (singular)
            $table->boolean('requires_prescription')->default(false);
            $table->boolean('is_controlled')->default(false);
            $table->boolean('is_active')->default(true); // Se o produto está ativo no catálogo da farmácia
            
            $table->decimal('price', 10, 2);
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_available')->default(true); // Se está disponível para venda (em estoque > 0 e ativo)

            $table->timestamps();
            $table->softDeletes();

            // Adicionar índice único composto para slug por farmácia
            $table->unique(['pharmacy_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
