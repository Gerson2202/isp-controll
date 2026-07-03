@extends('adminlte::page')

@section('title', 'Avisos')

@section('content_header')
    <div class="d-flex align-items-center">
        <i class="fas fa-comments text-success fa-2x mr-3"></i>

        <div>
            <h1 class="m-0 font-weight-bold">
                Avisos
            </h1>
        </div>
    </div>
@stop

@section('content')
  <div class="card card-success card-outline">
    <div class="card-body p-0">
        @livewire('conversaciones.envio-masivo-whats-app')
    </div>
</div>
@stop

@include('partials.global-footer')

@push('css')
@endpush

@push('js')
@endpush
