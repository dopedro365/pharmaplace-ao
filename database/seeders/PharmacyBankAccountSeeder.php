<?php

namespace Database\Seeders;

use App\Models\Pharmacy;
use App\Models\PharmacyBankAccount;
use Illuminate\Database\Seeder;

class PharmacyBankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pharmacies = Pharmacy::with('user')->get();

        if ($pharmacies->isEmpty()) {
            $this->command->warn('Não há farmácias para criar contas bancárias.');
            return;
        }

        $angolaBanks = [
            [
                'bank_name' => 'Banco Angolano de Investimentos (BAI)',
                'swift_code' => 'BAIAAOAO',
                'bank_code' => '0001',
            ],
            [
                'bank_name' => 'Banco de Fomento Angola (BFA)',
                'swift_code' => 'BFAOAOAO',
                'bank_code' => '0002',
            ],
            [
                'bank_name' => 'Banco Millennium Atlântico',
                'swift_code' => 'BMAOAOAO',
                'bank_code' => '0003',
            ],
            [
                'bank_name' => 'Banco Económico',
                'swift_code' => 'BECOAOAO',
                'bank_code' => '0004',
            ],
            [
                'bank_name' => 'Banco Sol',
                'swift_code' => 'BSOLAOAO',
                'bank_code' => '0005',
            ],
        ];

        foreach ($pharmacies as $index => $pharmacy) {
            if ($pharmacy->is_active) {
                $bankData = $angolaBanks[$index % count($angolaBanks)];
                
                // Conta principal
                PharmacyBankAccount::updateOrCreate(
                    [
                        'pharmacy_id' => $pharmacy->id,
                        'is_primary' => true,
                    ],
                    [
                        'bank_name' => $bankData['bank_name'],
                        'account_holder' => $pharmacy->name . ' Lda',
                        'account_number' => $bankData['bank_code'] . str_pad($pharmacy->id * 1000 + 100, 9, '0', STR_PAD_LEFT),
                        'iban' => 'AO06' . $bankData['bank_code'] . str_pad($pharmacy->id * 1000000 + 100000, 17, '0', STR_PAD_LEFT),
                        'swift_code' => $bankData['swift_code'],
                        'is_active' => true,
                    ]
                );

                // Conta secundária para farmácias principais
                if ($index < 3) {
                    $secondaryBank = $angolaBanks[($index + 1) % count($angolaBanks)];
                    
                    PharmacyBankAccount::updateOrCreate(
                        [
                            'pharmacy_id' => $pharmacy->id,
                            'is_primary' => false,
                            'bank_name' => $secondaryBank['bank_name'],
                        ],
                        [
                            'account_holder' => $pharmacy->name . ' Lda',
                            'account_number' => $secondaryBank['bank_code'] . str_pad($pharmacy->id * 2000 + 200, 9, '0', STR_PAD_LEFT),
                            'iban' => 'AO06' . $secondaryBank['bank_code'] . str_pad($pharmacy->id * 2000000 + 200000, 17, '0', STR_PAD_LEFT),
                            'swift_code' => $secondaryBank['swift_code'],
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
