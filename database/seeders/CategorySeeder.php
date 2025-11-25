<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Medicamentos por Sistema
            [
                'name' => 'Analgésicos e Antipiréticos',
                'description' => 'Medicamentos para alívio da dor e redução da febre',
                'icon' => 'pill',
                'sort_order' => 1,
            ],
            [
                'name' => 'Anti-inflamatórios',
                'description' => 'Medicamentos para reduzir inflamação e dor',
                'icon' => 'heart-pulse',
                'sort_order' => 2,
            ],
            [
                'name' => 'Antibióticos',
                'description' => 'Medicamentos para combater infecções bacterianas',
                'icon' => 'shield-check',
                'sort_order' => 3,
            ],
            [
                'name' => 'Antivirais',
                'description' => 'Medicamentos para tratamento de infecções virais',
                'icon' => 'virus',
                'sort_order' => 4,
            ],
            [
                'name' => 'Antifúngicos',
                'description' => 'Medicamentos para tratamento de infecções fúngicas',
                'icon' => 'bacteria',
                'sort_order' => 5,
            ],
            [
                'name' => 'Antiparasitários',
                'description' => 'Medicamentos contra parasitas e vermes',
                'icon' => 'bug',
                'sort_order' => 6,
            ],
            
            // Sistema Cardiovascular
            [
                'name' => 'Anti-hipertensivos',
                'description' => 'Medicamentos para controle da pressão arterial',
                'icon' => 'heart',
                'sort_order' => 7,
            ],
            [
                'name' => 'Cardiológicos',
                'description' => 'Medicamentos para o coração e sistema circulatório',
                'icon' => 'heart-pulse',
                'sort_order' => 8,
            ],
            [
                'name' => 'Anticoagulantes',
                'description' => 'Medicamentos para prevenir coagulação sanguínea',
                'icon' => 'droplet',
                'sort_order' => 9,
            ],
            [
                'name' => 'Hipolipemiantes',
                'description' => 'Medicamentos para reduzir colesterol e triglicéridos',
                'icon' => 'trending-down',
                'sort_order' => 10,
            ],
            
            // Sistema Respiratório
            [
                'name' => 'Broncodilatadores',
                'description' => 'Medicamentos para asma e problemas respiratórios',
                'icon' => 'wind',
                'sort_order' => 11,
            ],
            [
                'name' => 'Antitussígenos',
                'description' => 'Medicamentos para tosse',
                'icon' => 'lungs',
                'sort_order' => 12,
            ],
            [
                'name' => 'Expectorantes',
                'description' => 'Medicamentos para facilitar a expectoração',
                'icon' => 'droplets',
                'sort_order' => 13,
            ],
            [
                'name' => 'Descongestionantes',
                'description' => 'Medicamentos para congestão nasal',
                'icon' => 'nose',
                'sort_order' => 14,
            ],
            
            // Sistema Digestivo
            [
                'name' => 'Antiácidos',
                'description' => 'Medicamentos para acidez estomacal',
                'icon' => 'stomach',
                'sort_order' => 15,
            ],
            [
                'name' => 'Antieméticos',
                'description' => 'Medicamentos contra náuseas e vômitos',
                'icon' => 'ban',
                'sort_order' => 16,
            ],
            [
                'name' => 'Antidiarreicos',
                'description' => 'Medicamentos para tratamento da diarreia',
                'icon' => 'shield',
                'sort_order' => 17,
            ],
            [
                'name' => 'Laxantes',
                'description' => 'Medicamentos para constipação',
                'icon' => 'arrow-down',
                'sort_order' => 18,
            ],
            [
                'name' => 'Hepatoprotetores',
                'description' => 'Medicamentos para proteção do fígado',
                'icon' => 'liver',
                'sort_order' => 19,
            ],
            
            // Sistema Nervoso
            [
                'name' => 'Antidepressivos',
                'description' => 'Medicamentos para tratamento da depressão',
                'icon' => 'brain',
                'sort_order' => 20,
            ],
            [
                'name' => 'Ansiolíticos',
                'description' => 'Medicamentos para ansiedade',
                'icon' => 'smile',
                'sort_order' => 21,
            ],
            [
                'name' => 'Anticonvulsivantes',
                'description' => 'Medicamentos para epilepsia e convulsões',
                'icon' => 'zap',
                'sort_order' => 22,
            ],
            [
                'name' => 'Sedativos e Hipnóticos',
                'description' => 'Medicamentos para sono e sedação',
                'icon' => 'moon',
                'sort_order' => 23,
            ],
            [
                'name' => 'Antiparkinsonianos',
                'description' => 'Medicamentos para doença de Parkinson',
                'icon' => 'hand',
                'sort_order' => 24,
            ],
            
            // Sistema Endócrino
            [
                'name' => 'Antidiabéticos',
                'description' => 'Medicamentos para diabetes',
                'icon' => 'activity',
                'sort_order' => 25,
            ],
            [
                'name' => 'Insulinas',
                'description' => 'Insulinas para tratamento do diabetes',
                'icon' => 'syringe',
                'sort_order' => 26,
            ],
            [
                'name' => 'Hormônios Tireoidianos',
                'description' => 'Medicamentos para tireoide',
                'icon' => 'circle',
                'sort_order' => 27,
            ],
            [
                'name' => 'Corticosteroides',
                'description' => 'Hormônios anti-inflamatórios',
                'icon' => 'flame',
                'sort_order' => 28,
            ],
            
            // Especialidades Médicas
            [
                'name' => 'Oftalmológicos',
                'description' => 'Medicamentos para os olhos',
                'icon' => 'eye',
                'sort_order' => 29,
            ],
            [
                'name' => 'Otorrinolaringológicos',
                'description' => 'Medicamentos para ouvido, nariz e garganta',
                'icon' => 'ear',
                'sort_order' => 30,
            ],
            [
                'name' => 'Dermatológicos',
                'description' => 'Medicamentos para pele',
                'icon' => 'hand',
                'sort_order' => 31,
            ],
            [
                'name' => 'Ginecológicos',
                'description' => 'Medicamentos para saúde feminina',
                'icon' => 'female',
                'sort_order' => 32,
            ],
            [
                'name' => 'Urológicos',
                'description' => 'Medicamentos para sistema urinário',
                'icon' => 'droplet',
                'sort_order' => 33,
            ],
            [
                'name' => 'Oncológicos',
                'description' => 'Medicamentos para tratamento do câncer',
                'icon' => 'target',
                'sort_order' => 34,
            ],
            
            // Vitaminas e Suplementos
            [
                'name' => 'Vitaminas',
                'description' => 'Vitaminas essenciais para saúde',
                'icon' => 'sparkles',
                'sort_order' => 35,
            ],
            [
                'name' => 'Minerais',
                'description' => 'Suplementos minerais',
                'icon' => 'gem',
                'sort_order' => 36,
            ],
            [
                'name' => 'Suplementos Nutricionais',
                'description' => 'Complementos alimentares',
                'icon' => 'leaf',
                'sort_order' => 37,
            ],
            [
                'name' => 'Probióticos',
                'description' => 'Suplementos para flora intestinal',
                'icon' => 'bacteria',
                'sort_order' => 38,
            ],
            [
                'name' => 'Ômega 3 e Óleos',
                'description' => 'Suplementos de ácidos graxos',
                'icon' => 'fish',
                'sort_order' => 39,
            ],
            
            // Produtos de Higiene e Cuidados
            [
                'name' => 'Higiene Oral',
                'description' => 'Produtos para cuidados bucais',
                'icon' => 'smile',
                'sort_order' => 40,
            ],
            [
                'name' => 'Higiene Corporal',
                'description' => 'Sabonetes, shampoos e produtos de banho',
                'icon' => 'soap',
                'sort_order' => 41,
            ],
            [
                'name' => 'Cuidados com a Pele',
                'description' => 'Cremes, loções e produtos dermatológicos',
                'icon' => 'hand',
                'sort_order' => 42,
            ],
            [
                'name' => 'Proteção Solar',
                'description' => 'Protetores solares e produtos UV',
                'icon' => 'sun',
                'sort_order' => 43,
            ],
            [
                'name' => 'Cuidados Capilares',
                'description' => 'Produtos para cabelo',
                'icon' => 'scissors',
                'sort_order' => 44,
            ],
            
            // Produtos Infantis
            [
                'name' => 'Medicamentos Pediátricos',
                'description' => 'Medicamentos específicos para crianças',
                'icon' => 'baby',
                'sort_order' => 45,
            ],
            [
                'name' => 'Cuidados do Bebê',
                'description' => 'Produtos para cuidados infantis',
                'icon' => 'heart',
                'sort_order' => 46,
            ],
            [
                'name' => 'Fraldas e Absorventes',
                'description' => 'Produtos de higiene íntima',
                'icon' => 'package',
                'sort_order' => 47,
            ],
            
            // Equipamentos e Dispositivos
            [
                'name' => 'Equipamentos Médicos',
                'description' => 'Dispositivos médicos e equipamentos',
                'icon' => 'stethoscope',
                'sort_order' => 48,
            ],
            [
                'name' => 'Termômetros',
                'description' => 'Termômetros digitais e convencionais',
                'icon' => 'thermometer',
                'sort_order' => 49,
            ],
            [
                'name' => 'Medidores de Pressão',
                'description' => 'Aparelhos para medir pressão arterial',
                'icon' => 'gauge',
                'sort_order' => 50,
            ],
            [
                'name' => 'Glicosímetros',
                'description' => 'Aparelhos para medir glicose',
                'icon' => 'activity',
                'sort_order' => 51,
            ],
            [
                'name' => 'Material Hospitalar',
                'description' => 'Seringas, agulhas, curativos',
                'icon' => 'plus-square',
                'sort_order' => 52,
            ],
            
            // Produtos Naturais
            [
                'name' => 'Fitoterápicos',
                'description' => 'Medicamentos à base de plantas',
                'icon' => 'leaf',
                'sort_order' => 53,
            ],
            [
                'name' => 'Homeopáticos',
                'description' => 'Medicamentos homeopáticos',
                'icon' => 'droplet',
                'sort_order' => 54,
            ],
            [
                'name' => 'Óleos Essenciais',
                'description' => 'Óleos aromáticos e terapêuticos',
                'icon' => 'flower',
                'sort_order' => 55,
            ],
            [
                'name' => 'Chás Medicinais',
                'description' => 'Chás e infusões terapêuticas',
                'icon' => 'coffee',
                'sort_order' => 56,
            ],
            
            // Produtos Especiais
            [
                'name' => 'Contraceptivos',
                'description' => 'Métodos contraceptivos',
                'icon' => 'shield',
                'sort_order' => 57,
            ],
            [
                'name' => 'Produtos para Diabéticos',
                'description' => 'Produtos específicos para diabéticos',
                'icon' => 'heart-pulse',
                'sort_order' => 58,
            ],
            [
                'name' => 'Produtos Ortopédicos',
                'description' => 'Produtos para suporte ortopédico',
                'icon' => 'bone',
                'sort_order' => 59,
            ],
            [
                'name' => 'Primeiros Socorros',
                'description' => 'Kits e produtos de emergência',
                'icon' => 'first-aid',
                'sort_order' => 60,
            ],
            
            // Produtos de Beleza
            [
                'name' => 'Cosméticos',
                'description' => 'Produtos de beleza e maquiagem',
                'icon' => 'palette',
                'sort_order' => 61,
            ],
            [
                'name' => 'Anti-idade',
                'description' => 'Produtos anti-envelhecimento',
                'icon' => 'clock',
                'sort_order' => 62,
            ],
            [
                'name' => 'Perfumaria',
                'description' => 'Perfumes e fragrâncias',
                'icon' => 'spray',
                'sort_order' => 63,
            ],
            
            // Produtos Veterinários
            [
                'name' => 'Medicamentos Veterinários',
                'description' => 'Medicamentos para animais',
                'icon' => 'dog',
                'sort_order' => 64,
            ],
            [
                'name' => 'Cuidados Pet',
                'description' => 'Produtos de higiene para animais',
                'icon' => 'heart',
                'sort_order' => 65,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['name' => $categoryData['name']],
                array_merge($categoryData, [
                    'slug' => Str::slug($categoryData['name']),
                    'is_active' => true,
                ])
            );
        }
    }
}
