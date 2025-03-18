@extends('adminlte::page')
@section('title', 'Dashboard') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
   <h1>Clientes</h1>
@stop

@section('content')
 
    @livewire('buscador-clientes')
    @livewireScripts
@stop

@section('css')
    @livewireStyles
@stop

@section('js')
    
@stop


