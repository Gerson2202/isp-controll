@extends('adminlte::page')
@section('title', 'Inventario Unificado')

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-tasks me-2 text-info"></i>Categorías
</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-tags me-2 text-primary"></i>
                    Gestión de Categorías de Gastos
                </h5>
                <span class="badge bg-info">{{ \App\Models\CategoriaGasto::count() }}</span>
            </div>
        </div>
        <div class="card-body">
            @livewire('finanzas.categorias-gastos-index')
        </div>
    </div>
</div>
@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')