@extends('adminlte::page')
@section('title', 'Historial de Tickets') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-stream text-primary"></i> Historial de Tickets
</h1>
@stop

@section('content')
 
    @livewire('ticket-history')

@stop


{{-- include footer y logo  --}}
@include('partials.global-footer')



