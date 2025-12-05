@extends('adminlte::page')
@section('title', 'Dashboard') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-file-invoice mr-2 text-success"></i>
    Facturación
</h1>
@stop

@section('content')
    <div >     
         @livewire('facturacion.generar-facturas-mensuales')   
    </div>
@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')



