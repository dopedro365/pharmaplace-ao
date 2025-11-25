<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::where('role', 'customer')->where('status', 'approved')->take(6)->get();
        $activePharmacies = Pharmacy::where('is_active', true)->get();

        if ($customers->isEmpty() || $activePharmacies->isEmpty()) {
            $this->command->warn('Não há clientes ou farmácias ativas para criar pedidos.');
            return;
        }

        $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'];
        $paymentMethods = ['phasmapay', 'cash', 'bank_transfer', 'mobile_money'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        foreach ($customers as $customer) {
            // Criar 2 pedidos por cliente
            for ($i = 0; $i < 2; $i++) {
                $pharmacy = $activePharmacies->random();
                
                $subtotal = rand(600, 2500);
                $deliveryFee = $pharmacy->accepts_delivery ? $pharmacy->delivery_fee : 0;
                $total = $subtotal + $deliveryFee;
                
                $status = $statuses[array_rand($statuses)];
                $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
                
                // Ajustar status de pagamento baseado no status do pedido
                if ($status === 'delivered') {
                    $paymentStatus = 'paid';
                } elseif ($status === 'cancelled') {
                    $paymentStatus = rand(0, 1) ? 'failed' : 'refunded';
                }

                $createdAt = now()->subDays(rand(1, 45));

                // Criar pedido com campos básicos
                try {
                    Order::create([
                        'user_id' => $customer->id,
                        'pharmacy_id' => $pharmacy->id,
                        'total' => $total,
                        'subtotal' => $subtotal,
                        'delivery_fee' => $deliveryFee,
                        'status' => $status,
                        'payment_status' => $paymentStatus,
                        'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                        'delivery_address' => $this->generateRandomAddress(),
                        'delivery_phone' => '+244 9' . rand(10000000, 99999999),
                        'notes' => rand(0, 2) ? $this->getRandomNote() : null,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                } catch (\Exception $e) {
                    // Fallback com campos mínimos
                    try {
                        Order::create([
                            'user_id' => $customer->id,
                            'pharmacy_id' => $pharmacy->id,
                            'total' => $total,
                            'status' => $status,
                            'payment_status' => $paymentStatus,
                        ]);
                    } catch (\Exception $e2) {
                        $this->command->warn("Erro ao criar pedido: " . $e2->getMessage());
                        continue;
                    }
                }
            }
        }
    }

    private function generateRandomAddress(): string
    {
        $streets = [
            'Rua da Independência',
            'Avenida Principal', 
            'Rua do Comércio',
            'Avenida da Liberdade',
            'Rua Central',
            'Avenida dos Combatentes',
        ];

        $neighborhoods = [
            'Centro',
            'Bairro Novo',
            'Vila Nova',
            'Zona Industrial',
        ];

        $street = $streets[array_rand($streets)];
        $number = rand(1, 999);
        $neighborhood = $neighborhoods[array_rand($neighborhoods)];

        return "{$street}, {$number}, {$neighborhood}, Benguela";
    }

    private function getRandomNote(): string
    {
        $notes = [
            'Entregar no período da manhã',
            'Ligar antes de entregar',
            'Deixar com o porteiro',
            'Entregar após as 18h',
            'Produto urgente',
        ];

        return $notes[array_rand($notes)];
    }
}
