<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class NewOrderCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        // ✅ REMOVIDO WebPushChannel - apenas email e database
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Novo Pedido #{$this->order->order_number} Recebido")
            ->greeting("Olá, {$notifiable->name}!")
            ->line("Um novo pedido foi recebido na plataforma.");

        // Mensagem específica por tipo de usuário
        if ($notifiable->role === 'pharmacy') {
            $message->line("🏥 **Novo pedido para sua farmácia!**")
                    ->line("Um cliente fez um pedido em sua farmácia.");
        } else {
            $message->line("📦 **Novo pedido no sistema:**")
                    ->line("Um novo pedido foi registrado na plataforma.");
        }

        $message->line("**Detalhes do Pedido:**")
                ->line("• **Número:** #{$this->order->order_number}")
                ->line("• **Cliente:** {$this->order->customer_name}")
                ->line("• **Telefone:** {$this->order->customer_phone}")
                ->line("• **Total:** " . number_format($this->order->total, 2, ',', '.') . " Kz")
                ->line("• **Método de Pagamento:** " . ($this->order->payment_method === 'bank_transfer' ? 'Transferência Bancária' : 'Pagamento na Entrega'))
                ->line("• **Tipo de Entrega:** " . ($this->order->delivery_type === 'delivery' ? 'Entrega em Domicílio' : 'Retirada na Farmácia'))
                ->line("• **Data:** " . $this->order->created_at->format('d/m/Y H:i'));

        if ($this->order->payment_method === 'bank_transfer') {
            $message->line("⏳ **Status:** Aguardando verificação do pagamento");
        } else {
            $message->line("✅ **Status:** Confirmado");
        }

        $message->action('Ver Detalhes do Pedido', route('filament.painel.pages.order-detail-page', ['record' => $this->order->id]))
                ->line('Acesse o painel para gerenciar este pedido.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'customer_name' => $this->order->customer_name,
            'total' => $this->order->total,
            'title' => 'Novo Pedido Recebido',
            'message' => "Novo pedido #{$this->order->order_number} de {$this->order->customer_name}",
            'icon' => 'heroicon-o-shopping-bag',
            'color' => 'success',
            'url' => route('filament.painel.pages.order-detail-page', ['record' => $this->order->id]),
        ];
    }
}
