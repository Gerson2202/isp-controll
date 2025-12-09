@extends('adminlte::page')
@section('title', 'Registro de cliente') 

@section('content_header')
<h1>
    <i class="fas fa-user-circle me-2"></i>Registro de Clientes
</h1>   

@stop

@section('content')
  @livewire('cliente-formulario')
 

@stop

@include('partials.global-footer')




