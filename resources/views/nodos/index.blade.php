@extends('adminlte::page')
@section('title', 'Mis nodos') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-network-wired text-info"></i> Nodos
</h1>
@stop

@section('content')
  @livewire('nodos-index')
@stop
{{-- include footer y logo  --}}
@include('partials.global-footer')



