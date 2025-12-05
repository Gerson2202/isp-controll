@extends('adminlte::page')
@section('title', 'Registro de pagos') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')

    <h1 class="ml-1"><i class="bi bi-cash-coin me-2"></i>Administracion de Bodegas</h1>

@stop

@section('content') 
    <div class="mt-8">
        @livewire('bodega.show', ['bodega' => $bodega])
    </div>
@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')
