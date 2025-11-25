<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Category;
use App\Imports\SimpleProductImport;
use App\Filament\Exports\ProductExporter;
use App\Filament\Imports\ProductImporter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Collection; // Importar Collection para bulk actions

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Meus Produtos';
    protected static ?string $modelLabel = 'Produto';
    protected static ?string $pluralModelLabel = 'Produtos';
    protected static ?string $navigationGroup = 'Minha Farmácia';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return Auth::user()->isPharmacy() || Auth::user()->isAdmin() || Auth::user()->isManager();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informações do Produto')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome do Produto')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                
                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Product::class, 'slug', ignoreRecord: true, modifyRuleUsing: function ($rule, $get) {
                                        return $rule->where('pharmacy_id', Auth::user()->pharmacy->id);
                                    }),
                                    
                                Forms\Components\RichEditor::make('description')
                                    ->label('Descrição')
                                    ->toolbarButtons([
                                        'bold', 'italic', 'underline', 'strike', 'link', 'blockquote', 'codeBlock',
                                        'bulletList', 'orderedList', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                                        'alignLeft', 'alignCenter', 'alignRight', 'alignJustify',
                                    ])
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Section::make('Imagem')
                            ->description('Adicione a imagem principal do produto.')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Imagem do Produto')
                                    ->image()
                                    ->directory('product-images')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(2048)
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '1:1',
                                        '4:3',
                                        '16:9',
                                    ])
                                    ->helperText('Apenas uma imagem. Formatos: JPEG, PNG, WebP. Tamanho máximo: 2MB.'),
                            ])->columns(1),

                        Forms\Components\Section::make('Preços e Estoque')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Preço de Venda (AOA)')
                                    ->numeric()
                                    ->prefix('AOA')
                                    ->required()
                                    ->default(0.00)
                                    ->step(0.01),
                                
                                Forms\Components\TextInput::make('stock_quantity')
                                    ->label('Quantidade em Estoque')
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->minValue(0),
                                
                                Forms\Components\Toggle::make('is_available')
                                    ->label('Disponível para Venda')
                                    ->default(true)
                                    ->helperText('Desmarque se o produto não estiver disponível no momento.'),
                            ])->columns(3),

                        Forms\Components\Section::make('Dados do Fabricante')
                            ->schema([
                                Forms\Components\TextInput::make('manufacturer')
                                    ->label('Fabricante')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('batch_number')
                                    ->label('Número do Lote')
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('expiry_date')
                                    ->label('Data de Validade')
                                    ->native(false),
                            ])->columns(2),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Organização')
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->label('Categoria')
                                    ->options(Category::where('is_active', true)->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Produto Ativo no Catálogo')
                                    ->default(true)
                                    ->helperText('Desmarque para desativar o produto do seu catálogo.'),
                                Forms\Components\Toggle::make('requires_prescription')
                                    ->label('Requer Receita Médica')
                                    ->default(false)
                                    ->helperText('Marque se o medicamento precisa de receita médica'),
                                Forms\Components\Toggle::make('is_controlled')
                                    ->label('Medicamento Controlado')
                                    ->default(false)
                                    ->helperText('Marque se é um medicamento controlado'),
                            ]),
                        
                        Forms\Components\Hidden::make('pharmacy_id')
                            ->default(fn () => Auth::user()->pharmacy->id ?? null),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        $isAdmin = Auth::user()->isAdmin() || Auth::user()->isManager();
        
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagem')
                    ->circular()
                    ->size(80)
                    ->defaultImageUrl('/placeholder.svg?height=40&width=40&text=Prod'),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome do Produto')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->name),
                
                // Coluna da Farmácia (visível apenas para Admin/Manager)
                Tables\Columns\TextColumn::make('pharmacy.name')
                    ->label('Farmácia')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->pharmacy->name ?? 'N/A')
                    ->visible($isAdmin),
                
                // Categoria (visível apenas para Farmácias)
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->visible(!$isAdmin),
                
                Tables\Columns\TextColumn::make('manufacturer')
                    ->label('Fabricante')
                    ->searchable()
                    ->sortable()
                    ->limit(20)
                    ->placeholder('Não informado'),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('Preço')
                    ->money('AOA')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Estoque')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state > 50 => 'success',
                        $state > 10 => 'warning',
                        $state > 0 => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => $state . ' un.')
                    ->sortable(),
                
                // Data de Validade (visível apenas para Farmácias)
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Validade')
                    ->date('d/m/Y')
                    ->badge()
                    ->color(fn ($record) => $record->getExpiryBadgeColor()) // Usa a lógica do modelo
                    ->formatStateUsing(fn ($record) => $record->getExpiryBadgeText()) // Usa a lógica do modelo
                    ->sortable()
                    ->visible(!$isAdmin),
                
                // Disponibilidade (visível apenas para Farmácias)
                Tables\Columns\IconColumn::make('is_available')
                    ->label('Disponível')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($state) => $state ? 'Disponível para venda' : 'Indisponível para venda')
                    ->visible(!$isAdmin),
                
                // Ativo (visível apenas para Farmácias)
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->tooltip(fn ($state) => $state ? 'Visível no catálogo' : 'Inativo no catálogo')
                    ->visible(!$isAdmin),
            ])
            ->filters([
                Tables\Filters\Filter::make('name')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome do Produto')
                            ->placeholder('Buscar por nome...')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['name'],
                            fn (Builder $query, $value): Builder => $query->where('name', 'like', "%{$value}%"),
                        );
                    }),
                Tables\Filters\SelectFilter::make('manufacturer')
                    ->label('Fabricante')
                    ->options(function () {
                        return Product::whereNotNull('manufacturer')
                            ->distinct()
                            ->pluck('manufacturer', 'manufacturer');
                    })
                    ->placeholder('Selecionar fabricante')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('expiry_status')
                    ->label('Status de Validade')
                    ->options([
                        'good' => 'Data Boa',
                        'near' => 'Próximo da Validade',
                        'expired' => 'Vencido',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => match ($value) {
                                'expired' => $query->where('expiry_date', '<', now()),
                                'near' => $query->whereBetween('expiry_date', [
                                    now(),
                                    now()->addDays(Product::DAYS_BEFORE_EXPIRY_TO_DISABLE * 2)
                                ]),
                                'good' => $query->where('expiry_date', '>', now()->addDays(Product::DAYS_BEFORE_EXPIRY_TO_DISABLE * 2)),
                                default => $query,
                            }
                        );
                    })
                    ->visible(!$isAdmin),
                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Disponibilidade')
                    ->placeholder('Todos')
                    ->trueLabel('Disponível')
                    ->falseLabel('Indisponível')
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_available', true),
                        false: fn (Builder $query) => $query->where('is_available', false),
                        blank: fn (Builder $query) => $query,
                    )
                    ->visible(!$isAdmin),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status no Catálogo')
                    ->placeholder('Todos')
                    ->trueLabel('Ativo')
                    ->falseLabel('Inativo')
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_active', true),
                        false: fn (Builder $query) => $query->where('is_active', false),
                        blank: fn (Builder $query) => $query,
                    )
                    ->visible(!$isAdmin),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info')
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make()
                        ->color('warning')
                        ->icon('heroicon-o-pencil')
                        ->visible(!$isAdmin),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->visible(!$isAdmin),
                ])
                ->button()
                ->color('gray')
                ->size('sm')
                ->icon('heroicon-o-ellipsis-vertical')
                ->tooltip('Ações'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('toggle_availability')
                    ->label(fn (?Collection $records) => ($records && $records->contains('is_available', false)) ? 'Habilitar Selecionados' : 'Desabilitar Selecionados')
                    ->icon(fn (?Collection $records) => ($records && $records->contains('is_available', false)) ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn (?Collection $records) => ($records && $records->contains('is_available', false)) ? 'success' : 'danger')
                    ->action(function (Collection $records) {
                        $newState = $records->contains('is_available', false); // Se algum estiver false, a nova ação é habilitar
                        $records->each->update(['is_available' => $newState]);
                        Notification::make()
                            ->title("Produtos " . ($newState ? "habilitados" : "desabilitados") . " com sucesso!")
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),

                Tables\Actions\BulkAction::make('toggle_active')
                    ->label(fn (?Collection $records) => ($records && $records->contains('is_active', false)) ? 'Ativar Selecionados' : 'Desativar Selecionados')
                    ->icon(fn (?Collection $records) => ($records && $records->contains('is_active', false)) ? 'heroicon-o-eye' : 'heroicon-o-eye-slash')
                    ->color(fn (?Collection $records) => ($records && $records->contains('is_active', false)) ? 'info' : 'gray')
                    ->action(function (Collection $records) {
                        $newState = $records->contains('is_active', false); // Se algum estiver false, a nova ação é ativar
                        $records->each->update(['is_active' => $newState]);
                        Notification::make()
                            ->title("Produtos " . ($newState ? "ativados" : "desativados") . " com sucesso!")
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion()
                    ,
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Novo Produto')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->button()
                    ->visible(!$isAdmin),
                Tables\Actions\Action::make('check_expiry')
                    ->label('Verificar Validades')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->action(function () {
                        $products = Product::whereNotNull('expiry_date')
                            ->where('is_available', true)
                            ->get();

                        $disabledCount = 0;
                        foreach ($products as $product) {
                            if ($product->shouldBeDisabledByExpiry()) {
                                $product->update(['is_available' => false]);
                                $disabledCount++;
                            }
                        }

                        Notification::make()
                            ->title('Verificação de validade concluída')
                            ->body("{$disabledCount} produtos foram desabilitados por estarem próximos da validade.")
                            ->success()
                            ->send();
                    })
                    ->visible(!$isAdmin),
                    Tables\Actions\ExportAction::make()
                        ->exporter(ProductExporter::class)
                        ->label('Exportar')
                        ->visible(!$isAdmin),
                    Tables\Actions\ImportAction::make()
                        ->importer(ProductImporter::class)
                        ->label('Importar')
                        ->visible(!$isAdmin),
            ])
            ->emptyStateHeading('Nenhum produto encontrado')
            ->emptyStateDescription('Comece criando seu primeiro produto para sua farmácia.')
            ->emptyStateIcon('heroicon-o-cube')
            ->defaultSort('created_at', 'desc')
            ->striped(false)
            ->paginated([12, 24, 48, 'all'])
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description', 'manufacturer'];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['category', 'pharmacy']); // Eager load relationships for columns
        
        if (Auth::user()->isPharmacy()) {
            $query->where('pharmacy_id', Auth::user()->pharmacy->id);
        }
        
        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        if (Auth::user()->isPharmacy()) {
            return static::getModel()::where('pharmacy_id', Auth::user()->pharmacy->id)->count();
        }
        
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}
