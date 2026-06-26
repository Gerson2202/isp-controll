@extends('adminlte::page')
@section('title', 'Inventario Unificado')

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-tasks me-2 text-info"></i>Registrar Gastos
</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-body p-3">
            @livewire('finanzas.gastos-index')
        </div>
    </div>
</div>
@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')