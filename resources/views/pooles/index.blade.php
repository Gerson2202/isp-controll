@extends('adminlte::page')
@section('title', 'Registro de Pool') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-database mr-2"></i> <!-- Base de datos -->
    Gestionar Pooles
</h1>
@stop

@section('content')
   
   @livewire('pool-component')   

@stop
{{-- include footer y logo  --}}
@include('partials.global-footer')


