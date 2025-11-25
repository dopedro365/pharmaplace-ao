<?php

namespace App\Filament\Resources\NotificationResource\Pages;

use App\Filament\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListNotifications extends ListRecords
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('mark_all_as_read')
                ->label('Marcar todas como lidas')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $this->getUnreadNotificationsCount() > 0)
                ->action(function () {
                    $this->markAllUserNotificationsAsRead();
                    $this->redirect(request()->header('Referer'));
                }),
        ];
    }

    public function getTitle(): string
    {
        $unreadCount = $this->getUnreadNotificationsCount();
        return $unreadCount > 0 ? "Notificações ({$unreadCount} não lidas)" : 'Notificações';
    }

    /**
     * Método helper para contar notificações não lidas
     */
    private function getUnreadNotificationsCount(): int
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

    /**
     * Método helper para marcar todas as notificações como lidas
     */
    private function markAllUserNotificationsAsRead(): void
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
}
