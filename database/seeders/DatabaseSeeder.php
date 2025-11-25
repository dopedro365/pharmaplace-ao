<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Desabilitar verificações de chave estrangeira temporariamente
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            PharmacySeeder::class,
            PharmacyBankAccountSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            ReceiptSeeder::class,
        ]);
        
        // Reabilitar verificações de chave estrangeira
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
