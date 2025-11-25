@extends('layouts.app')

@section('title', 'Explorar Medicamentos - PharmaPlace AO')
@section('description', 'Explore nossa vasta seleção de medicamentos e produtos farmacêuticos disponíveis em farmácias de Angola.')

@push('styles')
<style>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Verde clínico correto - mesmo do "mostrando" */
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

/* Background verde claro */
.bg-green-50 {
    background-color: #f0fdf4;
}

.border-green-200 {
    border-color: #bbf7d0;
}
</style>
@endpush

@section('content')
    @livewire('explore-page')
@endsection
