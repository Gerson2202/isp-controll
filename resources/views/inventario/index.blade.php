@extends('adminlte::page')
@section('title', 'Registro de equipos') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
 <h1 class="ml-3">
    <i class="fas fa-desktop mr-2 text-primary"></i>
    Registro de Equipos
</h1>

@stop


@section('content')

@livewire('crear-inventario')

@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')
