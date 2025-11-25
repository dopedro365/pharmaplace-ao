<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private string $apiUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.sms.url', 'https://api.sms-artur.com/send');
        $this->apiKey = config('services.sms.key', '');
    }

    /**
     * Enviar SMS de novo pedido para farmácia
     */
    public function sendNewOrderSms(string $phone, Order $order): bool
    {
        $message = "🆕 NOVO PEDIDO #{$order->order_number}\n";
        $message .= "Cliente: {$order->customer_name}\n";
        $message .= "Total: AOA " . number_format($order->total, 2, ',', '.') . "\n";
        $message .= "Tipo: " . ($order->delivery_type === 'delivery' ? 'Entrega' : 'Retirada') . "\n";
        $message .= "Acesse o painel para mais detalhes.";

        return $this->sendSms($phone, $message);
    }

    /**
     * Enviar SMS de mudança de status para cliente
     */
    public function sendOrderStatusChangeSms(string $phone, Order $order, string $newStatus): bool
    {
        $statusMessages = [
            Order::STATUS_PAYMENT_VERIFICATION => "🔍 Seu pagamento está sendo verificado",
            Order::STATUS_CONFIRMED => "✅ Seu pedido foi confirmado e está sendo preparado",
            Order::STATUS_DELIVERED => "🎉 Seu pedido foi entregue com sucesso",
            Order::STATUS_CANCELLED => "❌ Seu pedido foi cancelado",
            Order::STATUS_RETURNED => "🔄 Seu pedido foi devolvido",
        ];

        $statusMessage = $statusMessages[$newStatus] ?? "Status do pedido alterado";

        $message = "📦 PEDIDO #{$order->order_number}\n";
        $message .= $statusMessage . "\n";
        
        if ($newStatus === Order::STATUS_DELIVERED) {
            $message .= "Obrigado por escolher nossa farmácia!";
        } elseif ($newStatus === Order::STATUS_CANCELLED) {
            $message .= "Entre em contato conosco se tiver dúvidas.";
        }

        return $this->sendSms($phone, $message);
    }

    /**
     * Método base para enviar SMS
     */
    private function sendSms(string $phone, string $message): bool
    {
        if (empty($this->apiKey)) {
            Log::warning('SMS API key not configured');
            return false;
        }

        try {
            // Limpar e formatar número de telefone
            $phone = $this->formatPhoneNumber($phone);

            $response = Http::timeout(10)->post($this->apiUrl, [
                'api_key' => $this->apiKey,
                'phone' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("SMS sent successfully to {$phone}");
                return true;
            } else {
                Log::error("Failed to send SMS to {$phone}: " . $response->body());
                return false;
            }

        } catch (\Exception $e) {
            Log::error("SMS sending error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Formatar número de telefone para padrão angolano
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remover espaços e caracteres especiais
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Se começar com 244, manter
        if (str_starts_with($phone, '244')) {
            return $phone;
        }

        // Se começar com 9, adicionar 244
        if (str_starts_with($phone, '9')) {
            return '244' . $phone;
        }

        // Caso contrário, assumir que já está no formato correto
        return $phone;
    }
}
