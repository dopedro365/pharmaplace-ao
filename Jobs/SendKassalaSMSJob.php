<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendKassalaSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $recipient;
    protected string $message;
    protected int $orderId;
    protected ?string $senderId;

    public int $tries = 3;
    public int $timeout = 30;
    public int $backoff = 10;

    public function __construct(string $recipient, string $message, int $orderId, ?string $senderId = null)
    {
        $this->recipient = $recipient;
        $this->message = $message;
        $this->orderId = $orderId;
        $this->senderId = $senderId ?? config('services.kassala_sms.sender_id', 'RammesPharm');
        
        // ✅ CONFIGURAR FILA ESPECÍFICA PARA SMS
        $this->onQueue('sms');
        
        // ✅ DELAY PEQUENO PARA EVITAR DUPLICATAS
        $this->delay(now()->addSeconds(3));
    }

    public function handle(): void
    {
        $jobId = uniqid();
        $apiKey = config('services.kassala_sms.api_key');
        $url = 'https://smsapi.sudomakes.com/api/enviar-sms';

        Log::info("SMS Job [{$jobId}]: INICIANDO envio para pedido {$this->orderId}", [
            'recipient' => $this->recipient,
            'sender_id' => $this->senderId,
            'attempt' => $this->attempts(),
            'message_preview' => substr($this->message, 0, 50) . '...'
        ]);

        if (empty($apiKey)) {
            Log::error("SMS Job [{$jobId}]: ❌ API Key não configurada!");
            throw new \Exception("KASSALA_SMS_API_KEY não está configurada no .env");
        }

        // ✅ VERIFICAR SE JÁ FOI ENVIADO (EVITAR DUPLICATAS)
        $cacheKey = "sms_sent_{$this->orderId}_{$this->recipient}";
        if (cache()->has($cacheKey)) {
            Log::info("SMS Job [{$jobId}]: ⚠️ SMS já foi enviado para este pedido, pulando...");
            return;
        }

        // Tentar diferentes formatos de payload
        $payloads = $this->getPayloadVariations($apiKey);
        
        foreach ($payloads as $index => $payload) {
            Log::info("SMS Job [{$jobId}]: Tentando payload #{$index}");

            try {
                $success = $this->tryApiCall($jobId, $url, $payload, $index);
                if ($success) {
                    // ✅ MARCAR COMO ENVIADO PARA EVITAR DUPLICATAS
                    cache()->put($cacheKey, true, now()->addHours(24));
                    Log::info("SMS Job [{$jobId}]: ✅ SMS enviado e marcado no cache");
                    return; // Sucesso - sair do método
                }
            } catch (\Exception $e) {
                Log::warning("SMS Job [{$jobId}]: Payload #{$index} falhou: " . $e->getMessage());
                continue; // Tentar próximo payload
            }
        }

        // Se chegou aqui, todos os payloads falharam
        throw new \Exception("Todos os formatos de payload falharam para pedido {$this->orderId}");
    }

    private function getPayloadVariations(string $apiKey): array
    {
        return [
            // Variação 1: Básico
            [
                'api_key' => $apiKey,
                'destinatario' => $this->recipient,
                'mensagem' => $this->message,
            ],
            
            // Variação 2: Com remetente
            [
                'api_key' => $apiKey,
                'destinatario' => $this->recipient,
                'mensagem' => $this->message,
                'remetente' => $this->senderId,
            ],
            
            // Variação 3: Com sender_id
            [
                'api_key' => $apiKey,
                'destinatario' => $this->recipient,
                'mensagem' => $this->message,
                'sender_id' => $this->senderId,
            ],
            
            // Variação 4: Nomes em inglês
            [
                'api_key' => $apiKey,
                'recipient' => $this->recipient,
                'message' => $this->message,
                'sender_id' => $this->senderId,
            ],
        ];
    }

    private function tryApiCall(string $jobId, string $url, array $payload, int $payloadIndex): bool
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'RammesPharm/1.0',
                ])
                ->post($url, $payload);

            Log::info("SMS Job [{$jobId}]: Resposta recebida", [
                'payload_index' => $payloadIndex,
                'status_code' => $response->status(),
                'response_preview' => substr($response->body(), 0, 200)
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Verificar se a resposta indica sucesso
                if (isset($responseData['status']) && $responseData['status'] === 'success') {
                    Log::info("SMS Job [{$jobId}]: ✅ SMS enviado com sucesso!", [
                        'payload_index' => $payloadIndex,
                        'response' => $responseData
                    ]);
                    return true;
                } elseif (!isset($responseData['erro'])) {
                    // Se não tem erro, assumir sucesso
                    Log::info("SMS Job [{$jobId}]: ✅ SMS enviado (assumindo sucesso)");
                    return true;
                }
            }

            Log::warning("SMS Job [{$jobId}]: Tentativa falhou", [
                'payload_index' => $payloadIndex,
                'status' => $response->status(),
                'body_preview' => substr($response->body(), 0, 200)
            ]);

        } catch (\Exception $e) {
            Log::error("SMS Job [{$jobId}]: Erro na tentativa payload #{$payloadIndex}: " . $e->getMessage());
            throw $e;
        }

        return false;
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SMS Job: 💀 JOB FALHOU DEFINITIVAMENTE para pedido {$this->orderId}", [
            'recipient' => $this->recipient,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }

    /**
     * ✅ IDENTIFICADOR ÚNICO PARA EVITAR DUPLICATAS
     */
    public function uniqueId(): string
    {
        return "sms_order_{$this->orderId}_{$this->recipient}";
    }
}
