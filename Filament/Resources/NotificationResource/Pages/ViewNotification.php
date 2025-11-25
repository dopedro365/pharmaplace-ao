<?php

namespace App\Filament\Resources\NotificationResource\Pages;

use App\Filament\Resources\NotificationResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewNotification extends ViewRecord
{
    protected static string $resource = NotificationResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        // Marcar como lida automaticamente ao visualizar
        if (!$this->record->read_at) {
            $this->record->markAsRead();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('go_to_related')
                ->label('Ir para Assunto')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('primary')
                ->visible(fn() => !empty($this->record->data['url']))
                ->url(fn() => $this->record->data['url'] ?? '#')
                ->openUrlInNewTab(false),
                
            Actions\Action::make('back_to_list')
                ->label('Voltar à Lista')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn() => NotificationResource::getUrl('index')),
        ];
    }
}
