@extends('adminlte::page')
@section('title', 'Asignar Ip') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-2">
    <i class="fas fa-link text-info"></i> Asignar Ip Cliente: 
    <span class="text-info">{{ $cliente->nombre }}</span>
</h1>

@stop

@section('content')
  @livewire('asignar-ip-cliente', ['cliente_id' => $cliente->id])
@stop


{{-- include footer y logo  --}}
@include('partials.global-footer')

