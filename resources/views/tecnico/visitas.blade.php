@extends('adminlte::page')
@section('title', 'Tickets Asignados') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-2">
    <i class="fas fa-tasks mr-2"></i> <!-- Tareas -->
    Tickets Asignados
</h1>
@stop

@section('content')
  @livewire('tecnico.visitas.tabla')
@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')
