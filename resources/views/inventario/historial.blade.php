@extends('adminlte::page')
@section('title', 'Historial de movimientos') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-history mr-2 text-primary"></i>
    Historial de Movimientos
</h1>

@stop

@section('content')
    <div class="container mt-2">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="historialTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="equipos-tab" data-bs-toggle="tab" data-bs-target="#equipos"
                            type="button" role="tab" aria-controls="equipos" aria-selected="true">
                            <i class="fas fa-laptop me-1"></i>   Equipos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="consumibles-tab" data-bs-toggle="tab" data-bs-target="#consumibles"
                            type="button" role="tab" aria-controls="consumibles" aria-selected="false">
                            <i class="fas fa-boxes me-1"></i>  Consumibles
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="historialTabsContent">
                    <div class="tab-pane fade show active" id="equipos" role="tabpanel" aria-labelledby="equipos-tab">
                        @livewire('inventario.historial-movimientos-equipos')
                    </div>
                    <div class="tab-pane fade" id="consumibles" role="tabpanel" aria-labelledby="consumibles-tab">
                        @livewire('inventario.historial-movimientos-consumibles')
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
{{-- include footer y logo  --}}
@include('partials.global-footer')
