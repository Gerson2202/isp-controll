@extends('adminlte::page')
@section('title', 'Tickets abiertos') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-ticket text-primary"></i> Tickets abiertos
</h1>
@stop

@section('content')
 @livewire('ticket-table')
@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')



