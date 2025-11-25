@extends('layouts.app')

@section('title', 'Carrinho - ' . $pharmacy->name . ' - PharmaPlace AO')
@section('description', 'Finalize sua compra na ' . $pharmacy->name . ' com segurança e praticidade.')

@section('content')
    @livewire('cart-show', ['pharmacy' => $pharmacy])
@endsection
