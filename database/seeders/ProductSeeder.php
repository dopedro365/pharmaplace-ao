<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Pharmacy;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activePharmacies = Pharmacy::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();

        if ($activePharmacies->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Não há farmácias ativas ou categorias disponíveis para criar produtos.');
            return;
        }

        $baseProducts = [
            [
                'name' => 'Paracetamol 500mg',
                'description' => 'Analgésico e antipirético para alívio da dor e febre',
                'price' => 250.00,
                'stock' => 100,
                'category_name' => 'Analgésicos e Antipiréticos',
            ],
            [
                'name' => 'Aspirina 500mg',
                'description' => 'Ácido acetilsalicílico para dor e inflamação',
                'price' => 180.00,
                'stock' => 80,
                'category_name' => 'Analgésicos e Antipiréticos',
            ],
            [
                'name' => 'Ibuprofeno 400mg',
                'description' => 'Anti-inflamatório não esteroidal',
                'price' => 450.00,
                'stock' => 80,
                'category_name' => 'Anti-inflamatórios',
            ],
            [
                'name' => 'Vitamina C 1000mg',
                'description' => 'Suplemento de vitamina C',
                'price' => 380.00,
                'stock' => 90,
                'category_name' => 'Vitaminas',
            ],
            [
                'name' => 'Complexo B',
                'description' => 'Suplemento vitamínico do complexo B',
                'price' => 650.00,
                'stock' => 120,
                'category_name' => 'Vitaminas',
            ],
        ];

        // Criar produtos para cada farmácia ativa
        foreach ($activePharmacies as $pharmacy) {
            foreach ($baseProducts as $productData) {
                $category = $categories->where('name', $productData['category_name'])->first();
                
                if ($category) {
                    $priceVariation = rand(-10, 15) / 100;
                    $stockVariation = rand(-20, 30);
                    
                    $slug = Str::slug($productData['name'] . '-' . $pharmacy->id . '-' . uniqid());
                    
                    try {
                        Product::updateOrCreate(
                            [
                                'pharmacy_id' => $pharmacy->id,
                                'slug' => $slug,
                            ],
                            [
                                'category_id' => $category->id,
                                'name' => $productData['name'],
                                'description' => $productData['description'],
                                'price' => round($productData['price'] * (1 + $priceVariation), 2),
                                'stock_quantity' => max(0, $productData['stock'] + $stockVariation),
                                'is_active' => true,
                                'is_available' => true,
                                'composition' => fake()->words(3, true),
                                'manufacturer' => fake()->company(),
                                'batch_number' => 'LOT' . date('Y') . rand(100000, 999999),
                                'barcode' => '789' . rand(1000000000, 9999999999),
                                'requires_prescription' => false,
                                'is_controlled' => false,
                            ]
                        );
                    } catch (\Exception $e) {
                        // Criar com campos mínimos se falhar
                        Product::updateOrCreate(
                            [
                                'pharmacy_id' => $pharmacy->id,
                                'slug' => $slug,
                            ],
                            [
                                'category_id' => $category->id,
                                'name' => $productData['name'],
                                'description' => $productData['description'],
                                'price' => $productData['price'],
                                'stock_quantity' => $productData['stock'],
                                'is_active' => true,
                            ]
                        );
                    }
                }
            }
        }
    }
}
