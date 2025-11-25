<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    /**
     * Salvar subscription do usuário
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        $subscriptionData = $request->input('subscription');
        
        if (!$subscriptionData) {
            return response()->json(['error' => 'Dados de subscription inválidos'], 400);
        }

        try {
            // Salvar ou atualizar subscription usando o trait HasPushSubscriptions
            $user->updatePushSubscription(
                $subscriptionData['endpoint'],
                $subscriptionData['keys']['p256dh'] ?? null,
                $subscriptionData['keys']['auth'] ?? null
            );

            \Log::info("Push subscription salva para usuário {$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Subscription salva com sucesso'
            ]);

        } catch (\Exception $e) {
            \Log::error("Erro ao salvar push subscription: " . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Remover subscription do usuário
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        try {
            $subscriptionData = $request->input('subscription');
            
            if ($subscriptionData && isset($subscriptionData['endpoint'])) {
                $user->deletePushSubscription($subscriptionData['endpoint']);
            } else {
                // Remover todas as subscriptions se não especificar endpoint
                $user->deleteAllPushSubscriptions();
            }

            return response()->json([
                'success' => true,
                'message' => 'Subscription removida com sucesso'
            ]);

        } catch (\Exception $e) {
            \Log::error("Erro ao remover push subscription: " . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Retornar chave pública VAPID
     */
    public function vapidPublicKey()
    {
        return response()->json([
            'publicKey' => config('webpush.vapid.public_key')
        ]);
    }
}
