@extends('layouts.app')

@section('title', 'Finalizar Compra - PharmaPlace AO')
@section('description', 'Finalize sua compra de medicamentos na {{ $pharmacy->name }}')

@section('content')
    {{-- Renderizar o componente Livewire --}}
    @livewire('checkout-form', ['pharmacy' => $pharmacy])
@endsection

@push('styles')
<style>
.bg-green-500 {
    background-color: #10b981 !important;
    color: white !important;
}

.bg-green-500:hover {
    background-color: #059669 !important;
}

.text-green-500 {
    color: #10b981 !important;
}

.text-green-600 {
    color: #059669 !important;
}

.focus\:ring-green-500:focus {
    --tw-ring-color: #10b981 !important;
}

.focus\:border-green-500:focus {
    border-color: #10b981 !important;
}

.border-green-500 {
    border-color: #10b981 !important;
}

.bg-green-50 {
    background-color: #f0fdf4;
}
</style>
@endpush
