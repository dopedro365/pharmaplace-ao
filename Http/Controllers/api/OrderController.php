<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderStatusChanged;
use App\Notifications\NewOrderCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'delivery_type' => 'required|in:pickup,delivery',
            'delivery_address' => 'required_if:delivery_type,delivery|string|max:500',
            'delivery_municipality' => 'required_if:delivery_type,delivery|string|max:100',
            'delivery_province' => 'required_if:delivery_type,delivery|string|max:100',
            'delivery_notes' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cash,transfer,card',
            'payment_proofs' => 'nullable|array',
            'payment_proofs.*' => 'file|mimes:pdf|max:5120', // 5MB cada
            'bank_account_id' => 'nullable|exists:pharmacy_bank_accounts,id',
            'cart_items' => 'required|array|min:1',
            'cart_items.*.product_id' => 'required|exists:products,id',
            'cart_items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na validação dos dados',
                'errors' => $validator->errors()
            ], 422);
        }

        // VERIFICAR SE USUÁRIO É FARMÁCIA (NÃO PODE COMPRAR)
        $user = auth()->user();
        if ($user && $user->role === 'pharmacy') {
            return response()->json([
                'success' => false,
                'message' => 'Usuários farmácias não podem fazer pedidos',
                'errors' => ['user' => ['Farmácias não podem fazer compras']]
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Validar itens do carrinho e calcular totais
            $cartItems = $request->cart_items;
            $subtotal = 0;
            $pharmacy_id = null;
            $validatedItems = [];

            foreach ($cartItems as $item) {
                $product = Product::with('pharmacy')->find($item['product_id']);
                
                if (!$product || !$product->is_active || !$product->is_available) {
                    throw new \Exception("Produto ID {$item['product_id']} não está disponível");
                }

                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Estoque insuficiente para o produto: {$product->name}");
                }

                // Verificar se todos os produtos são da mesma farmácia
                if ($pharmacy_id === null) {
                    $pharmacy_id = $product->pharmacy_id;
                } elseif ($pharmacy_id !== $product->pharmacy_id) {
                    throw new \Exception("Todos os produtos devem ser da mesma farmácia");
                }

                $itemTotal = $product->price * $item['quantity'];
                $subtotal += $itemTotal;

                $validatedItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $itemTotal
                ];
            }

            // Calcular taxa de entrega
            $deliveryFee = 0;
            if ($request->delivery_type === 'delivery') {
                $pharmacy = \App\Models\Pharmacy::find($pharmacy_id);
                $deliveryFee = $pharmacy->delivery_fee ?? 0;
            }

            $total = $subtotal + $deliveryFee;

            // PROCESSAR UPLOAD DOS COMPROVATIVOS (CORRIGIDO)
            $paymentProofPaths = [];
            if ($request->hasFile('payment_proofs')) {
                foreach ($request->file('payment_proofs') as $index => $file) {
                    try {
                        // Verificar se o arquivo é válido
                        if (!$file->isValid()) {
                            throw new \Exception("Arquivo de comprovativo #{$index} é inválido");
                        }

                        // Gerar nome único
                        $fileName = 'comprovativo_' . time() . '_' . $index . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                        
                        // USAR storeAs PARA CONTROLE TOTAL
                        $path = $file->storeAs('payment_proofs', $fileName, 'public');
                        
                        if (!$path) {
                            throw new \Exception("Falha ao salvar comprovativo #{$index}");
                        }

                        $paymentProofPaths[] = $path;

                    } catch (\Exception $e) {
                        throw new \Exception("Erro ao processar arquivo comprovativo #{$index}: " . $e->getMessage());
                    }
                }
            }

            // Criar pedido
            $order = Order::create([
                'user_id' => $user ? $user->id : null,
                'pharmacy_id' => $pharmacy_id,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'status' => $request->payment_method === 'cash' ? 'confirmed' : 'payment_verification',
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'delivery_type' => $request->delivery_type,
                'delivery_address' => $request->delivery_address,
                'delivery_municipality' => $request->delivery_municipality,
                'delivery_province' => $request->delivery_province,
                'delivery_notes' => $request->delivery_notes,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_proof' => !empty($paymentProofPaths) ? json_encode($paymentProofPaths) : null,
                'bank_account_id' => $request->bank_account_id,
            ]);

            // Criar itens do pedido E DESCONTAR ESTOQUE
            foreach ($validatedItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // DESCONTAR ESTOQUE IMEDIATAMENTE
                $item['product']->decrement('stock_quantity', $item['quantity']);
            }

            // Limpar carrinho se usuário logado
            if ($user) {
                CartItem::where('user_id', $user->id)->delete();
            }

            // ENVIAR NOTIFICAÇÕES
            $this->sendOrderNotifications($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pedido criado com sucesso',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'total' => $order->total
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Limpar arquivos de comprovativo se foram criados
            foreach ($paymentProofPaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar pedido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function sendOrderNotifications(Order $order)
    {
        try {
            // Notificar a farmácia sobre o novo pedido
            if ($order->pharmacy && $order->pharmacy->user) {
                $order->pharmacy->user->notify(new NewOrderCreated($order));
            }

            // Notificar admins e managers
            User::whereIn('role', ['admin', 'manager'])->each(function ($user) use ($order) {
                $user->notify(new NewOrderCreated($order));
            });

            // Se o pedido foi confirmado automaticamente (pagamento em dinheiro), notificar o cliente
            if ($order->status === 'confirmed' && $order->user) {
                $order->user->notify(new OrderStatusChanged($order, 'pending_payment', 'confirmed'));
            }

        } catch (\Exception $e) {
            // Log do erro mas não falhar o pedido
            \Log::error('Erro ao enviar notificações do pedido: ' . $e->getMessage());
        }
    }
}
