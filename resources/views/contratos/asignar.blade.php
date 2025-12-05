@extends('adminlte::page')
@section('title', 'Contrato') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-user-check mr-2"></i> <!-- Usuario con check -->
    Asignar Contrato
</h1>   
@stop

@section('content')
    @livewire('asignar-contrato', ['cliente' => $cliente->id])
@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')
