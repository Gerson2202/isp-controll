@extends('adminlte::page')
@section('title', 'Cerrar Ticket') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-ticket-alt text-success"></i> Cerrar Ticket
</h1>
@stop

@section('content')
    @livewire('tecnico.visitas.cerrar-visita', ['visitaId' => $visita->id])
@stop


{{-- include footer y logo  --}}
@include('partials.global-footer')


