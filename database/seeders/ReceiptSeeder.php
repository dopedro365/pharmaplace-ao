<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Receipt;
use Illuminate\Database\Seeder;

class ReceiptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se existem pedidos
        $orders = Order::all();
        
        if ($orders->isEmpty()) {
            $this->command->warn('Não há pedidos para criar recibos.');
            return;
        }

        // Criar recibos para pedidos pagos
        $paidOrders = Order::where('payment_status', 'paid')->get();

        foreach ($paidOrders as $order) {
            Receipt::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'receipt_number' => 'REC-' . date('Y') . '-' . str_pad($order->id, 8, '0', STR_PAD_LEFT),
                ],
                [
                    'amount' => $order->total ?? 1000, // Fallback se total não existir
                    'currency' => 'AOA',
                    'issued_at' => $order->created_at ? $order->created_at->addMinutes(rand(5, 120)) : now(),
                    'payment_method' => $order->payment_method ?? 'phasmapay',
                    'status' => 'issued',
                ]
            );
        }

        // Criar alguns recibos para pedidos reembolsados
        $refundedOrders = Order::where('payment_status', 'refunded')->take(3)->get();

        foreach ($refundedOrders as $order) {
            // Recibo original
            Receipt::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'receipt_number' => 'REC-' . date('Y') . '-' . str_pad($order->id, 8, '0', STR_PAD_LEFT),
                ],
                [
                    'amount' => $order->total ?? 1000,
                    'currency' => 'AOA',
                    'issued_at' => $order->created_at ? $order->created_at->addMinutes(rand(5, 60)) : now(),
                    'payment_method' => $order->payment_method ?? 'phasmapay',
                    'status' => 'refunded',
                ]
            );

            // Recibo de reembolso
            Receipt::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'receipt_number' => 'REF-' . date('Y') . '-' . str_pad($order->id, 8, '0', STR_PAD_LEFT),
                ],
                [
                    'amount' => -($order->total ?? 1000), // Valor negativo para reembolso
                    'currency' => 'AOA',
                    'issued_at' => $order->created_at ? $order->created_at->addDays(rand(1, 10)) : now(),
                    'payment_method' => $order->payment_method ?? 'phasmapay',
                    'status' => 'issued',
                ]
            );
        }
    }
}
