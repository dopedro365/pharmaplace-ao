<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Adicionar novas colunas se não existirem
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('payment_verified_at');
            }
            if (!Schema::hasColumn('orders', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('delivered_at');
            }
            if (!Schema::hasColumn('orders', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            }
        });

        // Atualizar enum de status para versão simplificada
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'payment_verification',
            'confirmed', 
            'delivered',
            'cancelled',
            'returned'
        ) DEFAULT 'payment_verification'");

        // Migrar status antigos para novos
        DB::statement("UPDATE orders SET status = 'confirmed' WHERE status IN ('preparing', 'ready_pickup', 'out_delivery')");
        DB::statement("UPDATE orders SET status = 'payment_verification' WHERE status = 'pending_payment'");
    }

    public function down(): void
    {
        // Reverter para status antigos
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'pending_payment', 
            'payment_verification', 
            'confirmed', 
            'preparing', 
            'ready_pickup', 
            'out_delivery', 
            'delivered', 
            'cancelled'
        ) DEFAULT 'pending_payment'");

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivered_at', 'cancelled_at', 'cancellation_reason']);
        });
    }
};
