<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pharmacy;

class SettingsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.settings-page';
    protected static ?string $title = 'Configurações';
    protected static ?string $navigationLabel = 'Configurações';
    protected static ?int $navigationSort = 100;

    // ✅ Usar array data para todos os campos (incluindo uploads)
    public array $data = [];

    // Dados de senha (separados do data)
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // ✅ Valores originais para validação condicional
    protected string $original_email = '';
    protected string $original_phone = '';
    protected string $original_license_number = '';

    public function mount(): void
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                abort(403);
            }
            
            // ✅ Inicializar data com valores do usuário
            $this->data = [
                'name' => $user->name ?? '',
                'email' => $user->email ?? '',
                'phone' => $user->phone ?? '',
                'avatar' => null, // Upload sempre null inicialmente
            ];
            
            // Guardar valores originais
            $this->original_email = $user->email ?? '';
            $this->original_phone = $user->phone ?? '';

            if ($user->isPharmacy() && $user->pharmacy) {
                $pharmacy = $user->pharmacy;
                
                // ✅ Adicionar dados da farmácia ao array data
                $this->data = array_merge($this->data, [
                    'name' => $pharmacy->name ?? $user->name ?? '', // Nome da farmácia tem prioridade
                    'pharmacy_name' => $pharmacy->name ?? '',
                    'pharmacy_license_number' => $pharmacy->license_number ?? '',
                    'pharmacy_description' => $pharmacy->description ?? '',
                    'pharmacy_address' => $pharmacy->address ?? '',
                    'pharmacy_municipality' => $pharmacy->municipality ?? '',
                    'pharmacy_province' => $pharmacy->province ?? '',
                    'pharmacy_phone' => $pharmacy->phone ?? '',
                    'pharmacy_email' => $pharmacy->email ?? '',
                    'pharmacy_whatsapp' => $pharmacy->whatsapp ?? '',
                    'pharmacy_logo' => null, // Upload sempre null inicialmente
                    'pharmacy_images' => null, // Upload sempre null inicialmente
                    'accepts_delivery' => (bool) ($pharmacy->accepts_delivery ?? false),
                    'delivery_fee' => (float) ($pharmacy->delivery_fee ?? 0),
                    'delivery_time_minutes' => (int) ($pharmacy->delivery_time_minutes ?? 30),
                    'minimum_order' => (float) ($pharmacy->minimum_order ?? 0),
                ]);
                
                // Valor original da licença
                $this->original_license_number = $pharmacy->license_number ?? '';
            }
            
        } catch (\Exception $e) {
            \Log::error('Error in SettingsPage mount', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            // ✅ Valores padrão seguros em caso de erro
            $this->data = [
                'name' => '',
                'email' => '',
                'phone' => '',
                'avatar' => null,
                'pharmacy_name' => '',
                'pharmacy_license_number' => '',
                'pharmacy_description' => '',
                'pharmacy_address' => '',
                'pharmacy_municipality' => '',
                'pharmacy_province' => '',
                'pharmacy_phone' => '',
                'pharmacy_email' => '',
                'pharmacy_whatsapp' => '',
                'pharmacy_logo' => null,
                'pharmacy_images' => null,
                'accepts_delivery' => false,
                'delivery_fee' => 0,
                'delivery_time_minutes' => 30,
                'minimum_order' => 0,
            ];
        }
    }

    public function form(Form $form): Form
    {
        $user = Auth::user();
        $isPharmacy = $user && $user->isPharmacy();
        
        $schema = [];

        if ($isPharmacy) {
            // Para farmácias: Informações da Farmácia (principal)
            $schema[] = Forms\Components\Section::make('Informações da Farmácia')
                ->description('Dados principais da sua farmácia')
                ->icon('heroicon-o-building-storefront')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('pharmacy_name')
                                ->label('Nome da Farmácia')
                                ->required()
                                ->maxLength(255),
                            
                            Forms\Components\TextInput::make('pharmacy_license_number')
                                ->label('NIF / Licença')
                                ->required()
                                ->maxLength(50),
                        ]),
                    
                    Forms\Components\Textarea::make('pharmacy_description')
                        ->label('Descrição da Farmácia')
                        ->rows(3)
                        ->maxLength(1000)
                        ->helperText('Descreva os serviços e especialidades da sua farmácia'),
                    
                    Forms\Components\TextInput::make('pharmacy_address')
                        ->label('Endereço Completo')
                        ->required()
                        ->maxLength(500),
                    
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('pharmacy_municipality')
                                ->label('Município')
                                ->required()
                                ->maxLength(100),
                            
                            Forms\Components\TextInput::make('pharmacy_province')
                                ->label('Província')
                                ->required()
                                ->maxLength(100),
                        ]),
                ]);

            // Informações de Contato
            $schema[] = Forms\Components\Section::make('Informações de Contato')
                ->description('Dados de contato da farmácia e do responsável')
                ->icon('heroicon-o-phone')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('email')
                                ->label('Email Principal')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->helperText('Email usado para login'),
                            
                            Forms\Components\TextInput::make('phone')
                                ->label('Telefone Principal')
                                ->tel()
                                ->required()
                                ->maxLength(20)
                                ->helperText('Telefone usado para login'),
                        ]),
                    
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('pharmacy_phone')
                                ->label('Telefone da Farmácia')
                                ->tel()
                                ->maxLength(20)
                                ->helperText('Telefone público da farmácia'),
                            
                            Forms\Components\TextInput::make('pharmacy_email')
                                ->label('Email da Farmácia')
                                ->email()
                                ->maxLength(255)
                                ->helperText('Email público da farmácia'),
                            
                            Forms\Components\TextInput::make('pharmacy_whatsapp')
                                ->label('WhatsApp')
                                ->tel()
                                ->maxLength(20)
                                ->helperText('Número para WhatsApp'),
                        ]),
                ]);

            // Logo e Imagens da Farmácia
            $schema[] = Forms\Components\Section::make('Logo e Imagens')
                ->description('Logo e imagens da farmácia')
                ->icon('heroicon-o-photo')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\FileUpload::make('pharmacy_logo')
                        ->label('Logo da Farmácia')
                        ->image()
                        ->directory('pharmacy-logos')
                        ->visibility('public')
                        ->maxSize(2048)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->helperText('Logo principal da farmácia (será usado como avatar)'),
                    
                    Forms\Components\FileUpload::make('pharmacy_images')
                        ->label('Imagens da Farmácia')
                        ->image()
                        ->multiple()
                        ->directory('pharmacy-images')
                        ->visibility('public')
                        ->maxSize(2048)
                        ->maxFiles(5)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->helperText('Até 5 imagens da farmácia (máx. 2MB cada)'),
                ]);

            // Configurações de Entrega
            $schema[] = Forms\Components\Section::make('Configurações de Entrega')
                ->description('Configure as opções de entrega da sua farmácia')
                ->icon('heroicon-o-truck')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Toggle::make('accepts_delivery')
                        ->label('Aceita Entregas')
                        ->helperText('Ative para permitir entregas em domicílio')
                        ->live(),
                    
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('delivery_fee')
                                ->label('Taxa de Entrega Padrão (Kz)')
                                ->numeric()
                                ->prefix('Kz')
                                ->step(0.01)
                                ->minValue(0)
                                ->helperText('Taxa padrão (pode ser sobrescrita por zona)')
                                ->visible(fn (Forms\Get $get) => $get('accepts_delivery')),
                            
                            Forms\Components\TextInput::make('delivery_time_minutes')
                                ->label('Tempo de Entrega (minutos)')
                                ->numeric()
                                ->suffix('min')
                                ->minValue(1)
                                ->maxValue(1440)
                                ->helperText('Tempo estimado de entrega')
                                ->visible(fn (Forms\Get $get) => $get('accepts_delivery')),
                            
                            Forms\Components\TextInput::make('minimum_order')
                                ->label('Pedido Mínimo (Kz)')
                                ->numeric()
                                ->prefix('Kz')
                                ->step(0.01)
                                ->minValue(0)
                                ->helperText('Valor mínimo para pedidos')
                                ->visible(fn (Forms\Get $get) => $get('accepts_delivery')),
                        ]),
                ]);

        } else {
            // Para usuários não-farmácia: Informações Pessoais
            $schema[] = Forms\Components\Section::make('Informações Pessoais')
                ->description('Atualize suas informações básicas')
                ->icon('heroicon-o-user')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nome Completo')
                                ->required()
                                ->maxLength(255),
                            
                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                        ]),
                    
                    Forms\Components\TextInput::make('phone')
                        ->label('Telefone')
                        ->tel()
                        ->required()
                        ->maxLength(20),
                    
                    Forms\Components\FileUpload::make('avatar')
                        ->label('Avatar')
                        ->image()
                        ->directory('avatars')
                        ->visibility('public')
                        ->maxSize(2048)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->helperText('Sua foto de perfil (máx. 2MB)'),
                ]);
        }

        return $form->schema($schema)->statePath('data');
    }

    public function save(): void
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                throw new \Exception('Usuário não encontrado');
            }
            
            $isPharmacy = $user->isPharmacy();

            if ($isPharmacy) {
                // ✅ Validação básica primeiro
                $this->validate([
                    'data.pharmacy_name' => 'required|string|max:255',
                    'data.pharmacy_license_number' => 'required|string|max:50',
                    'data.pharmacy_address' => 'required|string|max:500',
                    'data.pharmacy_municipality' => 'required|string|max:100',
                    'data.pharmacy_province' => 'required|string|max:100',
                    'data.email' => 'required|email|max:255',
                    'data.phone' => 'required|string|max:20',
                    'data.pharmacy_email' => 'nullable|email|max:255',
                    'data.delivery_fee' => 'nullable|numeric|min:0',
                    'data.delivery_time_minutes' => 'nullable|integer|min:1|max:1440',
                    'data.minimum_order' => 'nullable|numeric|min:0',
                ]);

                // ✅ Validação condicional para NIF
                if ($this->data['pharmacy_license_number'] !== $this->original_license_number) {
                    $existingPharmacy = Pharmacy::where('license_number', $this->data['pharmacy_license_number'])
                        ->where('id', '!=', $user->pharmacy->id)
                        ->first();
                    
                    if ($existingPharmacy) {
                        $this->addError('data.pharmacy_license_number', 'Este NIF / Licença já está sendo usado por outra farmácia.');
                        return;
                    }
                }

                // ✅ Validação condicional para email
                if ($this->data['email'] !== $this->original_email) {
                    $existingUser = User::where('email', $this->data['email'])
                        ->where('id', '!=', $user->id)
                        ->first();
                    
                    if ($existingUser) {
                        $this->addError('data.email', 'Este email já está sendo usado por outro usuário.');
                        return;
                    }
                }

                // ✅ Validação condicional para telefone
                if ($this->data['phone'] !== $this->original_phone) {
                    $existingUser = User::where('phone', $this->data['phone'])
                        ->where('id', '!=', $user->id)
                        ->first();
                    
                    if ($existingUser) {
                        $this->addError('data.phone', 'Este telefone já está sendo usado por outro usuário.');
                        return;
                    }
                }

                // ✅ Processar uploads de forma segura
                $avatarPath = $this->data['pharmacy_logo'] ?? null;
                $imagesArray = [];
                
                if (isset($this->data['pharmacy_images']) && $this->data['pharmacy_images']) {
                    $imagesArray = is_array($this->data['pharmacy_images']) ? $this->data['pharmacy_images'] : [$this->data['pharmacy_images']];
                }

                // Atualizar usuário
                $user->update([
                    'name' => $this->data['pharmacy_name'],
                    'email' => $this->data['email'],
                    'phone' => $this->data['phone'],
                    'avatar' => $avatarPath,
                ]);

                // Atualizar farmácia
                $user->pharmacy->update([
                    'name' => $this->data['pharmacy_name'],
                    'license_number' => $this->data['pharmacy_license_number'],
                    'description' => $this->data['pharmacy_description'] ?? '',
                    'address' => $this->data['pharmacy_address'],
                    'municipality' => $this->data['pharmacy_municipality'],
                    'province' => $this->data['pharmacy_province'],
                    'phone' => $this->data['pharmacy_phone'] ?? '',
                    'email' => $this->data['pharmacy_email'] ?? '',
                    'whatsapp' => $this->data['pharmacy_whatsapp'] ?? '',
                    'logo' => $avatarPath,
                    'images' => $imagesArray,
                    'accepts_delivery' => $this->data['accepts_delivery'] ?? false,
                    'delivery_fee' => $this->data['delivery_fee'] ?? 0,
                    'delivery_time_minutes' => $this->data['delivery_time_minutes'] ?? 30,
                    'minimum_order' => $this->data['minimum_order'] ?? 0,
                ]);
            } else {
                // ✅ Validação para usuários normais
                $this->validate([
                    'data.name' => 'required|string|max:255',
                    'data.email' => 'required|email|max:255',
                    'data.phone' => 'required|string|max:20',
                ]);

                // ✅ Validação condicional para email
                if ($this->data['email'] !== $this->original_email) {
                    $existingUser = User::where('email', $this->data['email'])
                        ->where('id', '!=', $user->id)
                        ->first();
                    
                    if ($existingUser) {
                        $this->addError('data.email', 'Este email já está sendo usado por outro usuário.');
                        return;
                    }
                }

                // ✅ Validação condicional para telefone
                if ($this->data['phone'] !== $this->original_phone) {
                    $existingUser = User::where('phone', $this->data['phone'])
                        ->where('id', '!=', $user->id)
                        ->first();
                    
                    if ($existingUser) {
                        $this->addError('data.phone', 'Este telefone já está sendo usado por outro usuário.');
                        return;
                    }
                }

                $user->update([
                    'name' => $this->data['name'],
                    'email' => $this->data['email'],
                    'phone' => $this->data['phone'],
                    'avatar' => $this->data['avatar'] ?? null,
                ]);
            }

            // ✅ Atualizar valores originais
            $this->original_email = $this->data['email'];
            $this->original_phone = $this->data['phone'];
            if ($isPharmacy) {
                $this->original_license_number = $this->data['pharmacy_license_number'];
            }

            Notification::make()
                ->title('Configurações atualizadas com sucesso!')
                ->success()
                ->send();

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar configurações: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'data' => $this->data,
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::make()
                ->title('Erro ao salvar configurações!')
                ->body('Tente novamente ou contacte o suporte.')
                ->danger()
                ->send();
        }
    }

    public function updatePassword(): void
    {
        try {
            $this->validate([
                'current_password' => 'required|current_password',
                'password' => 'required|min:8|confirmed',
            ]);

            $user = Auth::user();
            $user->update([
                'password' => Hash::make($this->password),
            ]);

            $this->current_password = '';
            $this->password = '';
            $this->password_confirmation = '';

            Notification::make()
                ->title('Senha atualizada com sucesso!')
                ->success()
                ->send();
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar senha: ' . $e->getMessage());
            
            Notification::make()
                ->title('Erro ao atualizar senha!')
                ->body('Tente novamente.')
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Salvar Alterações')
                ->submit('save')
                ->color('primary')
                ->icon('heroicon-o-check'),
        ];
    }

    public function getPasswordForm(): Form
    {
        return Form::make($this)
            ->schema([
                Forms\Components\Section::make('Alterar Senha')
                    ->description('Atualize sua senha de acesso')
                    ->icon('heroicon-o-lock-closed')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Senha Atual')
                            ->password()
                            ->required(),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('password')
                                    ->label('Nova Senha')
                                    ->password()
                                    ->required()
                                    ->minLength(8)
                                    ->same('password_confirmation'),
                                
                                Forms\Components\TextInput::make('password_confirmation')
                                    ->label('Confirmar Nova Senha')
                                    ->password()
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    public static function canAccess(): bool
    {
        return Auth::check();
    }
}
