@extends('adminlte::page')
@section('title', 'Aps') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-broadcast-tower mr-2 text-primary"></i>
    Access Points
</h1>
@stop

@section('content')

  @livewire('ap.ap-index')

@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')
