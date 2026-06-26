@extends('adminlte::page')
@section('title', 'Dashboard Financiero')

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-chart-line me-2 text-info"></i>Dashboard Financiero
</h1>
@stop

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-pie mr-2"></i>Resumen Financiero
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                <i class="fas fa-expand"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        @livewire('finanzas.dashboard-financiero')
    </div>
</div>
@stop

@include('partials.global-footer')