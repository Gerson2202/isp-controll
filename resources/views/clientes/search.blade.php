@extends('adminlte::page')
@section('title', 'Buscador') 

@section('content_header')
<h1>
    <i class="fas fa-search me-2"></i>Buscar <i class="fas fa-users ms-1 me-2"></i>Clientes
</h1>
@stop

@section('content')
 
    @livewire('buscador-clientes')

@stop

 @include('partials.global-footer')
