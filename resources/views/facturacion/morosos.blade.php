@extends('adminlte::page')
@section('title', 'Clientes morosos') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-chart-line mr-2 text-success"></i>
    Clientes Morosos
</h1>
@stop

@section('content')
    <div >     
         @livewire('contratos.morosos')   
    </div>
@stop
{{-- include footer y logo  --}}
@include('partials.global-footer')


