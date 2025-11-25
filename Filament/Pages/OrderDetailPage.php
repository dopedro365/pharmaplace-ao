<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Notifications\OrderStatusChanged;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use App\Traits\SendsKassalaSMS;

class OrderDetailPage extends Page
{
    use SendsKassalaSMS;

    protected static string $view = 'filament.pages.order-detail';
    protected static ?string $title = 'Detalhes do Pedido';
    protected static bool $shouldRegisterNavigation = false;

    public Order $record;

    public function mount(): void
    {
        $recordId = request()->get('record');
        
        if (!$recordId) {
            abort(404);
        }

        $this->record = Order::with(['user', 'pharmacy', 'items.product', 'bankAccount'])
            ->findOrFail($recordId);

        // Verificar permissões
        $user = Auth::user();
        
        if ($user->role === 'customer' && $this->record->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para ver este pedido.');
        }
        
        if ($user->role === 'pharmacy' && $this->record->pharmacy_id !== $user->pharmacy->id) {
            abort(403, 'Você não tem permissão para ver este pedido.');
        }
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Ações de pagamento apenas para roles autorizados
        $user = Auth::user();
        if (in_array($user->role, ['admin', 'manager', 'pharmacy']) && $this->record->status === 'payment_verification') {
            $actions[] = Action::make('confirm_payment')
                ->label('Confirmar Pagamento')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('confirmPayment');

            $actions[] = Action::make('reject_payment')
                ->label('Rejeitar Pagamento')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->action('rejectPayment');
        }

        return $actions;
    }

    /**
     * Enviar notificações quando status do pedido mudar
     */
    private function sendOrderStatusNotifications(Order $order, string $oldStatus, string $newStatus): void
    {
        \Log::info("Enviando notificações para mudança de status: {$oldStatus} -> {$newStatus}");

        try {
            // NOTIFICAR O CLIENTE
            if ($order->user) {
                $order->user->notify(new OrderStatusChanged($order, $oldStatus, $newStatus));
                \Log::info("Notificação enviada para cliente: {$order->user->email}");

                // Enviar SMS
                if ($order->customer_phone) {
                    $smsMessage = "O status do seu pedido #{$order->order_number} foi atualizado para: {$order->status_label}.";
                    $this->sendKassalaSMSAsync($order->customer_phone, $smsMessage, $order->id);
                    \Log::info("SMS adicionado à fila para cliente: {$order->customer_phone}");
                }
            }

            // NOTIFICAR A FARMÁCIA (se não foi ela que fez a mudança)
            $currentUser = Auth::user();
            if ($order->pharmacy && $order->pharmacy->user && $currentUser->id !== $order->pharmacy->user->id) {
                $order->pharmacy->user->notify(new OrderStatusChanged($order, $oldStatus, $newStatus));
                \Log::info("Notificação enviada para farmácia: {$order->pharmacy->user->email}");
            }

            // NOTIFICAR ADMINS E MANAGERS
            User::whereIn('role', ['admin', 'manager'])->each(function ($user) use ($order, $oldStatus, $newStatus) {
                if ($user->id !== Auth::id()) { // Não notificar quem fez a ação
                    $user->notify(new OrderStatusChanged($order, $oldStatus, $newStatus));
                }
            });

            \Log::info("Todas as notificações foram enviadas com sucesso");

        } catch (\Exception $e) {
            \Log::error("Erro ao enviar notificações: " . $e->getMessage());
        }
    }

    public function confirmPayment(): void
    {
        $user = Auth::user();
        
        // Verificar se o usuário tem permissão
        if (!in_array($user->role, ['admin', 'manager', 'pharmacy'])) {
            Notification::make()
                ->title('Você não tem permissão para confirmar pagamentos.')
                ->danger()
                ->send();
            return;
        }

        // Verificar se é farmácia e se o pedido pertence a ela
        if ($user->role === 'pharmacy' && $this->record->pharmacy_id !== $user->pharmacy->id) {
            Notification::make()
                ->title('Você só pode confirmar pagamentos de pedidos da sua farmácia.')
                ->danger()
                ->send();
            return;
        }

        $oldStatus = $this->record->status;

        $this->record->update([
            'status' => 'confirmed',
            'payment_verified_at' => now()
        ]);

        // ENVIAR NOTIFICAÇÕES
        $this->sendOrderStatusNotifications($this->record, $oldStatus, 'confirmed');

        Notification::make()
            ->title('Pagamento confirmado com sucesso!')
            ->success()
            ->send();

        // Recarregar a página para atualizar os dados
        $this->redirect(request()->header('Referer'));
    }

    public function rejectPayment(): void
    {
        $user = Auth::user();
        
        // Verificar se o usuário tem permissão
        if (!in_array($user->role, ['admin', 'manager', 'pharmacy'])) {
            Notification::make()
                ->title('Você não tem permissão para rejeitar pagamentos.')
                ->danger()
                ->send();
            return;
        }

        // Verificar si é farmácia e se o pedido pertence a ela
        if ($user->role === 'pharmacy' && $this->record->pharmacy_id !== $user->pharmacy->id) {
            Notification::make()
                ->title('Você só pode rejeitar pagamentos de pedidos da sua farmácia.')
                ->danger()
                ->send();
            return;
        }

        $oldStatus = $this->record->status;

        $this->record->update(['status' => 'cancelled']);

        // RESTAURAR ESTOQUE quando cancelado
        $this->record->restoreStock();

        // ENVIAR NOTIFICAÇÕES
        $this->sendOrderStatusNotifications($this->record, $oldStatus, 'cancelled');

        Notification::make()
            ->title('Pagamento rejeitado e pedido cancelado!')
            ->body('O estoque foi restaurado automaticamente.')
            ->success()
            ->send();

        // Recarregar a página para atualizar os dados
        $this->redirect(request()->header('Referer'));
    }

    public function getTitle(): string
    {
        return "Detalhes do Pedido";
    }
}
