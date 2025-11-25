<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Recreates orders table with complete functionality:
     * - Multiple payment proofs support
     * - Simplified status system
     * - Enhanced payment verification
     * - Delivery management
     */
    public function up(): void
    {
        Schema::dropIfExists('orders');
        
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pharmacy_id')->constrained()->onDelete('cascade');
            
            $table->enum('status', [
                'payment_verification',
                'confirmed', 
                'delivered',
                'cancelled',
                'returned'
            ])->default('payment_verification');
            
            // Dados do cliente
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            
            // Endereço de entrega
            $table->enum('delivery_type', ['pickup', 'delivery']);
            $table->string('delivery_address')->nullable();
            $table->string('delivery_municipality')->nullable();
            $table->string('delivery_province')->nullable();
            $table->text('delivery_notes')->nullable();
            
            // Valores
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            $table->string('payment_method')->default('bank_transfer');
            $table->json('payment_proofs')->nullable(); // Array of proof file paths
            $table->decimal('total_proofs_amount', 10, 2)->nullable(); // Sum of all proof amounts
            $table->json('proofs_analysis')->nullable(); // Analysis results from service
            $table->json('payment_proofs_paths')->nullable(); // Detailed file paths
            $table->integer('total_proofs_count')->default(0);
            $table->decimal('verified_amount', 15, 2)->default(0);
            $table->boolean('payment_fully_verified')->default(false);
            $table->timestamp('payment_verified_at')->nullable();
            $table->text('payment_verification_notes')->nullable();
            $table->text('payment_notes')->nullable();
            
            $table->timestamp('estimated_delivery')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['pharmacy_id', 'status']);
            $table->index(['payment_method', 'payment_fully_verified']);
            $table->index(['pharmacy_id', 'payment_fully_verified']);
            $table->index('order_number');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    { // Remover a chave estrangeira antes de excluir a tabela
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign('order_items_order_id_foreign');  // Nome da chave estrangeira
        });

        // Excluir as tabelas
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');
    }
};
