<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use App\Models\PharmacyProduct;

class ProductDetailPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-eye';
    protected static string $view = 'filament.pages.product-detail';
    protected static ?string $title = 'Detalhes do Produto';
    protected static bool $shouldRegisterNavigation = false;

    public Product $record;
    public $selectedTab = 'description';
    public $quantity = 1;

    public function mount(int $record): void
    {
        $this->record = Product::with(['category', 'pharmacyProducts.pharmacy'])->findOrFail($record);
    }

    //protected function getTitle(): string
     public function getTitle(): string
    {
        return $this->record->name;
    }

    public function getPharmacyProducts()
    {
        return PharmacyProduct::where('product_id', $this->record->id)
            ->with('pharmacy')
            ->where('is_available', true)
            ->orderBy('price')
            ->get();
    }

    public function addToCart($pharmacyProductId)
    {
        // Implementar lógica do carrinho
        session()->push('cart', [
            'pharmacy_product_id' => $pharmacyProductId,
            'quantity' => $this->quantity,
            'added_at' => now()
        ]);
        
        $this->dispatch('cart-updated');
    }

    public function setTab($tab)
    {
        $this->selectedTab = $tab;
    }
}
