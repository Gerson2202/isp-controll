@extends('adminlte::page')
@section('title', 'Lista de inventario') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-cogs me-2 text-primary"></i>
    Lista de Equipos
</h1>

@stop

@section('content')
    <div class="container-fluid ">
        <div class="card">
            <div class="card-header">
                <h3>Equipos registrados en inventario</h3>
            </div>
            <div class="card-body">
                <!-- Tabla de inventarios -->
                <div class="table-responsive">
                    <table id="inventarios-table" class="table table-striped table-bordered nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Mac</th>
                                <th>Asignado a</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventarios as $inventario)
                            <tr>
                                <td>{{ $inventario->id }}</td>
                                <td>{{ $inventario->modelo->nombre }}</td>
                                <td>{{ $inventario->descripcion }}</td>
                                <td>{{ $inventario->mac }}</td>
                                <td>
                                    <!-- Lógica para mostrar a qué está asignado el inventario -->
                                    @if($inventario->nodo)
                                    <strong>Nodo:</strong>  {{ $inventario->nodo->nombre }} <!-- Si está asignado a un nodo, mostramos el nombre del nodo -->
                                    @elseif($inventario->cliente)
                                    <strong>Cliente:</strong> {{$inventario->cliente->nombre}}<!-- Si está asignado a un cliente, mostramos "Cliente" -->
                                    @else
                                        No asignado <!-- Si no está asignado a ninguno, mostramos "No asignado" -->
                                    @endif
                                </td>
                                <td>
                                    <!-- Botón para ver detalles -->
                                    <a href="{{ route('equipos.show', $inventario->id) }}" class="btn btn-info btn-sm">Ver detalles</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>
@stop
{{-- include footer y logo  --}}
@include('partials.global-footer')





