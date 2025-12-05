@extends('adminlte::page')
@section('title', 'Buscador') 

@section('content_header')
   <h1>Clientes</h1>
@stop

@section('content')
 
    @livewire('buscador-clientes')

@stop

 @include('partials.global-footer')
