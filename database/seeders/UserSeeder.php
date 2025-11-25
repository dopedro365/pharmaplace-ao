<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'admin@rammplus.com'],
            [
                'name' => 'Super Administrador',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'approved',
                'email_verified_at' => now(),
            ]
        );

        // Manager
        User::updateOrCreate(
            ['email' => 'manager@rammplus.com'],
            [
                'name' => 'Gestor Principal',
                'password' => Hash::make('password123'),
                'role' => 'manager',
                'status' => 'approved',
                'email_verified_at' => now(),
            ]
        );

        // Usuários de Farmácias
        $pharmacyUsers = [
            [
                'name' => 'Farmácia Central Benguela',
                'email' => 'central@farmacia.com',
                'role' => 'pharmacy',
                'status' => 'approved',
            ],
            [
                'name' => 'Farmácia Popular 24H',
                'email' => 'popular@farmacia.com',
                'role' => 'pharmacy',
                'status' => 'approved',
            ],
            [
                'name' => 'Farmácia Saúde & Vida',
                'email' => 'saude@farmacia.com',
                'role' => 'pharmacy',
                'status' => 'approved',
            ],
            [
                'name' => 'Farmácia Nova Esperança',
                'email' => 'esperanca@farmacia.com',
                'role' => 'pharmacy',
                'status' => 'approved',
            ],
            [
                'name' => 'Farmácia Bem-Estar',
                'email' => 'bemestar@farmacia.com',
                'role' => 'pharmacy',
                'status' => 'pending',
            ],
        ];

        foreach ($pharmacyUsers as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ])
            );
        }

        // Clientes
        $customers = [
            [
                'name' => 'João Silva Santos',
                'email' => 'joao.silva@email.com',
            ],
            [
                'name' => 'Maria Fernanda Costa',
                'email' => 'maria.costa@email.com',
            ],
            [
                'name' => 'Pedro Manuel Oliveira',
                'email' => 'pedro.oliveira@email.com',
            ],
            [
                'name' => 'Ana Beatriz Ferreira',
                'email' => 'ana.ferreira@email.com',
            ],
            [
                'name' => 'Carlos Eduardo Mendes',
                'email' => 'carlos.mendes@email.com',
            ],
            [
                'name' => 'Luisa Maria Rodrigues',
                'email' => 'luisa.rodrigues@email.com',
            ],
            [
                'name' => 'António José Pereira',
                'email' => 'antonio.pereira@email.com',
            ],
            [
                'name' => 'Isabel Cristina Alves',
                'email' => 'isabel.alves@email.com',
            ],
            [
                'name' => 'Miguel Ângelo Sousa',
                'email' => 'miguel.sousa@email.com',
            ],
            [
                'name' => 'Catarina Sofia Lopes',
                'email' => 'catarina.lopes@email.com',
            ],
        ];

        foreach ($customers as $customerData) {
            User::updateOrCreate(
                ['email' => $customerData['email']],
                array_merge($customerData, [
                    'password' => Hash::make('password123'),
                    'role' => 'customer',
                    'status' => 'approved',
                    'email_verified_at' => now(),
                ])
            );
        }

        // Criar clientes adicionais apenas se não existirem
        $existingCustomersCount = User::where('role', 'customer')->count();
        $targetCustomersCount = 35; // 10 fixos + 25 adicionais
        
        if ($existingCustomersCount < $targetCustomersCount) {
            $customersToCreate = $targetCustomersCount - $existingCustomersCount;
            
            for ($i = 1; $i <= $customersToCreate; $i++) {
                $email = 'customer' . ($existingCustomersCount + $i) . '@email.com';
                
                User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => fake()->name(),
                        'password' => Hash::make('password123'),
                        'role' => 'customer',
                        'status' => 'approved',
                        'email_verified_at' => now(),
                    ]
                );
            }
        }
    }
}
