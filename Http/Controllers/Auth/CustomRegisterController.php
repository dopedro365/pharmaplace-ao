<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pharmacy;
use App\Models\Document;
use App\Models\LegalDocument;
use App\Models\UserLegalAcceptance;
use App\Notifications\NewPharmacyRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class CustomRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $termsOfUse = LegalDocument::getActiveTerms();
        $privacyPolicy = LegalDocument::getActivePrivacyPolicy();
        
        return view('auth.custom-register', compact('termsOfUse', 'privacyPolicy'));
    }

    public function register(Request $request)
    {
        Log::info('🚀 Iniciando processo de registro', [
            'role' => $request->role,
            'name' => $request->name,
            'pharmacy_name' => $request->pharmacy_name,
            'email' => $request->email
        ]);

        // Regras básicas de validação
        $basicRules = [
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:customer,pharmacy',
            'accept_terms' => 'required|accepted',
            'accept_privacy' => 'required|accepted',
        ];

        // Adicionar validação condicional baseada no role
        if ($request->role === 'customer') {
            $basicRules['name'] = 'required|string|max:255';
        } elseif ($request->role === 'pharmacy') {
            $pharmacyRules = [
                'pharmacy_name' => 'required|string|max:255',
                'nif' => 'required|string|unique:pharmacies,license_number',
                'address' => 'required|string|max:255',
                'municipality' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'whatsapp' => 'nullable|string|max:255',
                'accepts_delivery' => 'boolean',
                'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'document_types.*' => 'required|string|in:license,certificate,other',
            ];
            $basicRules = array_merge($basicRules, $pharmacyRules);
        }

        $validator = Validator::make($request->all(), $basicRules, [
            // Mensagens para campos básicos
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto válido.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.email' => 'Por favor, insira um email válido.',
            'email.unique' => 'Este email já está em uso.',
            'phone.string' => 'O telefone deve ser um texto válido.',
            'phone.unique' => 'Este número de telefone já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.string' => 'A senha deve ser um texto válido.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'role.required' => 'O tipo de conta é obrigatório.',
            'role.in' => 'Tipo de conta inválido.',
            
            // Mensagens para campos de farmácia
            'pharmacy_name.required' => 'O nome da farmácia é obrigatório.',
            'pharmacy_name.string' => 'O nome da farmácia deve ser um texto válido.',
            'pharmacy_name.max' => 'O nome da farmácia não pode ter mais de 255 caracteres.',
            'nif.required' => 'O NIF é obrigatório.',
            'nif.string' => 'O NIF deve ser um texto válido.',
            'nif.unique' => 'Este NIF já está registrado.',
            'address.required' => 'O endereço é obrigatório.',
            'address.string' => 'O endereço deve ser um texto válido.',
            'address.max' => 'O endereço não pode ter mais de 255 caracteres.',
            'municipality.required' => 'O município é obrigatório.',
            'municipality.string' => 'O município deve ser um texto válido.',
            'municipality.max' => 'O município não pode ter mais de 255 caracteres.',
            'province.required' => 'A província é obrigatória.',
            'province.string' => 'A província deve ser um texto válido.',
            'province.max' => 'A província não pode ter mais de 255 caracteres.',
            'whatsapp.string' => 'O WhatsApp deve ser um texto válido.',
            'whatsapp.max' => 'O WhatsApp não pode ter mais de 255 caracteres.',
            
            // Mensagens para termos e documentos
            'accept_terms.required' => 'Você deve aceitar os Termos de Uso.',
            'accept_terms.accepted' => 'Você deve aceitar os Termos de Uso.',
            'accept_privacy.required' => 'Você deve aceitar a Política de Privacidade.',
            'accept_privacy.accepted' => 'Você deve aceitar a Política de Privacidade.',
            'documents.*.required' => 'Pelo menos um documento é obrigatório para farmácias.',
            'documents.*.file' => 'O documento deve ser um arquivo válido.',
            'documents.*.mimes' => 'Os documentos devem ser arquivos PDF, JPG, JPEG ou PNG.',
            'documents.*.max' => 'Cada documento deve ter no máximo 5MB.',
            'document_types.*.required' => 'O tipo de documento é obrigatório.',
            'document_types.*.string' => 'O tipo de documento deve ser um texto válido.',
            'document_types.*.in' => 'Tipo de documento inválido.',
        ]);

        // Validação customizada: pelo menos email ou telefone
        $validator->after(function ($validator) use ($request) {
            if (!$request->email && !$request->phone) {
                $validator->errors()->add('email', 'Email ou telefone é obrigatório.');
                $validator->errors()->add('phone', 'Email ou telefone é obrigatório.');
            }
        });

        if ($validator->fails()) {
            Log::warning('❌ Validação falhou', ['errors' => $validator->errors()->toArray()]);
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            Log::info('📝 Iniciando transação do banco de dados');

            // Determinar o nome do usuário baseado no role
            $userName = $request->role === 'pharmacy' ? $request->pharmacy_name : $request->name;

            // Criar usuário
            $userData = [
                'name' => $userName,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => $request->role === 'customer' ? 'approved' : 'pending',
                'is_active' => true,
            ];

            $user = User::create($userData);
            Log::info('👤 Usuário criado', ['user_id' => $user->id, 'role' => $user->role]);

            $this->recordLegalAcceptances($user, $request);

            // Se for farmácia, criar registro da farmácia
            if ($request->role === 'pharmacy') {
                Log::info('🏥 Criando registro da farmácia');
                
                $pharmacyData = [
                    'user_id' => $user->id,
                    'name' => $request->pharmacy_name,
                    'license_number' => $request->nif,
                    'address' => $request->address,
                    'municipality' => $request->municipality,
                    'province' => $request->province,
                    'phone' => $user->phone, // Usar telefone do usuário
                    'email' => $user->email, // Usar email do usuário
                    'whatsapp' => $request->whatsapp,
                    'is_verified' => false,
                    'is_active' => false,
                    'accepts_delivery' => $request->boolean('accepts_delivery', true),
                    'delivery_fee' => 0,
                    'minimum_order' => 0,
                ];

                $pharmacy = Pharmacy::create($pharmacyData);
                Log::info('🏥 Farmácia criada', ['pharmacy_id' => $pharmacy->id, 'name' => $pharmacy->name]);

                // Upload e salvar documentos
                if ($request->hasFile('documents')) {
                    Log::info('📄 Processando documentos', ['count' => count($request->file('documents'))]);
                    
                    foreach ($request->file('documents') as $index => $file) {
                        $documentType = $request->document_types[$index] ?? 'other';
                        
                        // Gerar nome único para o arquivo
                        $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('pharmacy-documents', $fileName, 'public');

                        Document::create([
                            'pharmacy_id' => $pharmacy->id,
                            'type' => $documentType,
                            'file_path' => $filePath,
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                            'status' => 'pending',
                        ]);
                    }
                    Log::info('📄 Documentos salvos com sucesso');
                }

                // 🔥 ENVIAR NOTIFICAÇÃO PARA ADMINISTRADORES
                Log::info('🔔 Iniciando processo de notificação');
                $this->notifyAdministrators($user, $pharmacy);
            }

            DB::commit();
            Log::info('✅ Transação commitada com sucesso');

            // Redirecionar baseado no status
            if ($user->status === 'approved') {
                Auth::login($user);
                Log::info('🏠 Redirecionando cliente para welcome');
                return redirect()->route('welcome.success')->with('success', 'Conta criada com sucesso! Bem-vindo ao RammesPharm.');
            } else {
                Auth::login($user);
                Log::info('⏳ Redirecionando farmácia para pending');
                return redirect()->route('account.pending')->with('info', 'Conta criada com sucesso! Aguarde a aprovação da administração.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('💥 Erro durante registro', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Erro ao criar conta: ' . $e->getMessage()])->withInput();
        }
    }

    private function recordLegalAcceptances(User $user, Request $request)
    {
        try {
            $termsOfUse = LegalDocument::getActiveTerms();
            $privacyPolicy = LegalDocument::getActivePrivacyPolicy();

            if ($termsOfUse) {
                UserLegalAcceptance::create([
                    'user_id' => $user->id,
                    'legal_document_id' => $termsOfUse->id,
                    'document_version' => $termsOfUse->version,
                    'accepted_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            if ($privacyPolicy) {
                UserLegalAcceptance::create([
                    'user_id' => $user->id,
                    'legal_document_id' => $privacyPolicy->id,
                    'document_version' => $privacyPolicy->version,
                    'accepted_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            Log::info('📋 Aceitações legais registradas', ['user_id' => $user->id]);
        } catch (\Exception $e) {
            Log::error('❌ Erro ao registrar aceitações legais', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
        }
    }

    public function getLegalDocument($type)
    {
        try {
            $document = null;
            
            if ($type === 'terms') {
                $document = LegalDocument::getActiveTerms();
            } elseif ($type === 'privacy') {
                $document = LegalDocument::getActivePrivacyPolicy();
            }

            if (!$document) {
                return response()->json(['error' => 'Documento não encontrado'], 404);
            }

            return response()->json([
                'title' => $document->title,
                'content' => $document->content,
                'version' => $document->version,
                'effective_date' => $document->effective_date->format('d/m/Y')
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao carregar documento'], 500);
        }
    }

    /**
     * Notificar administradores sobre nova farmácia registrada
     */
    private function notifyAdministrators(User $user, Pharmacy $pharmacy)
    {
        try {
            Log::info('🔍 Buscando administradores para notificar');
            
            // Buscar todos os administradores e managers
            $administrators = User::whereIn('role', ['admin', 'manager'])
                ->where('is_active', true)
                ->get();

            Log::info('👥 Administradores encontrados', [
                'count' => $administrators->count(),
                'admins' => $administrators->pluck('name', 'id')->toArray()
            ]);

            if ($administrators->count() > 0) {
                Log::info('📤 Enviando notificações...');
                
                // Enviar notificação para todos os administradores
                Notification::send($administrators, new NewPharmacyRegistered($user, $pharmacy));
                
                Log::info('✅ Notificações enviadas com sucesso', [
                    'pharmacy_id' => $pharmacy->id,
                    'pharmacy_name' => $pharmacy->name,
                    'user_name' => $user->name,
                    'administrators_count' => $administrators->count(),
                    'channels' => ['mail', 'database']
                ]);

                // Verificar se as notificações foram salvas no banco
                $dbNotifications = \DB::table('notifications')
                    ->where('type', 'App\Notifications\NewPharmacyRegistered')
                    ->where('created_at', '>=', now()->subMinutes(5))
                    ->count();
                
                Log::info('📊 Notificações no banco de dados', ['count' => $dbNotifications]);

            } else {
                Log::warning('⚠️ Nenhum administrador encontrado para notificar', [
                    'pharmacy_id' => $pharmacy->id ?? null,
                    'pharmacy_name' => $pharmacy->name ?? null
                ]);

                // Vamos verificar se existem usuários admin/manager no banco
                $allAdmins = User::whereIn('role', ['admin', 'manager'])->get();
                Log::info('🔍 Todos os admins no banco (incluindo inativos)', [
                    'count' => $allAdmins->count(),
                    'admins' => $allAdmins->pluck('name', 'role')->toArray()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('💥 Erro ao enviar notificações', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'pharmacy_id' => $pharmacy->id ?? null,
                'user_id' => $user->id ?? null
            ]);
        }
    }

    public function showPendingAccount()
    {
        $user = Auth::user();
        
        if (!$user || $user->status === 'approved') {
            return redirect()->route('home');
        }

        return view('auth.account-pending', compact('user'));
    }
}
