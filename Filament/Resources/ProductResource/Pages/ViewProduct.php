<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Filament\Pages\PharmacyDetailsPage; // Importar a nova página

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->color('warning')
                ->icon('heroicon-o-pencil'),
            Actions\DeleteAction::make()
                ->color('danger')
                ->icon('heroicon-o-trash'),
            
            // Ação para Habilitar/Desabilitar (is_available)
            Actions\Action::make('toggle_availability')
                ->label(fn ($record) => $record->is_available ? 'Marcar Indisponível' : 'Marcar Disponível')
                ->icon(fn ($record) => $record->is_available ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn ($record) => $record->is_available ? 'danger' : 'success')
                ->action(function ($record) {
                    $record->update(['is_available' => !$record->is_available]);
                    Notification::make()
                        ->title('Status de disponibilidade atualizado!')
                        ->success()
                        ->send();
                    $this->refreshFormData(['is_available']); // Atualiza a interface
                }),
            
            // Ação para Ativar/Desativar (is_active)
            Actions\Action::make('toggle_active')
                ->label(fn ($record) => $record->is_active ? 'Desativar Produto' : 'Ativar Produto')
                ->icon(fn ($record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                ->color(fn ($record) => $record->is_active ? 'gray' : 'info')
                ->action(function ($record) {
                    $record->update(['is_active' => !$record->is_active]);
                    Notification::make()
                        ->title('Status de ativação atualizado!')
                        ->success()
                        ->send();
                    $this->refreshFormData(['is_active']); // Atualiza a interface
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $isAdminOrManager = Auth::user()->isAdmin() || Auth::user()->isManager();

        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações do Produto')
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\TextEntry::make('name')
                                        ->label('Nome do Produto')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->color('primary'),
                                    Infolists\Components\TextEntry::make('category.name')
                                        ->label('Categoria')
                                        ->badge()
                                        ->color('info'),
                                    Infolists\Components\TextEntry::make('manufacturer')
                                        ->label('Fabricante')
                                        ->placeholder('Não informado'),
                                    Infolists\Components\TextEntry::make('barcode')
                                        ->label('Código de Barras')
                                        ->copyable()
                                        ->placeholder('Não informado'),
                                ]),
                            Infolists\Components\ImageEntry::make('image')
                                ->label('Imagem')
                                ->height(250)
                                ->circular()
                                ->width(250),
                        ])->from('lg'),
                    ]),

                // Seção de Informações da Farmácia, visível apenas para Admin/Manager
                Infolists\Components\Section::make('Informações da Farmácia')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('pharmacy.name')
                                    ->label('Nome da Farmácia')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('primary')
                                    ->url(function ($record) {
                                        // Verifica se a farmácia existe e se o usuário é admin/manager
                                        if ($record->pharmacy && (Auth::user()->isAdmin() || Auth::user()->isManager())) {
                                            // Link para a nova PharmacyDetailsPage
                                            return PharmacyDetailsPage::getUrl(['record' => $record->pharmacy->id]);
                                        }
                                        return null;
                                    })
                                    //->openUrlInNewTab()
                                    ->tooltip('Clique para ver detalhes completos da farmácia'),
                                Infolists\Components\TextEntry::make('pharmacy.email')
                                    ->label('Email da Farmácia')
                                    ->copyable()
                                    ->placeholder('Não informado'),
                                Infolists\Components\TextEntry::make('pharmacy.phone')
                                    ->label('Telefone da Farmácia')
                                    ->copyable()
                                    ->placeholder('Não informado'),
                                Infolists\Components\TextEntry::make('pharmacy.address')
                                    ->label('Endereço da Farmácia')
                                    ->placeholder('Não informado')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->visible($isAdminOrManager) // Condição de visibilidade
                    ->collapsible(), // Torna a seção recolhível

                Infolists\Components\Section::make('Preços e Estoque')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('price')
                                    ->label('Preço')
                                    ->money('AOA')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('success'),
                                Infolists\Components\TextEntry::make('stock_quantity')
                                    ->label('Estoque')
                                    ->badge()
                                    ->color(fn (string $state): string => match (true) {
                                        $state > 50 => 'success',
                                        $state > 10 => 'warning',
                                        $state > 0 => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => $state . ' unidades'),
                                Infolists\Components\IconEntry::make('is_available')
                                    ->label('Disponível para Venda')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Status e Regulamentação')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Produto Ativo no Catálogo')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                                Infolists\Components\IconEntry::make('requires_prescription')
                                    ->label('Requer Receita')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-document-text')
                                    ->falseIcon('heroicon-o-minus')
                                    ->trueColor('warning')
                                    ->falseColor('gray'),
                                Infolists\Components\IconEntry::make('is_controlled')
                                    ->label('Medicamento Controlado')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-shield-exclamation')
                                    ->falseIcon('heroicon-o-minus')
                                    ->trueColor('danger')
                                    ->falseColor('gray'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Descrição')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->label('')
                            ->html()
                            ->placeholder('Nenhuma descrição fornecida'),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Informações Médicas')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('composition')
                                    ->label('Composição')
                                    ->placeholder('Não informado'),
                                Infolists\Components\TextEntry::make('indications')
                                    ->label('Indicações')
                                    ->placeholder('Não informado'),
                                Infolists\Components\TextEntry::make('contraindications')
                                    ->label('Contraindicações')
                                    ->placeholder('Não informado'),
                                Infolists\Components\TextEntry::make('dosage')
                                    ->label('Posologia')
                                    ->placeholder('Não informado'),
                            ]),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Dados do Fabricante')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('batch_number')
                                    ->label('Número do Lote')
                                    ->placeholder('Não informado'),
                                Infolists\Components\TextEntry::make('expiry_date')
                                    ->label('Data de Validade')
                                    ->date()
                                    ->placeholder('Não informado'),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Criado em')
                                    ->dateTime(),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
