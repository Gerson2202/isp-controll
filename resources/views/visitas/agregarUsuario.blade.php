@extends('adminlte::page')
@section('title', 'Agregar usuarios') 

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-user-plus text-primary"></i> Agregar usuario a visita
</h1>   

@stop

@section('content')
    <div class="container mt-2">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-plus me-2"></i>Agregar Usuario a la Visita #{{ $visita->id }}
                </h5>
            </div>

            <div class="card-body">
                <p class="mb-3">
                    <strong>Ticket:</strong> {{ $visita->ticket_id }} <br>
                    <strong>Descripción:</strong> {{ $visita->descripcion }}
                </p>

                <form action="{{ route('visitas.guardarUsuarios', $visita->id) }}" method="POST">
                    @csrf

                    <input type="hidden" name="fecha_inicio" value="{{ $fecha_inicio }}">
                    <input type="hidden" name="fecha_cierre" value="{{ $fecha_cierre }}">

                    <div class="mb-4">
                        <label for="usuarios" class="form-label fw-semibold">
                            <i class="bi bi-people me-1"></i>Seleccionar usuarios
                        </label>

                        @if ($usuarios->isEmpty())
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                No hay usuarios disponibles para agregar a esta visita.
                            </div>
                        @else
                            <select name="usuarios[]" id="usuarios" class="form-select" multiple required
                                style="height:auto;">
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}"
                                        style="border-bottom: 1px solid #ddd; padding: 6px 8px;">
                                        {{ $usuario->name }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="form-text">
                                Puedes seleccionar uno o varios usuarios manteniendo presionada la tecla
                                <strong>Ctrl</strong> (Windows) o <strong>Cmd</strong> (Mac).
                            </div>
                        @endif
                    </div>


                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save me-1"></i>Guardar Usuarios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')



