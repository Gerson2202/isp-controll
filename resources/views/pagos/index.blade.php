@extends('adminlte::page')
@section('title', 'Registro de pagos') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-1">
    <i class="fas fa-credit-card me-2"></i> <!-- Tarjeta de crédito -->
    Registrar Pago
</h1>
@stop

@section('content') 
    <div class="mt-8">
        @livewire('facturacion.registrar-pago')
    </div>
@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')



