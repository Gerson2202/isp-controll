@extends('adminlte::page')
@section('title', 'Gestion de Roles') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-user-lock text-info"></i> Administración de Roles y Permisos
</h1>   
@stop

@section('content')
 
     @livewire('rolesComponent') 

@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')

