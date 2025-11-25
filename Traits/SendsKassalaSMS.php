<?php

namespace App\Traits;

use App\Jobs\SendKassalaSMSJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait SendsKassalaSMS
{
    /**
     * Enviar SMS de forma assíncrona usando fila
     */
    public function sendKassalaSMSAsync(string $recipient, string $message, int $orderId, ?string $senderId = null): void
    {
        $traitId = uniqid();
        
        // Validar número de telefone
        $cleanRecipient = $this->cleanPhoneNumber($recipient);
        if (!$this->isValidPhoneNumber($cleanRecipient)) {
            Log::warning("Trait [{$traitId}]: ⚠️ Número de telefone inválido: {$recipient}");
            return;
        }

        // Determinar o sender_id baseado no contexto
        if (!$senderId) {
            $senderId = $this->determineSenderId($orderId);
        }

        Log::info("Trait [{$traitId}]: Adicionando SMS à fila para pedido {$orderId}", [
            'recipient' => $cleanRecipient,
            'sender_id' => $senderId,
            'message_length' => strlen($message)
        ]);

        try {
            SendKassalaSMSJob::dispatch($cleanRecipient, $message, $orderId, $senderId)
                ->delay(now()->addSeconds(2));
                
            Log::info("Trait [{$traitId}]: ✅ SMS adicionado à fila com sucesso");
            
        } catch (\Exception $e) {
            Log::error("Trait [{$traitId}]: ❌ Erro ao adicionar SMS à fila: " . $e->getMessage());
        }
    }

    /**
     * Limpar número de telefone
     */
    private function cleanPhoneNumber(string $phone): string
    {
        // Remover espaços, parênteses, hífens, etc.
        $clean = preg_replace('/[^0-9+]/', '', $phone);
        
        // Se não começar com +, assumir que é Angola (+244)
        if (!str_starts_with($clean, '+')) {
            if (str_starts_with($clean, '244')) {
                $clean = '+' . $clean;
            } else {
                $clean = '+244' . $clean;
            }
        }
        
        return $clean;
    }

    /**
     * Validar número de telefone
     */
    private function isValidPhoneNumber(string $phone): bool
    {
        // Validação básica para números de Angola
        return preg_match('/^\+244[0-9]{9}$/', $phone) === 1;
    }

    /**
     * Determinar o sender_id baseado no contexto
     */
    private function determineSenderId(int $orderId): string
    {
        try {
            // Buscar o pedido para determinar a farmácia
            $order = \App\Models\Order::with('pharmacy')->find($orderId);
            
            if ($order && $order->pharmacy) {
                // Usar nome da farmácia (limitado a 11 caracteres para SMS)
                $pharmacyName = substr($order->pharmacy->name, 0, 11);
                return $pharmacyName;
            }
            
            // Fallback para nome padrão da aplicação
            return config('services.kassala_sms.sender_id', 'FarmaciaApp');
            
        } catch (\Exception $e) {
            Log::warning("Erro ao determinar sender_id para pedido {$orderId}: " . $e->getMessage());
            return config('services.kassala_sms.sender_id', 'FarmaciaApp');
        }
    }

    /**
     * Método síncrono mantido para casos especiais
     */
    public function sendKassalaSMSSync(string $recipient, string $message, ?string $senderId = null): array
    {
        $apiKey = config('services.kassala_sms.api_key');
        $url = 'https://smsapi.sudomakes.com/api/enviar-sms';
        $senderId = $senderId ?? config('services.kassala_sms.sender_id', 'FarmaciaApp');

        $cleanRecipient = $this->cleanPhoneNumber($recipient);

        try {
            $payload = [
                'api_key' => $apiKey,
                'destinatario' => $cleanRecipient,
                'mensagem' => $message,
            ];

            if ($senderId) {
                $payload['remetente'] = $senderId; // ou 'sender_id'
            }

            $response = Http::timeout(10)->post($url, $payload);
            return $response->json();
            
        } catch (\Exception $e) {
            Log::error("Erro ao enviar SMS síncrono: " . $e->getMessage());
            return ['status' => 'error', 'mensagem' => 'Falha ao enviar SMS: ' . $e->getMessage()];
        }
    }
}
