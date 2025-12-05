@extends('adminlte::page')
@section('title', 'Dashboard') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-chart-line mr-2 text-success"></i>
    Dashboard Financiero
</h1>
@stop

@section('content')
    <div >     
        @livewire('facturacion.dashboard-financiero')   
    </div>
@stop
{{-- include footer y logo  --}}
@include('partials.global-footer')


