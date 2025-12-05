@extends('adminlte::page')
@section('title', 'Informacion de equipo')

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-info-circle mr-2 text-info"></i>
    Detalles de Equipo
</h1>   
@stop

@section('content')
   @livewire('inventario-show', ['inventarioId' => $inventario->id])
@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')
