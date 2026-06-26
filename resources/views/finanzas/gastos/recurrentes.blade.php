@extends('adminlte::page')
@section('title', 'Inventario Unificado')

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-sync-alt me-2 text-info"></i> Gastos Recurrentes
</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-body p-3">
            @livewire('finanzas.gastos-recurrentes-index')
        </div>
    </div>
</div>
@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')