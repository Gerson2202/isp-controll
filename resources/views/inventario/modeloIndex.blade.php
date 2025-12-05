@extends('adminlte::page')
@section('title', 'Registro') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-server mr-2 text-primary"></i>
    Registro de Modelos de Equipo
</h1> 
@stop

@section('content')
    @livewire('modelo-crud') 
@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')


