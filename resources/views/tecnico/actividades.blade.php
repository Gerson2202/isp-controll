@extends('adminlte::page')
@section('title', 'Actividades') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-calendar-day mr-2 text-primary"></i>
    Actividades del Día
</h1>

@stop

@section('content')
 
@livewire('tecnico.actividades.index')

@stop
{{-- include footer y logo  --}}
@include('partials.global-footer')
