@extends('adminlte::page')

@section('title', 'Estadísticas del Nodo')

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-2">

        <div>
            <h1 class="m-0 fw-bold">
                <i class="fas fa-network-wired text-primary me-2"></i>
                Estadísticas del Nodo
            </h1>
        </div>

    </div>
@stop

@section('content')

    <div class="container-fluid px-0">

        <div class="card border-0 shadow-sm">

            <div class="card-body p-3 p-md-4">

                <livewire:nodo.nodo-estadisticas :nodoId="$nodo" />

            </div>

        </div>

    </div>

@stop

@include('partials.global-footer')