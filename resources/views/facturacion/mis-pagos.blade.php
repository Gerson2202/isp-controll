@extends('adminlte::page')

@section('title', 'Mis Pagos')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1 class="ml-3 mb-0">
        <i class="fas fa-file-invoice-dollar mr-2 text-success"></i>
        Mis Pagos
    </h1>

</div>

@stop

@section('content')

    @livewire('facturacion.mis-pagos')

@stop

@include('partials.global-footer')