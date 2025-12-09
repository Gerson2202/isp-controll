@extends('adminlte::page')
@section('title', 'Inventario Unificado')

@section('content_header')
<h1 class="ml-1">
    <i class="fas fa-tasks me-2 text-info"></i>Detalle de Inventario
</h1>

@stop

@section('content')
    @livewire('inventario.detalle-inventario', ['tipo' => $tipo, 'id' => $id])

@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')
