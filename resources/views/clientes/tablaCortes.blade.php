@extends('adminlte::page')
@section('title', 'Cortes y activaciones') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-toggle-on mr-2 text-danger"></i>
    Cortes Y Activaciones
</h1>  
@stop

@section('content')
  
    @livewire('clientes.cliente-cortes')

@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')
