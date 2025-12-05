@extends('adminlte::page')
@section('title', 'Aministracion de usuarios') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-user-shield text-primary"></i> Administración de usuarios
</h1>
@stop

@section('content')
    @livewire('usuarios')
@stop
    
{{-- include footer y logo  --}}
@include('partials.global-footer')

