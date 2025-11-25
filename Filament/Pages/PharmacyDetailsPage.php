<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use App\Models\Pharmacy;
use Illuminate\Support\Facades\Auth;

class PharmacyDetailsPage extends Page
{
    // Define o ícone de navegação (não será visível na navegação principal, mas é bom ter)
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    // Define o label de navegação (também não será visível na navegação principal)
    protected static ?string $navigationLabel = 'Detalhes da Farmácia';
    // Define o slug da URL, com um parâmetro dinâmico para o ID da farmácia
    protected static ?string $slug = 'pharmacies/{record}/details';
    // Define a view Blade para esta página (pode ser mínima se usar apenas Infolists)
    protected static string $view = 'filament.pages.pharmacy-details-page';

    // Propriedade para armazenar o registro da farmácia
    public Pharmacy $record;

    /**
     * Método de montagem da página, executado ao carregar.
     * Carrega o registro da farmácia e verifica a autorização.
     */
    public function mount(Pharmacy $record): void
    {
        $this->record = $record;
        // Verifica se o usuário logado é Admin ou Manager
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403); // Acesso negado se não for Admin ou Manager
        }
    }

    /**
     * Define o título da página.
     */
    public function getTitle(): string
    {
        return 'Detalhes da Farmácia: ' . $this->record->name;
    }

    /**
     * Define o esquema do Infolist para exibir os dados da farmácia.
     */
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record) // Associa o Infolist ao registro da farmácia
            ->schema([
                // Seção de Informações Essenciais da Farmácia
                Components\Section::make('Informações Essenciais da Farmácia')
                    ->schema([
                        Components\Split::make([
                            Components\Grid::make(2)
                                ->schema([
                                    Components\TextEntry::make('name')
                                        ->label('Nome da Farmácia')
                                        ->size('lg')
                                        ->weight('bold'),
                                    Components\TextEntry::make('license_number')
                                        ->label('Nº de Licença')
                                        ->placeholder('Não informado'),
                                    Components\TextEntry::make('email')
                                        ->label('Email')
                                        ->copyable()
                                        ->placeholder('Não informado'),
                                    Components\TextEntry::make('phone')
                                        ->label('Telefone')
                                        ->copyable()
                                        ->placeholder('Não informado'),
                                    Components\TextEntry::make('whatsapp')
                                        ->label('WhatsApp')
                                        ->copyable()
                                        ->placeholder('Não informado'),
                                    Components\TextEntry::make('address')
                                        ->label('Endereço')
                                        ->placeholder('Não informado')
                                        ->columnSpanFull(),
                                    Components\IconEntry::make('is_verified')
                                        ->label('Verificada')
                                        ->boolean()
                                        ->trueIcon('heroicon-o-check-badge')
                                        ->falseIcon('heroicon-o-x-mark')
                                        ->trueColor('success')
                                        ->falseColor('danger'),
                                    Components\IconEntry::make('is_active')
                                        ->label('Ativa no Sistema')
                                        ->boolean()
                                        ->trueIcon('heroicon-o-check-circle')
                                        ->falseIcon('heroicon-o-x-circle')
                                        ->trueColor('success')
                                        ->falseColor('danger'),
                                ]),
                            Components\ImageEntry::make('logo')
                                ->label('Logo da Farmácia')
                                ->defaultImageUrl('/placeholder.svg?height=150&width=150&text=Logo')
                                ->height(150)
                                ->width(150)
                                ->circular(),
                        ])->from('lg'),
                    ]),

                // Seção de Estatísticas de Produtos
                Components\Section::make('Estatísticas de Produtos')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('products_count')
                                    ->label('Total de Produtos')
                                    // Usa o método getProductsCount() do modelo Pharmacy
                                    ->getStateUsing(fn (Pharmacy $record) => $record->getProductsCount())
                                    ->badge()
                                    ->color('primary'),
                                Components\TextEntry::make('active_products_count')
                                    ->label('Produtos Ativos')
                                    // Usa o método getActiveProductsCount() do modelo Pharmacy
                                    ->getStateUsing(fn (Pharmacy $record) => $record->getActiveProductsCount())
                                    ->badge()
                                    ->color('success'),
                            ]),
                        Components\TextEntry::make('product_categories')
                            ->label('Categorias de Produtos')
                            ->getStateUsing(function (Pharmacy $record) {
                                // Busca as categorias únicas dos produtos da farmácia
                                $categories = $record->products()->with('category')->get()->pluck('category.name')->filter()->unique()->sort()->implode(', ');
                                return $categories ?: 'Nenhuma categoria de produto encontrada.';
                            })
                            ->columnSpanFull(),
                    ]),

                // Seção de Informações da Conta do Usuário
                Components\Section::make('Informações da Conta')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('user.name')
                                    ->label('Nome do Usuário Responsável')
                                    ->placeholder('Não associado'),
                                Components\TextEntry::make('user.email')
                                    ->label('Email do Usuário')
                                    ->copyable()
                                    ->placeholder('Não associado'),
                                Components\TextEntry::make('user.created_at')
                                    ->label('Conta Criada Em')
                                    ->dateTime()
                                    ->placeholder('Não associado'),
                            ]),
                    ]),
            ]);
    }

    /**
     * Desabilita a exibição desta página na navegação principal do Filament.
     * Ela será acessada apenas via link direto.
     */
    protected static bool $shouldRegisterNavigation = false;
}
