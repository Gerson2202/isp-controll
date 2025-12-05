@extends('adminlte::page')
@section('title', 'Registro de Bodegas') 
@section('content_header')
<br>
@stop

@section('content') 
    <div class="mt-8">
        @livewire('bodega.bodegas-crud')
    </div>
@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')




