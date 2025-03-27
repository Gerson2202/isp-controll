@extends('adminlte::page')
@section('title', 'Dashboard')

@section('content_header')
   <h1>Vista de Equipo</h1>
   @livewireStyles
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
   <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
@stop

@section('content')
   @livewire('inventario-show', ['inventarioId' => $inventario->id])
@stop

@section('css')
@stop

@section('js')
    @livewireScripts
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    @stack('scripts')
@stop
