@extends('adminlte::page')
@section('title', 'Informacion de Nodo') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3 d-flex align-items-center">
    <i class="fas fa-server me-2 text-secondary"></i>
    <span>Nodo</span>
    <span class="badge bg-info ms-2">{{ $nodo->nombre }}</span>
</h1>
@stop

@section('content')
 @livewire('nodo.nodos-detalles', ['nodo' => $nodo]) 
@stop


{{-- include footer y logo  --}}
@include('partials.global-footer')



