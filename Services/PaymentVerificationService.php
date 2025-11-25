<?php

namespace App\Services;

use App\Models\PharmacyTransaction;
use App\Models\Pharmacy;
use App\Services\Fasmapay;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class PaymentVerificationService
{
    protected $fasmapay;

    public function __construct(Fasmapay $fasmapay)
    {
        $this->fasmapay = $fasmapay;
    }

    /**
     * ✅ MÉTODO SIMPLIFICADO SEM TESTE DE CONEXÃO
     * Verifica comprovativos de pagamento para uma farmácia
     */
    public function verifyPaymentProofs(array $files, Pharmacy $pharmacy, float $expectedAmount, Carbon $cartUpdatedAt): array
    {
        Log::info('=== INÍCIO DA VERIFICAÇÃO DE COMPROVATIVOS ===', [
            'pharmacy_id' => $pharmacy->id,
            'pharmacy_name' => $pharmacy->name,
            'expected_amount' => $expectedAmount,
            'cart_updated_at' => $cartUpdatedAt->toDateString(),
            'files_count' => count($files)
        ]);

        try {
            // ✅ VALIDAR ARQUIVOS COM A API FASMAPAY DIRETAMENTE
            Log::info('Iniciando validação com API Fasmapay...');
            
            $apiResult = $this->fasmapay->validarComprovativosLivewire($files);
            
            if ($apiResult['status'] === 'erro') {
                Log::warning('Erro na validação dos comprovativos', $apiResult);
                return [
                    'success' => false,
                    'message' => 'Erro na validação dos comprovativos',
                    'details' => $apiResult['mensagem']
                ];
            }

            Log::info('Comprovativos validados pela API com sucesso', [
                'dados_count' => count($apiResult['dados'])
            ]);

            // ✅ PROCESSAR CADA COMPROVATIVO VALIDADO
            $validTransactions = [];
            $errors = [];
            $totalAmount = 0;

            foreach ($apiResult['dados'] as $index => $comprovativo) {
                Log::info("Processando comprovativo {$index}", [
                    'status' => $comprovativo['STATUS'] ?? 'N/A',
                    'transacao' => $comprovativo['TRANSACAO'] ?? 'N/A',
                    'montante' => $comprovativo['MONTANTE'] ?? 'N/A'
                ]);

                $result = $this->processComprovativo($comprovativo, $pharmacy, $cartUpdatedAt, $index + 1);
                
                if ($result['success']) {
                    $validTransactions[] = $result['transaction'];
                    $totalAmount += $result['transaction']['valor'];
                    Log::info("Comprovativo {$index} processado com sucesso", [
                        'valor' => $result['transaction']['valor']
                    ]);
                } else {
                    $errors[] = $result['message'];
                    Log::warning("Erro no comprovativo {$index}", ['error' => $result['message']]);
                }
            }

            // ✅ VERIFICAR SE HOUVE ERROS
            if (!empty($errors)) {
                return [
                    'success' => false,
                    'message' => 'Problemas encontrados nos comprovativos',
                    'details' => $errors
                ];
            }

            // ✅ VERIFICAR SE O VALOR TOTAL BATE
            if ($totalAmount < $expectedAmount) {
                $diferenca = $expectedAmount - $totalAmount;
                Log::warning('Valor insuficiente', [
                    'esperado' => $expectedAmount,
                    'recebido' => $totalAmount,
                    'diferenca' => $diferenca
                ]);

                return [
                    'success' => false,
                    'message' => "Valor insuficiente para cobrir o pedido",
                    'details' => [
                        "Valor esperado: " . number_format($expectedAmount, 2, ',', '.') . " Kz",
                        "Valor dos comprovativos: " . number_format($totalAmount, 2, ',', '.') . " Kz",
                        "Diferença: " . number_format($diferenca, 2, ',', '.') . " Kz"
                    ]
                ];
            }

            // ✅ SALVAR TRANSAÇÕES NO BANCO DE DADOS
            $savedTransactions = [];
            foreach ($validTransactions as $transactionData) {
                try {
                    $transaction = PharmacyTransaction::create($transactionData);
                    $savedTransactions[] = $transaction;
                    Log::info('Transação salva no banco', ['id' => $transaction->id]);
                } catch (\Exception $e) {
                    Log::error('Erro ao salvar transação', [
                        'data' => $transactionData,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }

            Log::info('Verificação concluída com sucesso', [
                'transactions_count' => count($savedTransactions),
                'total_amount' => $totalAmount
            ]);

            return [
                'success' => true,
                'message' => 'Comprovativos verificados com sucesso!',
                'transactions' => $savedTransactions,
                'total_amount' => $totalAmount,
                'expected_amount' => $expectedAmount
            ];

        } catch (\Exception $e) {
            Log::error('Erro na verificação de comprovativos', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Erro interno na verificação: ' . $e->getMessage(),
                'details' => ['Entre em contato com o suporte técnico.']
            ];
        }
    }

    /**
     * ✅ MÉTODO MELHORADO PARA PROCESSAR COMPROVATIVO INDIVIDUAL
     */
    protected function processComprovativo(array $comprovativo, Pharmacy $pharmacy, Carbon $cartUpdatedAt, int $index): array
    {
        Log::info("Processando comprovativo {$index} detalhadamente", [
            'comprovativo_keys' => array_keys($comprovativo),
            'status' => $comprovativo['STATUS'] ?? 'N/A'
        ]);

        // ✅ VERIFICAR SE O STATUS DA API É 200 (SUCESSO)
        if (($comprovativo['STATUS'] ?? 0) != 200) {
            $errorMsg = $comprovativo['LOG'] ?? 'Erro na validação pela API';
            Log::warning("Comprovativo {$index} rejeitado pela API", [
                'status' => $comprovativo['STATUS'] ?? 'N/A',
                'log' => $errorMsg
            ]);
            return [
                'success' => false,
                'message' => "Comprovativo {$index}: {$errorMsg}"
            ];
        }

        // ✅ OBTER CONTA BANCÁRIA PRINCIPAL DA FARMÁCIA
        $primaryBankAccount = $pharmacy->primaryBankAccount;
        if (!$primaryBankAccount) {
            Log::error("Farmácia {$pharmacy->id} não possui conta bancária principal");
            return [
                'success' => false,
                'message' => "Comprovativo {$index}: Farmácia não possui conta bancária configurada"
            ];
        }

        // ✅ VERIFICAR SE O IBAN DE DESTINO CORRESPONDE À FARMÁCIA
        $empresaIban = $this->fasmapay->formatarIban(str_replace(" ", "", $comprovativo["B_IBAN"] ?? ''));
        $pharmacyIban = $this->fasmapay->formatarIban(str_replace(" ", "", $primaryBankAccount->iban ?? ''));
        
        Log::info("Comparando IBANs", [
            'comprovativo_iban' => $empresaIban,
            'pharmacy_iban' => $pharmacyIban,
            'iban_original_comprovativo' => $comprovativo["B_IBAN"] ?? 'N/A',
            'iban_original_pharmacy' => $primaryBankAccount->iban ?? 'N/A'
        ]);
        
        if ($empresaIban !== $pharmacyIban) {
            return [
                'success' => false,
                'message' => "Comprovativo {$index}: IBAN de destino ({$comprovativo['B_IBAN']}) não corresponde à conta da farmácia ({$primaryBankAccount->iban})"
            ];
        }

        // ✅ VERIFICAR E FORMATAR A DATA DA TRANSFERÊNCIA
        $dataString = '';
        if (isset($comprovativo["DATA"]["data"])) {
            $dataString = $comprovativo["DATA"]["data"];
        } elseif (isset($comprovativo["DATA"])) {
            $dataString = is_array($comprovativo["DATA"]) ? ($comprovativo["DATA"]["data"] ?? '') : $comprovativo["DATA"];
        }

        Log::info("Processando data da transferência", [
            'data_raw' => $comprovativo["DATA"] ?? 'N/A',
            'data_string' => $dataString
        ]);

        $dataTransferencia = $this->fasmapay->formatarData($dataString);
        if (!$dataTransferencia) {
            return [
                'success' => false,
                'message' => "Comprovativo {$index}: Data da transferência inválida ({$dataString})"
            ];
        }

        $dataTransferenciaCarbon = Carbon::createFromFormat('Y-m-d', $dataTransferencia);
        
        // ✅ VERIFICAR SE A DATA DA TRANSFERÊNCIA NÃO É ANTERIOR À ATUALIZAÇÃO DO CARRINHO
        if (!$dataTransferenciaCarbon->greaterThanOrEqualTo($cartUpdatedAt->startOfDay())) {
            return [
                'success' => false,
                'message' => "Comprovativo {$index}: Data da transferência ({$dataTransferenciaCarbon->format('d/m/Y')}) é anterior à data do carrinho ({$cartUpdatedAt->format('d/m/Y')})"
            ];
        }

        // ✅ VERIFICAR SE A TRANSAÇÃO JÁ FOI UTILIZADA
        $referencia = $comprovativo['TRANSACAO'] ?? '';
        if (empty($referencia)) {
            return [
                'success' => false,
                'message' => "Comprovativo {$index}: Referência da transação não encontrada"
            ];
        }

        $existingTransaction = PharmacyTransaction::where('referencia', $referencia)->first();
        if ($existingTransaction) {
            Log::warning("Transação já utilizada", [
                'referencia' => $referencia,
                'existing_transaction_id' => $existingTransaction->id
            ]);
            return [
                'success' => false,
                'message' => "Comprovativo {$index}: Esta transação (Ref: {$referencia}) já foi utilizada anteriormente"
            ];
        }

        // ✅ PREPARAR DADOS DA TRANSAÇÃO
        $valor = $this->fasmapay->formatarMontante($comprovativo['MONTANTE'] ?? '0');
        
        $transactionData = [
            'pharmacy_id' => $pharmacy->id,
            'referencia' => $referencia,
            'valor' => $valor,
            'data_transferencia' => $dataTransferencia,
            'cliente_banco' => $comprovativo['O_BANCO'] ?? '',
            'empresa_banco' => $comprovativo['B_BANCO'] ?? '',
            'cliente_iban' => $comprovativo['O_IBAN'] ?? '',
            'empresa_iban' => $comprovativo['B_IBAN'] ?? '',
            'aplicativo' => $comprovativo['APLICATIVO'] ?? '',
            'comprovativo_path' => $comprovativo['FICHEIRO'] ?? '',
            'status' => 'verified',
            'observacoes' => "Comprovativo verificado automaticamente via API Fasmapay em " . now()->format('d/m/Y H:i:s')
        ];

        Log::info("Dados da transação preparados", $transactionData);

        return [
            'success' => true,
            'transaction' => $transactionData
        ];
    }

    /**
     * Marca transações como utilizadas para um pedido
     */
    public function markTransactionsAsUsed(array $transactionIds, int $orderId): void
    {
        PharmacyTransaction::whereIn('id', $transactionIds)
            ->update([
                'status' => 'used',
                'order_id' => $orderId,
                'updated_at' => now()
            ]);

        Log::info('Transações marcadas como utilizadas', [
            'transaction_ids' => $transactionIds,
            'order_id' => $orderId
        ]);
    }
}