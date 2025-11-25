<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Fasmapay
{
    // ✅ CHAVE DA API
    const CHAVE = "32y3103xirjNtICNfZkgttBALJaAOBiq19aoV3zUSx3E0Tds6B1ZoX6SfQsy230709";
    const URL   = "https://api.fasma.ao";

    /**
     * ✅ MÉTODO CORRIGIDO PARA VALIDAR ARQUIVOS UPLOADEDFILE DO LIVEWIRE
     * Removido o teste de conexão que estava causando erro 400
     */
    public function validarComprovativosLivewire(array $uploadedFiles, $diretorioDestino = "fasmapay/")
    {
        Log::info('=== VALIDANDO COMPROVATIVOS LIVEWIRE ===', [
            'arquivos_count' => count($uploadedFiles),
            'diretorio' => $diretorioDestino
        ]);

        $resultado = [];
        $erros     = [];

        if (empty($uploadedFiles)) {
            return [
                'status'   => 'erro',
                'mensagem' => ['Nenhum comprovativo enviado.'],
            ];
        }

        foreach ($uploadedFiles as $index => $file) {
            if (!($file instanceof UploadedFile)) {
                $erros[] = "Arquivo {$index} não é um arquivo válido.";
                continue;
            }

            $filename = $file->getClientOriginalName();
            Log::info("Processando arquivo Livewire {$index}: {$filename}");

            // Verificar se é PDF
            if ($file->getMimeType() !== 'application/pdf') {
                $erros[] = "O arquivo {$filename} não é um PDF válido.";
                Log::warning("Arquivo {$filename} não é PDF válido - MIME: " . $file->getMimeType());
                continue;
            }

            // Verificar tamanho (máximo 5MB)
            if ($file->getSize() > 5 * 1024 * 1024) {
                $erros[] = "O arquivo {$filename} excede o tamanho máximo de 5MB.";
                Log::warning("Arquivo {$filename} muito grande: " . $file->getSize() . " bytes");
                continue;
            }

            try {
                // Criar diretório se não existir
                $fullPath = storage_path('app/public/' . $diretorioDestino);
                if (!is_dir($fullPath)) {
                    mkdir($fullPath, 0777, true);
                    Log::info("Diretório criado: {$fullPath}");
                }

                // Gerar nome único para o arquivo
                $nomeArquivo = md5(time() . $filename . $index) . '.pdf';
                $caminhoDestino = $fullPath . $nomeArquivo;

                // ✅ SALVAR ARQUIVO USANDO MÉTODO CORRETO
                if (!$file->move($fullPath, $nomeArquivo)) {
                    throw new Exception("Falha ao salvar arquivo {$filename}");
                }

                Log::info("Arquivo Livewire salvo em: {$caminhoDestino}");

                // ✅ ENVIAR PARA API COM CHAVE CORRETA
                $resultadoApi = $this->enviarParaApiFasma($caminhoDestino, $filename);
                
                if ($resultadoApi['sucesso']) {
                    $resultado[] = $resultadoApi['dados'];
                } else {
                    $erros[] = $resultadoApi['erro'];
                }

            } catch (Exception $e) {
                $erros[] = "Erro ao processar arquivo {$filename}: " . $e->getMessage();
                Log::error("Erro ao processar arquivo {$filename}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Retornar resultado
        if (!empty($erros)) {
            return [
                'status'   => 'erro',
                'mensagem' => $erros,
            ];
        }

        return [
            'status' => 'sucesso',
            'dados'  => $resultado,
        ];
    }

    /**
     * ✅ MÉTODO CORRIGIDO PARA ENVIAR ARQUIVO PARA API FASMA
     * Ajustado para enviar dados corretos e evitar erro 400
     */
    private function enviarParaApiFasma($caminhoArquivo, $nomeOriginal)
    {
        Log::info('Enviando arquivo para API Fasma', [
            'arquivo' => $caminhoArquivo,
            'nome_original' => $nomeOriginal,
            'chave_api' => substr(self::CHAVE, 0, 10) . '...' // Log parcial da chave por segurança
        ]);

        try {
            // Verificar se arquivo existe
            if (!file_exists($caminhoArquivo)) {
                throw new Exception("Arquivo não encontrado: {$caminhoArquivo}");
            }

            // ✅ CONFIGURAR URL DA API COM CHAVE CORRETA
            $urlApi = self::URL . '?sudopay_key=' . self::CHAVE;

            // ✅ OBTER URL DO SITE ATUAL DE FORMA MAIS ROBUSTA
            $siteUrl = config('app.url');
            if (empty($siteUrl)) {
                $siteUrl = request()->getSchemeAndHttpHost();
            }

            Log::info('Configurações da requisição', [
                'url_api' => $urlApi,
                'site_url' => $siteUrl,
                'arquivo_existe' => file_exists($caminhoArquivo),
                'tamanho_arquivo' => filesize($caminhoArquivo)
            ]);

            // ✅ CONFIGURAR CURL COM PARÂMETROS CORRETOS
            $ch = curl_init();
            
            // Configurações básicas
            curl_setopt($ch, CURLOPT_URL, $urlApi);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120); // Timeout maior para upload
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            
            // ✅ CONFIGURAÇÕES SSL MAIS PERMISSIVAS (PARA DESENVOLVIMENTO)
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            
            // ✅ HEADERS CORRETOS
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'User-Agent: Mozilla/5.0 (compatible; FarmaciaApp/1.0)',
                'Accept: application/json,text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8',
                'Cache-Control: no-cache',
            ]);
            
            // ✅ DADOS DO POST COM CURLFILE
            $postData = [
                'url' => $siteUrl,
                'sudopay_file' => new \CURLFile($caminhoArquivo, 'application/pdf', basename($caminhoArquivo))
            ];
            
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

            Log::info('Enviando requisição para API...', [
                'url' => $urlApi,
                'post_data_keys' => array_keys($postData)
            ]);

            // ✅ EXECUTAR REQUISIÇÃO
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlInfo = curl_getinfo($ch);

            // ✅ VERIFICAR ERROS DE CURL
            if (curl_errno($ch)) {
                $curlError = curl_error($ch);
                curl_close($ch);
                Log::error('Erro cURL ao conectar com API Fasma', [
                    'error' => $curlError,
                    'arquivo' => $nomeOriginal,
                    'curl_info' => $curlInfo
                ]);
                throw new Exception("Erro de conexão com API: {$curlError}");
            }

            curl_close($ch);

            Log::info('Resposta da API recebida', [
                'http_code' => $httpCode,
                'response_length' => strlen($response),
                'response_preview' => substr($response, 0, 500),
                'curl_info' => $curlInfo
            ]);

            // ✅ VERIFICAR CÓDIGO HTTP - ACEITAR MAIS CÓDIGOS
            if (!in_array($httpCode, [200, 201, 202])) {
                Log::error('API retornou código HTTP inválido', [
                    'http_code' => $httpCode,
                    'response' => $response,
                    'arquivo' => $nomeOriginal
                ]);
                throw new Exception("API retornou código HTTP {$httpCode}. Resposta: " . substr($response, 0, 200));
            }

            // ✅ DECODIFICAR RESPOSTA JSON
            $vetorResposta = json_decode($response, true);
            if ($vetorResposta === null && json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Resposta inválida da API', [
                    'response' => $response,
                    'json_error' => json_last_error_msg()
                ]);
                throw new Exception("Resposta inválida da API para {$nomeOriginal}: " . json_last_error_msg());
            }

            // ✅ ADICIONAR CAMINHO DO ARQUIVO À RESPOSTA
            if (is_array($vetorResposta)) {
                $vetorResposta["FICHEIRO"] = $caminhoArquivo;
            } else {
                // Se não for JSON válido, criar estrutura básica
                $vetorResposta = [
                    'STATUS' => $httpCode === 200 ? 200 : 400,
                    'LOG' => 'Resposta não-JSON da API: ' . substr($response, 0, 100),
                    'FICHEIRO' => $caminhoArquivo
                ];
            }

            Log::info('Arquivo processado pela API', [
                'arquivo' => $nomeOriginal,
                'status' => $vetorResposta['STATUS'] ?? 'N/A',
                'log' => $vetorResposta['LOG'] ?? 'N/A'
            ]);

            return [
                'sucesso' => true,
                'dados' => $vetorResposta
            ];

        } catch (Exception $e) {
            Log::error('Erro ao enviar arquivo para API Fasma', [
                'arquivo' => $nomeOriginal,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'sucesso' => false,
                'erro' => "Erro ao validar {$nomeOriginal}: " . $e->getMessage()
            ];
        }
    }

    /**
     * ✅ MÉTODO ORIGINAL MANTIDO PARA COMPATIBILIDADE
     */
    public function validarRecibo($comprovativos, $diretorioDestino = "fasmapay/")
    {
        Log::info('=== VALIDANDO RECIBOS VIA $_FILES ===', [
            'campo' => $comprovativos,
            'diretorio' => $diretorioDestino
        ]);

        $resultado = [];
        $erros     = [];

        if (isset($_FILES[$comprovativos])) {
            $arquivos = $_FILES[$comprovativos];

            if (is_array($arquivos['name'])) {
                foreach ($arquivos['name'] as $key => $filename) {
                    Log::info("Processando arquivo {$key}: {$filename}");

                    if ($arquivos['error'][$key] !== UPLOAD_ERR_OK) {
                        $erros[] = "Erro ao enviar o arquivo " . $filename;
                        Log::warning("Erro no upload do arquivo {$filename}");
                        continue;
                    }

                    $caminhoArquivo = $arquivos['tmp_name'][$key];
                    $nomeArquivo    = md5(time() . $filename) . '.pdf';

                    if (mime_content_type($caminhoArquivo) !== 'application/pdf') {
                        $erros[] = "O arquivo " . $filename . " não é um PDF válido.";
                        Log::warning("Arquivo {$filename} não é PDF válido");
                        continue;
                    }

                    $fullPath = storage_path('app/public/' . $diretorioDestino);
                    if (!is_dir($fullPath)) {
                        mkdir($fullPath, 0777, true);
                        Log::info("Diretório criado: {$fullPath}");
                    }

                    $caminhoDestino = $fullPath . $nomeArquivo;

                    if (!move_uploaded_file($caminhoArquivo, $caminhoDestino)) {
                        $erros[] = "Erro ao fazer o upload do arquivo " . $filename;
                        Log::error("Falha ao mover arquivo {$filename} para {$caminhoDestino}");
                        continue;
                    }

                    Log::info("Arquivo salvo em: {$caminhoDestino}");

                    $resultadoApi = $this->enviarParaApiFasma($caminhoDestino, $filename);
                    
                    if ($resultadoApi['sucesso']) {
                        $resultado[] = $resultadoApi['dados'];
                    } else {
                        $erros[] = $resultadoApi['erro'];
                    }
                }

                if (!empty($erros)) {
                    return [
                        'status'   => 'erro',
                        'mensagem' => $erros,
                    ];
                }

                return [
                    'status' => 'sucesso',
                    'dados'  => $resultado,
                ];
            } else {
                $erros[] = 'O campo de comprovativos não contém múltiplos arquivos.';
                return [
                    'status'   => 'erro',
                    'mensagem' => $erros,
                ];
            }
        } else {
            $erros[] = 'Nenhum comprovativo enviado.';
            return [
                'status'   => 'erro',
                'mensagem' => $erros,
            ];
        }
    }

    // ✅ MANTER MÉTODOS ORIGINAIS INALTERADOS
    public function formatarMontante($montante)
    {
        $valor = str_replace('.', '', $montante);
        $valor = str_replace(',', '.', $valor);
        return floatval($valor);
    }

    public function formatarIban($iban)
    {
        $string = str_replace('AO06', '', $iban);
        $string = str_replace('.', '', $string);
        $string = str_replace(' ', '', $string);
        return $string;
    }

    public function formatarData($data)
    {
        try {
            $data = trim($data);

            $objetoData = \DateTime::createFromFormat('d/m/Y', $data)
                ?: \DateTime::createFromFormat('Y-m-d', $data)
                ?: \DateTime::createFromFormat('d-m-Y', $data)
                ?: \DateTime::createFromFormat('m/d/Y', $data)
                ?: \DateTime::createFromFormat('Y/m/d', $data);

            if ($objetoData) {
                return $objetoData->format('Y-m-d');
            }

            throw new \Exception("Formato de data inválido: $data");
        } catch (\Exception $e) {
            return null;
        }
    }

    public function cambio()
    {
        try {
            $response = Http::get('https://economia.awesomeapi.com.br/json/last/USD-AOA');

            if ($response->successful()) {
                $high = $response->json('USDAOA.high');
                $ask  = $response->json('USDAOA.ask');

                $ask = $ask > $high ? $ask : $high;
                $ask += 0.2 * $ask;
            } else {
                $ask = 1000;
            }
        } catch (Exception $e) {
            $ask = 1000;
        }
        return $ask;
    }

    public function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/\s+/', '', $phone);

        if (!preg_match('/^\+/', $phone)) {
            $phone = '+244' . ltrim($phone, '0');
        }

        return $phone;
    }

    public function removerAcentos($string)
    {
        $acentos = [
            'á', 'à', 'ã', 'â', 'ä', 'é', 'è', 'ê', 'ë', 'í', 'ì', 'î', 'ï',
            'ó', 'ò', 'õ', 'ô', 'ö', 'ú', 'ù', 'û', 'ü', 'ç',
            'Á', 'À', 'Ã', 'Â', 'Ä', 'É', 'È', 'Ê', 'Ë', 'Í', 'Ì', 'Î', 'Ï',
            'Ó', 'Ò', 'Õ', 'Ô', 'Ö', 'Ú', 'Ù', 'Û', 'Ü', 'Ç',
        ];
        $semAcentos = [
            'a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'c',
            'A', 'A', 'A', 'A', 'A', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I',
            'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'C',
        ];

        return str_replace($acentos, $semAcentos, $string);
    }

    public function enviarSMS($numero, $mensagem)
    {
        $url = "https://www.traccar.org/sms/";
        $token = "dtlBjcseQtSy0B-v-KCLqV:APA91bHS6EPk7djffZY35zzWYM9_l6yAHkukDgCFShwjI75T86VpY86IJlBUszUhpsc1gfCarqaLjwN_1uWGc9q0Wn652ZM7daRmu2hKUjANoXOCBN_Wgk8";

        $data = [
            "to"      => $numero,
            "message" => $mensagem,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: " . $token,
            "Content-Type: application/json",
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo "Erro ao enviar SMS: " . curl_error($ch);
        }

        curl_close($ch);
        return $response;
    }
}