<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PharmacyTransaction;
use App\Models\PharmacyBankAccount;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentProofService
{
    /**
     * Processa múltiplos comprovativos de pagamento
     * 
     * @param array $proofs Array de arquivos UploadedFile
     * @param Order $order Pedido relacionado
     * @param PharmacyBankAccount $bankAccount Conta bancária da farmácia
     * @return array Resultado do processamento
     */
    public function processPaymentProofs(array $proofs, Order $order, PharmacyBankAccount $bankAccount): array
    {
        $results = [
            'success' => false,
            'message' => '',
            'details' => [],
            'total_amount' => 0,
            'required_amount' => $order->total_amount,
            'processed_proofs' => [],
            'errors' => []
        ];

        try {
            // Validar se há comprovativos
            if (empty($proofs)) {
                $results['message'] = 'Nenhum comprovativo foi enviado.';
                return $results;
            }

            // Processar cada comprovativo
            foreach ($proofs as $index => $proof) {
                $proofResult = $this->processSingleProof($proof, $order, $bankAccount, $index + 1);
                
                if ($proofResult['success']) {
                    $results['processed_proofs'][] = $proofResult['transaction'];
                    $results['total_amount'] += $proofResult['amount'];
                    $results['details'][] = "Comprovativo " . ($index + 1) . ": " . number_format($proofResult['amount'], 2, ',', '.') . " Kz processado com sucesso.";
                } else {
                    $results['errors'][] = "Comprovativo " . ($index + 1) . ": " . $proofResult['message'];
                }
            }

            // Verificar se o valor total é suficiente
            if ($results['total_amount'] >= $results['required_amount']) {
                $results['success'] = true;
                $results['message'] = $this->generateSuccessMessage($results);
                
                // Marcar todas as transações como verificadas
                foreach ($results['processed_proofs'] as $transaction) {
                    $transaction->update([
                        'status' => 'verified',
                        'order_id' => $order->id
                    ]);
                }
                
                Log::info('Comprovativos processados com sucesso', [
                    'order_id' => $order->id,
                    'total_amount' => $results['total_amount'],
                    'required_amount' => $results['required_amount'],
                    'proofs_count' => count($results['processed_proofs'])
                ]);
                
            } else {
                $results['message'] = $this->generateInsufficientAmountMessage($results);
                
                // Marcar transações como pendentes para revisão
                foreach ($results['processed_proofs'] as $transaction) {
                    $transaction->update(['status' => 'pending']);
                }
            }

        } catch (\Exception $e) {
            Log::error('Erro ao processar comprovativos', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $results['message'] = 'Erro interno ao processar comprovativos. Tente novamente.';
            $results['errors'][] = 'Erro técnico: ' . $e->getMessage();
        }

        return $results;
    }

    /**
     * Processa um único comprovativo
     */
    private function processSingleProof(UploadedFile $proof, Order $order, PharmacyBankAccount $bankAccount, int $proofNumber): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'amount' => 0,
            'transaction' => null
        ];

        try {
            // Validar arquivo
            $validation = $this->validateProofFile($proof, $proofNumber);
            if (!$validation['valid']) {
                $result['message'] = $validation['message'];
                return $result;
            }

            // Salvar arquivo
            $filePath = $this->saveProofFile($proof, $order);
            
            // Extrair dados do comprovativo (simulação - aqui você integraria com API real)
            $proofData = $this->extractProofData($proof, $filePath);
            
            // Validar dados extraídos
            $dataValidation = $this->validateProofData($proofData, $order, $bankAccount);
            if (!$dataValidation['valid']) {
                // Remover arquivo se validação falhar
                Storage::delete($filePath);
                $result['message'] = $dataValidation['message'];
                return $result;
            }

            // Criar transação
            $transaction = PharmacyTransaction::create([
                'pharmacy_id' => $order->pharmacy_id,
                'order_id' => null, // Será definido após validação completa
                'referencia' => $proofData['referencia'],
                'valor' => $proofData['valor'],
                'data_transferencia' => $proofData['data_transferencia'],
                'cliente_banco' => $proofData['cliente_banco'],
                'empresa_banco' => $proofData['empresa_banco'],
                'cliente_iban' => $proofData['cliente_iban'],
                'empresa_iban' => $proofData['empresa_iban'],
                'aplicativo' => $proofData['aplicativo'],
                'comprovativo_path' => $filePath,
                'status' => 'pending',
                'observacoes' => "Comprovativo #{$proofNumber} processado automaticamente"
            ]);

            $result['success'] = true;
            $result['amount'] = $proofData['valor'];
            $result['transaction'] = $transaction;
            $result['message'] = 'Comprovativo processado com sucesso';

        } catch (\Exception $e) {
            Log::error('Erro ao processar comprovativo individual', [
                'proof_number' => $proofNumber,
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            $result['message'] = 'Erro ao processar comprovativo: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Valida arquivo do comprovativo
     */
    private function validateProofFile(UploadedFile $file, int $proofNumber): array
    {
        // Validar tamanho (máx 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            return [
                'valid' => false,
                'message' => "Arquivo muito grande (máx. 5MB)"
            ];
        }

        // Validar tipo
        $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return [
                'valid' => false,
                'message' => "Formato não suportado. Use JPG, PNG ou PDF"
            ];
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Salva arquivo do comprovativo
     */
    private function saveProofFile(UploadedFile $file, Order $order): string
    {
        $fileName = 'comprovativo_' . $order->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('payment_proofs', $fileName, 'public');
    }

    /**
     * Extrai dados do comprovativo (simulação - integrar com API real)
     */
    private function extractProofData(UploadedFile $file, string $filePath): array
    {
        // SIMULAÇÃO: Em produção, aqui você integraria com API de OCR ou Fasmapay
        // Por agora, retornamos dados simulados baseados no nome do arquivo
        
        $randomAmount = rand(1000, 50000); // Valor aleatório para simulação
        $randomDate = Carbon::now()->subDays(rand(0, 5))->format('Y-m-d');
        
        return [
            'referencia' => 'REF' . time() . rand(1000, 9999),
            'valor' => $randomAmount,
            'data_transferencia' => $randomDate,
            'cliente_banco' => 'Banco Angolano de Investimentos',
            'empresa_banco' => 'Banco Angolano de Investimentos',
            'cliente_iban' => 'AO06 0040 0000 2716 3888 1017 7',
            'empresa_iban' => 'AO06 0040 0000 8877 0965 1013 2',
            'aplicativo' => 'BAI Directo'
        ];
    }

    /**
     * Valida dados extraídos do comprovativo
     */
    private function validateProofData(array $data, Order $order, PharmacyBankAccount $bankAccount): array
    {
        // Validar data da transferência
        $transferDate = Carbon::parse($data['data_transferencia']);
        $orderDate = Carbon::parse($order->created_at);
        
        if ($transferDate->lt($orderDate)) {
            return [
                'valid' => false,
                'message' => "Data da transferência ({$transferDate->format('d/m/Y')}) é anterior à data do pedido ({$orderDate->format('d/m/Y')})"
            ];
        }

        // Validar se não é muito antiga (máx 30 dias)
        if ($transferDate->lt(Carbon::now()->subDays(30))) {
            return [
                'valid' => false,
                'message' => "Comprovativo muito antigo (máx. 30 dias)"
            ];
        }

        // Validar valor mínimo
        if ($data['valor'] < 100) {
            return [
                'valid' => false,
                'message' => "Valor muito baixo (mín. 100 Kz)"
            ];
        }

        // Verificar se referência já existe
        if (PharmacyTransaction::where('referencia', $data['referencia'])->exists()) {
            return [
                'valid' => false,
                'message' => "Comprovativo já foi processado anteriormente"
            ];
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Gera mensagem de sucesso
     */
    private function generateSuccessMessage(array $results): string
    {
        $totalFormatted = number_format($results['total_amount'], 2, ',', '.');
        $requiredFormatted = number_format($results['required_amount'], 2, ',', '.');
        $proofsCount = count($results['processed_proofs']);
        
        $message = "Pagamento confirmado! ";
        $message .= "Processados {$proofsCount} comprovativo(s) totalizando {$totalFormatted} Kz ";
        $message .= "(valor necessário: {$requiredFormatted} Kz).";
        
        if ($results['total_amount'] > $results['required_amount']) {
            $excess = $results['total_amount'] - $results['required_amount'];
            $excessFormatted = number_format($excess, 2, ',', '.');
            $message .= " Valor excedente de {$excessFormatted} Kz será creditado à farmácia.";
        }
        
        return $message;
    }

    /**
     * Gera mensagem de valor insuficiente
     */
    private function generateInsufficientAmountMessage(array $results): string
    {
        $totalFormatted = number_format($results['total_amount'], 2, ',', '.');
        $requiredFormatted = number_format($results['required_amount'], 2, ',', '.');
        $missing = $results['required_amount'] - $results['total_amount'];
        $missingFormatted = number_format($missing, 2, ',', '.');
        
        return "Valor insuficiente. Total dos comprovativos: {$totalFormatted} Kz. " .
               "Valor necessário: {$requiredFormatted} Kz. " .
               "Faltam: {$missingFormatted} Kz. " .
               "Adicione mais comprovativos ou entre em contato com a farmácia.";
    }

    /**
     * Obtém resumo das transações de um pedido
     */
    public function getOrderTransactionsSummary(Order $order): array
    {
        $transactions = PharmacyTransaction::where('order_id', $order->id)->get();
        
        return [
            'total_transactions' => $transactions->count(),
            'total_amount' => $transactions->sum('valor'),
            'verified_amount' => $transactions->where('status', 'verified')->sum('valor'),
            'pending_amount' => $transactions->where('status', 'pending')->sum('valor'),
            'transactions' => $transactions
        ];
    }
}
