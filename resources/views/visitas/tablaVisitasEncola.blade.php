@extends('adminlte::page')
@section('title', 'Cola de programación') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-clock text-info"></i> Cola de programación
</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Visitas en Cola de Programación</h5>
                <!-- Botón Agregar Visita -->
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                    data-bs-target="#agregarVisitaModal">
                    <i class="fas fa-plus"></i> Agregar Visita
                </button>
            </div>
            <div class="card-body">
                <!-- Modal para agregar visita -->
                <div class="modal fade" id="agregarVisitaModal" tabindex="-1" aria-labelledby="agregarVisitaModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('visitas.store') }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="agregarVisitaModalLabel">Agregar Nueva Visita</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="titulo" class="form-label">Título *</label>
                                        <input type="text" class="form-control" id="titulo" name="titulo" required
                                            placeholder="Ingrese el título de la visita">
                                    </div>
                                    <div class="mb-3">
                                        <label for="descripcion" class="form-label">Descripción *</label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required
                                            placeholder="Describa los detalles de la visita"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Guardar Visita</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Contenedor scrollable y responsivo -->
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Descripción</th>
                                <th>#Ticket</th>
                                <th>Cliente</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($visitasEnCola as $visita)
                                <tr>
                                    <td>{{ $visita->id }}</td>
                                    <td>{{ $visita->descripcion }}</td>
                                    <td>{{ $visita->ticket ? $visita->ticket->id : 'Sin ticket' }}</td>
                                    <td>
                                        @if ($visita->ticket && $visita->ticket->cliente)
                                            <a href="{{ route('clientes.show', $visita->ticket->cliente->id) }}"
                                                target="_blank" class="btn btn-outline-info btn-sm">
                                                {{ $visita->ticket->cliente->nombre }}
                                            </a>
                                        @else
                                            Sin cliente
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('visitas.edit', $visita->id) }}"
                                            class="btn btn-info btn-sm">Agendar o actualizar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No hay visitas en cola de programación.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

@stop


{{-- include footer y logo  --}}
@include('partials.global-footer')


