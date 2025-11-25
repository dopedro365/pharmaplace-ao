<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Product;

class ProductExpired extends Notification implements ShouldQueue
{
    use Queueable;

    protected Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function via($notifiable): array
    {
        // ✅ REMOVIDO WebPushChannel - apenas email e database
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Produto Expirado - {$this->product->name}")
            ->greeting("Olá, {$notifiable->name}!")
            ->line("Um produto foi automaticamente desabilitado por estar expirado.");

        // Mensagem específica por tipo de usuário
        if ($notifiable->role === 'pharmacy') {
            $message->line("🏥 **Produto da sua farmácia expirou:**");
        } else {
            $message->line("⚠️ **Produto expirado no sistema:**");
        }

        $message->line("**Detalhes do Produto:**")
                ->line("• **Nome:** {$this->product->name}")
                ->line("• **Fabricante:** {$this->product->manufacturer}")
                ->line("• **Data de Validade:** " . ($this->product->expiry_date ? $this->product->expiry_date->format('d/m/Y') : 'Não informada'))
                ->line("• **Estoque:** {$this->product->stock_quantity} unidades")
                ->line("• **Farmácia:** " . ($this->product->pharmacy->name ?? 'N/A'));

        if ($notifiable->role === 'pharmacy') {
            $message->line("🔄 **Ação Recomendada:**")
                    ->line("• Remova o produto do estoque físico")
                    ->line("• Atualize a data de validade se necessário")
                    ->line("• Adicione novo lote se disponível");
        }

        $message->line("⚠️ **Status:** O produto foi automaticamente desabilitado para vendas.")
                ->action('Ver Produto', url('/painel/resources/products/' . $this->product->id))
                ->line('Acesse o painel para gerenciar este produto.');

        return $message;
    }

    public function toArray($notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'pharmacy_name' => $this->product->pharmacy->name ?? 'N/A',
            'expiry_date' => $this->product->expiry_date?->format('d/m/Y'),
            'stock_quantity' => $this->product->stock_quantity,
            'title' => 'Produto Expirado',
            'message' => "Produto '{$this->product->name}' foi desabilitado por expiração",
            'icon' => 'heroicon-o-exclamation-triangle',
            'color' => 'warning',
            'url' => url('/painel/resources/products/' . $this->product->id),
        ];
    }
}
