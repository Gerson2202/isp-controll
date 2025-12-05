@extends('adminlte::page')

@section('title', 'Tabla de programacion')

@section('content_header')
    <h1 class="ml-2">
        <i class="fas fa-calendar-alt text-primary l-2"></i>
        Tabla de programación       
    </h1>

@stop
@section('content')
    @livewire('visitas.tabla-visitas')
@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')


