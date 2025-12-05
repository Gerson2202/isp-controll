@extends('adminlte::page')
@section('title', 'Editar Ticke') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
  <h1 class="ml-3">
    <i class="fas fa-pencil-alt text-success"></i> Editar Ticket
</h1>
  

@stop

@section('content')
   @livewire('ticket-edit', ['ticketId' => $ticketId->id])
@stop


{{-- include footer y logo  --}}
@include('partials.global-footer')

