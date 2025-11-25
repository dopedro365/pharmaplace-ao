@props(['product', 'hidePharmacyName' => false])

@livewire('product-card', ['product' => $product, 'hidePharmacyName' => $hidePharmacyName], key($product->id))
