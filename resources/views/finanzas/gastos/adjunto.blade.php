@extends('adminlte::page')
@section('title', 'Gastos Archivos')

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-paperclip me-2 text-info"></i>Subir Documentos
</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-body p-3">
            {{-- Pasamos el ID al componente --}}
            @livewire('finanzas.gasto-adjuntos', ['gastoId' => $gastoId])
        </div>
    </div>
</div>
@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')