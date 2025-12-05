@extends('adminlte::page')
@section('title', 'Crear Planes') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-clipboard-list mr-2 text-success"></i>
    Gestión de Planes
</h1>
@stop

@section('content')
  @livewire('planes-formulario')
  {{-- @livewire('modal-component') --}}

@stop
{{-- include footer y logo  --}}
@include('partials.global-footer')