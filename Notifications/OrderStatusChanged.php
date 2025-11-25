<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected Order $order;
    protected string $oldStatus;
    protected string $newStatus;

    public function __construct(Order $order, string $oldStatus, string $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        
        // ✅ GARANTIR QUE NÃO SEJA DUPLICADO
        $this->onQueue('notifications');
        $this->delay(now()->addSeconds(2)); // Pequeno delay para evitar duplicatas
    }

    public function via($notifiable): array
    {
        // ✅ APENAS EMAIL E DATABASE - REMOVIDO WebPush
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $statusMessages = [
            'payment_verification' => 'Aguardando verificação do pagamento',
            'confirmed' => 'Confirmado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
            'returned' => 'Devolvido',
        ];

        $oldStatusLabel = $statusMessages[$this->oldStatus] ?? $this->oldStatus;
        $newStatusLabel = $statusMessages[$this->newStatus] ?? $this->newStatus;
        
        // ✅ INFORMAÇÕES MELHORADAS COM FARMÁCIA E DATA
        $pharmacyName = $this->order->pharmacy->name ?? 'Farmácia';
        $orderDate = $this->order->created_at->format('d/m/Y \à\s H:i');

        $message = (new MailMessage)
            ->subject("Atualização do seu pedido - {$pharmacyName}")
            ->greeting("Olá, {$notifiable->name}!")
            ->line("Temos uma atualização sobre seu pedido da **{$pharmacyName}**.")
            ->line("**Data da compra:** {$orderDate}")
            ->line("**Status anterior:** {$oldStatusLabel}")
            ->line("**Novo status:** {$newStatusLabel}");

        switch ($this->newStatus) {
            case 'confirmed':
                $message->line('✅ Seu pagamento foi confirmado! Seu pedido está sendo preparado pela farmácia.');
                break;
            case 'delivered':
                $message->line('🎉 Seu pedido foi entregue com sucesso! Obrigado por escolher a ' . $pharmacyName . '.');
                break;
            case 'cancelled':
                $message->line('❌ Seu pedido foi cancelado pela farmácia.');
                if ($this->order->cancellation_reason) {
                    $message->line("**Motivo:** {$this->order->cancellation_reason}");
                }
                $message->line('O estoque foi restaurado automaticamente.');
                break;
            case 'returned':
                $message->line('🔄 Sua devolução foi processada pela farmácia.');
                if ($this->order->cancellation_reason) {
                    $message->line("**Motivo:** {$this->order->cancellation_reason}");
                }
                break;
        }

        $message->line("**Total do pedido:** " . number_format($this->order->total, 2, ',', '.') . " Kz")
            ->line("**Farmácia:** {$pharmacyName}")
            ->action('Ver Detalhes do Pedido', url('/painel/pages/order-detail-page?record=' . $this->order->id))
            ->line('Obrigado por usar nossa plataforma!');

        return $message;
    }

    public function toArray($notifiable): array
    {
        $statusMessages = [
            'payment_verification' => 'Aguardando verificação do pagamento',
            'confirmed' => 'Confirmado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
            'returned' => 'Devolvido',
        ];

        $pharmacyName = $this->order->pharmacy->name ?? 'Farmácia';
        $orderDate = $this->order->created_at->format('d/m/Y');
        $newStatusLabel = $statusMessages[$this->newStatus] ?? $this->newStatus;

        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'pharmacy_name' => $pharmacyName,
            'order_date' => $orderDate,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'old_status_label' => $statusMessages[$this->oldStatus] ?? $this->oldStatus,
            'new_status_label' => $newStatusLabel,
            'total' => $this->order->total,
            // ✅ MENSAGEM MELHORADA COM FARMÁCIA E DATA
            'message' => "Seu pedido da {$pharmacyName} de {$orderDate} foi {$newStatusLabel}.",
        ];
    }

    /**
     * ✅ MÉTODO PARA SMS COM FORMATAÇÃO MELHORADA
     */
    public function toSms($notifiable): string
    {
        $statusMessages = [
            'payment_verification' => 'aguardando verificação do pagamento',
            'confirmed' => 'confirmado',
            'delivered' => 'entregue',
            'cancelled' => 'cancelado',
            'returned' => 'devolvido',
        ];

        $pharmacyName = $this->order->pharmacy->name ?? 'Farmácia';
        $orderDate = $this->order->created_at->format('d/m/Y');
        $newStatusLabel = $statusMessages[$this->newStatus] ?? $this->newStatus;

        return "Seu pedido da {$pharmacyName} de {$orderDate} foi {$newStatusLabel}. Obrigado por escolher nossa plataforma!";
    }

    /**
     * ✅ IDENTIFICADOR ÚNICO PARA EVITAR DUPLICATAS
     */
    public function uniqueId(): string
    {
        return "order_status_changed_{$this->order->id}_{$this->oldStatus}_{$this->newStatus}";
    }
}
