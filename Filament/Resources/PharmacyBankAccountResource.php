<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PharmacyBankAccountResource\Pages;
use App\Models\PharmacyBankAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PharmacyBankAccountResource extends Resource
{
    protected static ?string $model = PharmacyBankAccount::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Coordenadas Bancárias';
    protected static ?string $modelLabel = 'Conta Bancária';
    protected static ?string $pluralModelLabel = 'Coordenadas Bancárias';
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Bancárias')
                    ->schema([
                        Forms\Components\TextInput::make('bank_name')
                            ->label('Nome do Banco')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Banco Angolano de Investimentos'),
                        
                        Forms\Components\TextInput::make('account_holder')
                            ->label('Titular da Conta')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nome completo do titular'),
                        
                        Forms\Components\TextInput::make('account_number')
                            ->label('Número da Conta')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('Ex: 0001000001100'),
                        
                        Forms\Components\TextInput::make('iban')
                            ->label('IBAN (Opcional)')
                            ->maxLength(34)
                            ->placeholder('Ex: AO06000100010011001013018'),
                        
                        Forms\Components\TextInput::make('swift_code')
                            ->label('Código SWIFT (Opcional)')
                            ->maxLength(11)
                            ->placeholder('Ex: BAIAAOAO'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Configurações')
                    ->schema([
                        Forms\Components\Toggle::make('is_primary')
                            ->label('Conta Principal')
                            ->helperText('Esta será a conta padrão para recebimentos')
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $set('is_active', true);
                                }
                            }),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Conta Ativa')
                            ->helperText('Desative para pausar o uso desta conta')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bank_name')
                    ->label('Banco')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('account_holder')
                    ->label('Titular')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('account_number')
                    ->label('Número da Conta')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => 
                        strlen($state) > 8 
                            ? substr($state, 0, 4) . ' **** **** ' . substr($state, -4)
                            : $state
                    ),
                
                Tables\Columns\TextColumn::make('iban')
                    ->label('IBAN')
                    ->placeholder('—')
                    ->formatStateUsing(fn (?string $state): string => 
                        $state && strlen($state) > 8 
                            ? substr($state, 0, 4) . ' **** **** ' . substr($state, -4) 
                            : ($state ?? '—')
                    ),
                
                Tables\Columns\IconColumn::make('is_primary')
                    ->label('Principal')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_primary')
                    ->label('Conta Principal')
                    ->placeholder('Todas')
                    ->trueLabel('Principal')
                    ->falseLabel('Secundária'),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Todas')
                    ->trueLabel('Ativas')
                    ->falseLabel('Inativas'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('is_primary', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // Se for farmácia, mostrar apenas suas contas
        if (Auth::user()->isPharmacy()) {
            $query->where('pharmacy_id', Auth::user()->pharmacy->id);
        }
        
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPharmacyBankAccounts::route('/'),
            'create' => Pages\CreatePharmacyBankAccount::route('/create'),
            'edit' => Pages\EditPharmacyBankAccount::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()->isPharmacy() || Auth::user()->isAdmin() || Auth::user()->isManager();
    }
}
