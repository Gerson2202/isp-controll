@extends('adminlte::page')
@section('title', 'Registro de cliente') 

@section('content_header')
   <h1>Registro de clientes </h1>
   @livewireStyles
    <!-- Agrega los estilos de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">


@stop

@section('content')
  @livewire('cliente-formulario')
  {{-- @livewire('modal-component') --}}

@stop

@include('partials.global-footer')




