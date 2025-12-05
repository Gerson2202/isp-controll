@extends('adminlte::page')
@section('title', 'Consumibles') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-2">
    <i class="fas fa-box-open mr-1 text-warning"></i>
    Consumibles
</h1>

@stop

@section('content')

  @livewire('consumibles.index')

@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')

