@extends('adminlte::page')
@section('title', 'Bodega Personal') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-2">
    <i class="fas fa-box mr-2"></i> <!-- Caja -->
    Bodega Personal
</h1>
@stop

@section('content')
    <div class="card shadow-sm rounded-0">
        <div class="card-header bg-primary text-white rounded-0">
            <h3 class="mb-0"><i class="fas fa-warehouse me-2"></i> Mi Bodega</h3>
        </div>

        <div class="card-body p-4">
            <ul class="nav nav-tabs" id="bodegaTabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#equipos">Equipos</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#consumibles">Consumibles</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#historial">Historial</a></li>
            </ul>

            <div class="tab-content mt-4">
                <div class="tab-pane fade show active" id="equipos">@livewire('tecnico.bodega.equipos')</div>
                <div class="tab-pane fade" id="consumibles">@livewire('tecnico.bodega.consumibles')</div>
                <div class="tab-pane fade" id="historial">@livewire('tecnico.bodega.historial')</div>
            </div>
        </div>
    </div>
@endsection
{{-- include footer y logo  --}}
@include('partials.global-footer')