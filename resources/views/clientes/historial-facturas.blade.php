@extends('adminlte::page')
@section('title', 'Historial de facturas')

@section('content_header')
    <h1 class="ml-4">
        <i class="fas fa-book"></i> Histórico de Facturas
    </h1>
@stop

@section('content')

    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">Histórico de Facturas: {{ $cliente->nombre }}</h3>
            </div>
            <div class="card-body">
                @livewire('facturacion.historial-facturas', ['cliente' => $cliente])
            </div>
        </div>
    </div>

@stop



{{-- include footer y logo  --}}
@include('partials.global-footer')
