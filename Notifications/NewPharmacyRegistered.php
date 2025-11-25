<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPharmacyRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $pharmacy;

    public function __construct(User $user, Pharmacy $pharmacy)
    {
        $this->user = $user;
        $this->pharmacy = $pharmacy;
    }

    public function via($notifiable)
    {
        // ✅ REMOVIDO WebPushChannel - apenas email e database
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nova Farmácia Registrada - ' . $this->pharmacy->name)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('🏥 **Uma nova farmácia foi registrada na plataforma e precisa de aprovação.**')
            ->line('')
            ->line('**Detalhes da Farmácia:**')
            ->line('• **Nome:** ' . $this->pharmacy->name)
            ->line('• **Licença:** ' . $this->pharmacy->license_number)
            ->line('• **Responsável:** ' . $this->user->name)
            ->line('• **Email:** ' . ($this->user->email ?? 'Não informado'))
            ->line('• **Telefone:** ' . ($this->user->phone ?? 'Não informado'))
            ->line('• **Localização:** ' . $this->pharmacy->municipality . ', ' . $this->pharmacy->province)
            ->line('• **Endereço:** ' . $this->pharmacy->address)
            ->line('• **Data de Registro:** ' . $this->pharmacy->created_at->format('d/m/Y H:i'))
            ->line('')
            ->line('⏳ **Status:** Aguardando aprovação')
            ->line('🔍 **Ação Necessária:** Revisar documentos e aprovar/rejeitar o registro')
            ->action('Revisar Farmácia', url('/painel/resources/pharmacies/' . $this->pharmacy->id))
            ->line('Por favor, revise os documentos e aprove ou rejeite o registro o mais breve possível.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'new_pharmacy_registration',
            'user_id' => $this->user->id,
            'pharmacy_id' => $this->pharmacy->id,
            'pharmacy_name' => $this->pharmacy->name,
            'user_name' => $this->user->name,
            'title' => 'Nova Farmácia Registrada',
            'message' => 'Nova farmácia "' . $this->pharmacy->name . '" registrada por ' . $this->user->name,
            'icon' => 'heroicon-o-building-storefront',
            'color' => 'info',
            'url' => url('/painel/resources/pharmacies/' . $this->pharmacy->id),
        ];
    }
}
