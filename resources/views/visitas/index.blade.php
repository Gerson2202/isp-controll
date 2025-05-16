@extends('adminlte::page')

@section('title', 'Editar Visita')

@section('content_header')
    <h1>Editar Visita</h1>
@stop

@section('content')
    <!-- Logo en sidebar-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var logoItem = document.querySelector('li#sidebar-logo-item');
            if (logoItem) {
                logoItem.innerHTML = '<img src="{{ asset('img/logo.png') }}" style="max-width:120px;max-height:90px; margin-left:70px;" alt="Logo" />';
            }
        });
    </script>

@stop
