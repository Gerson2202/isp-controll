@extends('adminlte::page')
@section('title', 'Dashboard Inventario') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-globe-americas mr-2 text-primary"></i>
    Consulta de Inventario Global
</h1>  

@stop

@section('content')
  @livewire('Inventario.Dashboard')
  {{-- @livewire('modal-component') --}}

@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')

