@extends('adminlte::page')
@section('title', 'Movimientos') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-exchange-alt mr-2 text-info"></i>
    Registrar Movimientos
</h1>   
@stop

@section('content')
<div class="container mt-2">
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="movimientosTabs" role="tablist">
               <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="consumibles-tab" data-bs-toggle="tab" 
                            data-bs-target="#consumibles" type="button" role="tab" aria-selected="false">
                        <i class="fas fa-boxes me-1"></i> Movimiento de Consumibles
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="equipos-tab" data-bs-toggle="tab" 
                            data-bs-target="#equipos" type="button" role="tab" aria-selected="true">
                        <i class="fas fa-laptop me-1"></i> Movimiento de Equipos
                    </button>
                </li>
                
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="movimientosTabsContent">
                <div class="tab-pane fade " id="equipos" role="tabpanel">
                    @livewire('inventario.movimiento-equipo')
                </div>
                <div class="tab-pane fade show active" id="consumibles" role="tabpanel">
                    @livewire('inventario.movimiento-consumibles')
                </div>
            </div>
        </div>
    </div>
</div>
@stop
{{-- include footer y logo  --}}
@include('partials.global-footer')