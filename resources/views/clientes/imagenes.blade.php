@extends('adminlte::page')
@section('title', 'Imagenes') 

@section('content_header')
    <h1 class="ml-3">
        <i class="fas fa-image me-2"></i> Imágenes del Cliente
    </h1>
@stop

@section('content')
    @livewire('clientes.clientes-imagenes', ['clienteId' => $cliente->id])

@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')


