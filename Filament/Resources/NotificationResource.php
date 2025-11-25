<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\DB;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Actions as InfolistActions;
use Filament\Infolists\Components\Actions\Action as InfolistAction;

class NotificationResource extends Resource
{
    protected static ?string $model = DatabaseNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'Notificações';
    protected static ?string $modelLabel = 'Notificação';
    protected static ?string $pluralModelLabel = 'Notificações';
    protected static ?int $navigationSort = 10;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('notifiable_id', Auth::id())
            ->where('notifiable_type', Auth::user()::class)
            ->latest();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->disabled(),
                Forms\Components\Textarea::make('data')
                    ->required()
                    ->disabled(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detalhes da Notificação')
                    ->schema([
                        TextEntry::make('data.title')
                            ->label('Título')
                            ->size('lg')
                            ->weight('bold'),
                            
                        TextEntry::make('data.message')
                            ->label('Mensagem Completa')
                            ->columnSpanFull(),
                            
                        TextEntry::make('created_at')
                            ->label('Data de Criação')
                            ->dateTime('d/m/Y H:i:s'),
                            
                        TextEntry::make('read_at')
                            ->label('Lida em')
                            ->dateTime('d/m/Y H:i:s')
                            ->placeholder('Não lida'),
                    ])
                    ->columns(2),
                    
                InfolistActions::make([
                    InfolistAction::make('mark_as_read')
                        ->label('Marcar como Lida')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn($record) => !$record->read_at)
                        ->action(function ($record) {
                            $record->markAsRead();
                            return redirect()->back();
                        }),
                        
                    InfolistAction::make('go_to_related')
                        ->label('Ir para Assunto')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('primary')
                        ->visible(fn($record) => !empty($record->data['url']))
                        ->url(fn($record) => $record->data['url'] ?? '#')
                        ->openUrlInNewTab(false)
                        ->action(function ($record) {
                            if (!$record->read_at) {
                                $record->markAsRead();
                            }
                        }),
                        
                    InfolistAction::make('back_to_list')
                        ->label('Voltar à Lista')
                        ->icon('heroicon-o-arrow-left')
                        ->color('gray')
                        ->url(fn() => static::getUrl('index')),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('data')
                    ->label('Título')
                    ->getStateUsing(fn($record) => $record->data['title'] ?? 'Notificação')
                    ->searchable()
                    ->weight('bold'),
                    
                TextColumn::make('data')
                    ->label('Mensagem')
                    ->getStateUsing(fn($record) => $record->data['message'] ?? '')
                    ->limit(50)
                    ->searchable(),
                    
                TextColumn::make('read_at')
                    ->label('Status')
                    ->getStateUsing(fn($record) => $record->read_at ? 'Lida' : 'Não lida')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Lida' => 'success',
                        'Não lida' => 'warning',
                        default => 'gray',
                    }),
                    
                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('read_status')
                    ->label('Status')
                    ->options([
                        'read' => 'Lidas',
                        'unread' => 'Não lidas',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // ✅ CORRIGIDO: Verificar se 'value' existe
                        if (!isset($data['value']) || empty($data['value'])) {
                            return $query;
                        }
                        
                        return $query->when(
                            $data['value'] === 'read',
                            fn (Builder $query): Builder => $query->whereNotNull('read_at'),
                        )->when(
                            $data['value'] === 'unread',
                            fn (Builder $query): Builder => $query->whereNull('read_at'),
                        );
                    }),
                    
