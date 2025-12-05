@extends('adminlte::page')
@section('title', 'Graficas') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')

   <h1 class="ml-3">Graficas del cliente</h1>

@stop

@section('content')
    <div class="container-fluid ">
        @livewire('clientes.graficas-consumo-cliente', ['cliente' => $cliente])  

    </div>

@stop


{{-- include footer y logo  --}}
@include('partials.global-footer')


