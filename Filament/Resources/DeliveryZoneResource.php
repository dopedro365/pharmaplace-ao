<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryZoneResource\Pages;
use App\Models\DeliveryZone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DeliveryZoneResource extends Resource
{
    protected static ?string $model = DeliveryZone::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Zonas de Entrega';
    protected static ?string $modelLabel = 'Zona de Entrega';
    protected static ?string $pluralModelLabel = 'Zonas de Entrega';
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações da Zona')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('municipality')
                                    ->label('Município')
                                    ->required()
                                    ->maxLength(100),
                                
                                Forms\Components\TextInput::make('province')
                                    ->label('Província')
                                    ->required()
                                    ->maxLength(100),
                            ]),
                        
                        Forms\Components\TextInput::make('zone_name')
                            ->label('Nome da Zona (Opcional)')
                            ->helperText('Ex: Centro, Periferia, Zona Industrial')
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Configurações de Entrega')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('delivery_fee')
                                    ->label('Taxa de Entrega (Kz)')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Kz')
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->helperText('Taxa específica para esta zona'),
                                
                                Forms\Components\TextInput::make('delivery_time_minutes')
                                    ->label('Tempo de Entrega (minutos)')
                                    ->required()
                                    ->numeric()
                                    ->suffix('min')
                                    ->minValue(1)
                                    ->default(60),
                                
                                Forms\Components\TextInput::make('minimum_order')
                                    ->label('Pedido Mínimo (Kz)')
                                    ->numeric()
                                    ->prefix('Kz')
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->default(0)
                                    ->helperText('Sobrescreve o pedido mínimo global'),
                            ]),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Zona Ativa')
                            ->helperText('Desative para pausar entregas nesta zona')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('municipality')
                    ->label('Município')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('province')
                    ->label('Província')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('zone_name')
                    ->label('Nome da Zona')
                    ->searchable()
                    ->placeholder('—'),
                
                Tables\Columns\TextColumn::make('delivery_fee')
                    ->label('Taxa de Entrega')
                    ->money('AOA')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('delivery_time_minutes')
                    ->label('Tempo de Entrega')
                    ->suffix(' min')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('minimum_order')
                    ->label('Pedido Mínimo')
                    ->money('AOA')
                    ->sortable(),
                
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
                Tables\Filters\SelectFilter::make('province')
                    ->label('Província')
                    ->options([
                        'Luanda' => 'Luanda',
                        'Benguela' => 'Benguela',
                        'Huambo' => 'Huambo',
                        'Huíla' => 'Huíla',
                        'Cabinda' => 'Cabinda',
                        'Cunene' => 'Cunene',
                        'Namibe' => 'Namibe',
                        'Cuando Cubango' => 'Cuando Cubango',
                        'Lunda Norte' => 'Lunda Norte',
                        'Lunda Sul' => 'Lunda Sul',
                        'Malanje' => 'Malanje',
                        'Moxico' => 'Moxico',
                        'Uíge' => 'Uíge',
                        'Zaire' => 'Zaire',
                        'Bengo' => 'Bengo',
                        'Bié' => 'Bié',
                        'Cuanza Norte' => 'Cuanza Norte',
                        'Cuanza Sul' => 'Cuanza Sul',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Todos')
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // Se for farmácia, mostrar apenas suas zonas
        if (Auth::user()->isPharmacy()) {
            $query->where('pharmacy_id', Auth::user()->pharmacy->id);
        }
        
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeliveryZones::route('/'),
            'create' => Pages\CreateDeliveryZone::route('/create'),
            'edit' => Pages\EditDeliveryZone::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()->isPharmacy() || Auth::user()->isAdmin() || Auth::user()->isManager();
    }
}
