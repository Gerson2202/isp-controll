@extends('adminlte::page')
@section('title', 'Monitoreo') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-chart-line text-info"></i> Monitoreo de nodos
</h1>  
@stop

@section('content')
    @livewire('nodo-monitoreo')
@stop


{{-- include footer y logo  --}}
@include('partials.global-footer')

