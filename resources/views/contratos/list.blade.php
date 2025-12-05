@extends('adminlte::page')
@section('title', 'Lista de contratos') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
   <h1 class="ml-3">Lista de  Contratos</h1>
@stop

@section('content')
    @livewire('contratos.contratos-list')
@stop
{{-- include footer y logo  --}}
@include('partials.global-footer')