                SelectFilter::make('notification_type')
                    ->label('Tipo')
                    ->options([
                        'new_pharmacy_registration' => 'Nova Farmácia',
                        'new_order' => 'Novo Pedido',
                        'order_status' => 'Status do Pedido',
                        'product_expired' => 'Produto Expirado',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // ✅ CORRIGIDO: Verificar se 'value' existe
                        if (!isset($data['value']) || empty($data['value'])) {
                            return $query;
                        }
                        
                        return $query->whereJsonContains('data->type', $data['value']);
                    }),
            ])
            ->actions([
                // ✅ ACTIONS AGRUPADAS EM DROPDOWN (três pontinhos no final de cada linha)
                Tables\Actions\ActionGroup::make([
                    Action::make('view_details')
                        ->label('Ver Detalhes')
                        ->icon('heroicon-o-eye')
                        ->color('primary')
                        ->url(fn($record) => static::getUrl('view', ['record' => $record]))
                        ->action(function ($record) {
                            if (!$record->read_at) {
                                $record->markAsRead();
                            }
                        }),
                        
                    Action::make('mark_as_read')
                        ->label('Marcar como Lida')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn($record) => !$record->read_at)
                        ->action(function ($record) {
                            $record->markAsRead();
                        }),
                        
                    Action::make('mark_as_unread')
                        ->label('Marcar como Não Lida')
                        ->icon('heroicon-o-x-mark')
                        ->color('warning')
                        ->visible(fn($record) => $record->read_at)
                        ->action(function ($record) {
                            $record->update(['read_at' => null]);
                        }),
                        
                    Action::make('go_to_related')
                        ->label('Ir para Assunto')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('primary')
                        ->visible(fn($record) => !empty($record->data['url']))
                        ->url(fn($record) => $record->data['url'] ?? '#')
                        ->openUrlInNewTab(false)
                        ->action(function ($record) {
                            if (!$record->read_at) {
                                $record->markAsRead();
                            }
                        }),
                        
                    Tables\Actions\DeleteAction::make()
                        ->label('Excluir')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Excluir Notificação')
                        ->modalDescription('Tem certeza que deseja excluir esta notificação?'),
                ])
                ->label('Ações')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // ✅ BULK ACTIONS (três pontinhos verticais quando seleciona múltiplos)
                    Tables\Actions\BulkAction::make('mark_selected_as_read')
                        ->label('Marcar como Lidas')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(fn($record) => $record->markAsRead());
                        })
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\BulkAction::make('mark_selected_as_unread')
                        ->label('Marcar como Não Lidas')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->action(function ($records) {
                            $records->each(fn($record) => $record->update(['read_at' => null]));
                        })
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\BulkAction::make('archive_selected')
                        ->label('Arquivar Selecionadas')
                        ->icon('heroicon-o-archive-box')
                        ->color('gray')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $data = $record->data;
                                $data['archived'] = true;
                                $data['archived_at'] = now()->toISOString();
                                $record->update(['data' => $data]);
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalHeading('Arquivar Notificações')
                        ->modalDescription('Tem certeza que deseja arquivar as notificações selecionadas?'),
                        
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir Selecionadas')
                        ->requiresConfirmation()
                        ->modalHeading('Excluir Notificações')
                        ->modalDescription('Tem certeza que deseja excluir permanentemente as notificações selecionadas?'),
                ]),
            ])
            ->headerActions([
                // ✅ HEADER ACTIONS (botões no topo da tabela)
                Action::make('mark_all_as_read')
                    ->label('Marcar Todas como Lidas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function () {
                        self::markAllUserNotificationsAsRead();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Marcar Todas como Lidas')
                    ->modalDescription('Tem certeza que deseja marcar todas as notificações como lidas?'),
                    
                Action::make('clear_all_read')
                    ->label('Limpar Todas as Lidas')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->action(function () {
                        self::clearAllReadNotifications();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Limpar Notificações Lidas')
                    ->modalDescription('Tem certeza que deseja excluir todas as notificações já lidas?'),
                    
                Action::make('refresh_notifications')
                    ->label('Atualizar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->action(function () {
                        // Força refresh da página
                        return redirect()->back();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('10s') // Auto-refresh a cada 10 segundos
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'view' => Pages\ViewNotification::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        if (!Auth::check()) {
            return null;
        }
        
        $count = self::getUnreadNotificationsCount();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (!Auth::check()) {
            return null;
        }
        
        $count = self::getUnreadNotificationsCount();
        return $count > 0 ? 'warning' : null;
    }

    private static function getUnreadNotificationsCount(): int
    {
        if (!Auth::check()) {
            return 0;
        }

        return DB::table('notifications')
            ->where('notifiable_id', Auth::id())
            ->where('notifiable_type', Auth::user()::class)
            ->whereNull('read_at')
            ->count();
    }

    private static function markAllUserNotificationsAsRead(): void
    {
        if (!Auth::check()) {
            return;
        }

        DB::table('notifications')
            ->where('notifiable_id', Auth::id())
            ->where('notifiable_type', Auth::user()::class)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private static function clearAllReadNotifications(): void
    {
        if (!Auth::check()) {
            return;
        }

        DB::table('notifications')
            ->where('notifiable_id', Auth::id())
            ->where('notifiable_type', Auth::user()::class)
            ->whereNotNull('read_at')
            ->delete();
    }
}
