<?php

namespace Database\Seeders;

use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Database\Seeder;

class PharmacySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pharmacyUsers = User::where('role', 'pharmacy')->get();

        $pharmaciesData = [
            [
                'user_email' => 'central@farmacia.com',
                'name' => 'Farmácia Central Benguela',
                'license_number' => 'FCB-2024-001',
                'description' => 'Farmácia com mais de 25 anos de experiência no centro de Benguela.',
                'address' => 'Rua Direita do Comércio, 145, Centro, Benguela',
                'municipality' => 'Benguela',
                'province' => 'Benguela',
                'latitude' => -12.5763,
                'longitude' => 13.4055,
                'phone' => '+244 923 456 789',
                'email' => 'central@farmacia.com',
                'whatsapp' => '+244 923 456 789',
                'opening_hours' => [
                    'monday' => '08:00-18:00',
                    'tuesday' => '08:00-18:00',
                    'wednesday' => '08:00-18:00',
                    'thursday' => '08:00-18:00',
                    'friday' => '08:00-18:00',
                    'saturday' => '08:00-14:00',
                    'sunday' => 'Fechado'
                ],
                'logo' => null,
                'images' => [],
                'is_verified' => true,
                'is_active' => true,
                'accepts_delivery' => true,
                'delivery_fee' => 300.00,
                'delivery_time_minutes' => 45,
                'minimum_order' => 500.00,
            ],
            [
                'user_email' => 'popular@farmacia.com',
                'name' => 'Farmácia Popular 24H',
                'license_number' => 'FP24-2024-002',
                'description' => 'Farmácia popular com atendimento 24 horas.',
                'address' => 'Avenida da Independência, 678, Benguela',
                'municipality' => 'Benguela',
                'province' => 'Benguela',
                'latitude' => -12.5800,
                'longitude' => 13.4100,
                'phone' => '+244 923 456 790',
                'email' => 'popular@farmacia.com',
                'whatsapp' => '+244 923 456 790',
                'opening_hours' => [
                    'monday' => '24h',
                    'tuesday' => '24h',
                    'wednesday' => '24h',
                    'thursday' => '24h',
                    'friday' => '24h',
                    'saturday' => '24h',
                    'sunday' => '24h'
                ],
                'logo' => null,
                'images' => [],
                'is_verified' => true,
                'is_active' => true,
                'accepts_delivery' => true,
                'delivery_fee' => 250.00,
                'delivery_time_minutes' => 30,
                'minimum_order' => 300.00,
            ],
            [
                'user_email' => 'saude@farmacia.com',
                'name' => 'Farmácia Saúde & Vida',
                'license_number' => 'FSV-2024-003',
                'description' => 'Especializada em medicamentos especiais e produtos naturais.',
                'address' => 'Rua Kwame Nkrumah, 234, Bairro Novo, Benguela',
                'municipality' => 'Benguela',
                'province' => 'Benguela',
                'latitude' => -12.5850,
                'longitude' => 13.4150,
                'phone' => '+244 923 456 791',
                'email' => 'saude@farmacia.com',
                'whatsapp' => '+244 923 456 791',
                'opening_hours' => [
                    'monday' => '07:30-19:00',
                    'tuesday' => '07:30-19:00',
                    'wednesday' => '07:30-19:00',
                    'thursday' => '07:30-19:00',
                    'friday' => '07:30-19:00',
                    'saturday' => '08:00-16:00',
                    'sunday' => '09:00-13:00'
                ],
                'logo' => null,
                'images' => [],
                'is_verified' => true,
                'is_active' => true,
                'accepts_delivery' => true,
                'delivery_fee' => 400.00,
                'delivery_time_minutes' => 60,
                'minimum_order' => 800.00,
            ],
        ];

        foreach ($pharmaciesData as $pharmacyData) {
            $user = $pharmacyUsers->where('email', $pharmacyData['user_email'])->first();
            
            if ($user) {
                Pharmacy::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $pharmacyData['name'],
                        'license_number' => $pharmacyData['license_number'],
                        'description' => $pharmacyData['description'],
                        'address' => $pharmacyData['address'],
                        'municipality' => $pharmacyData['municipality'],
                        'province' => $pharmacyData['province'],
                        'latitude' => $pharmacyData['latitude'],
                        'longitude' => $pharmacyData['longitude'],
                        'phone' => $pharmacyData['phone'],
                        'email' => $pharmacyData['email'],
                        'whatsapp' => $pharmacyData['whatsapp'],
                        'opening_hours' => $pharmacyData['opening_hours'],
                        'logo' => $pharmacyData['logo'],
                        'images' => $pharmacyData['images'],
                        'is_verified' => $pharmacyData['is_verified'],
                        'is_active' => $pharmacyData['is_active'],
                        'accepts_delivery' => $pharmacyData['accepts_delivery'],
                        'delivery_fee' => $pharmacyData['delivery_fee'],
                        'delivery_time_minutes' => $pharmacyData['delivery_time_minutes'],
                        'minimum_order' => $pharmacyData['minimum_order'],
                    ]
                );
            }
        }
    }
}
