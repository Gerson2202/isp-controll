@extends('adminlte::page')

@section('title', 'Editar Visita')

@section('content_header')
    <h1 class="ml-1">Editar Visita</h1>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

@stop

@section('content')
    <div class="container-fluid">
       <div class="card">
        <div class="card-header">
            <h5>Editar visita</h5>
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        

        </div>
        <div class="card-body">
            <form action="{{ route('visitas.update', $visita->id) }}" method="POST">
                @csrf
                @method('PUT')
            
                <div class="form-group">
                    <label for="fecha_inicio">Fecha de Inicio</label>
                    <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', $visita->fecha_inicio) }}" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="fecha_cierre">Fecha de Cierre</label>
                    <input type="datetime-local" id="fecha_cierre" name="fecha_cierre" value="{{ old('fecha_cierre', $visita->fecha_cierre) }}" class="form-control" >
                </div>
            
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" name="descripcion">{{ $visita->descripcion }}</textarea>
                </div>
            
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select class="form-control" name="estado" required>
                        <option value="Pendiente" {{ $visita->estado == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="En Progreso" {{ $visita->estado == 'En Progreso' ? 'selected' : '' }}>En Progreso</option>
                        <option value="Completada" {{ $visita->estado == 'Completada' ? 'selected' : '' }}>Completada</option>
                    </select>
                </div>
            
                <div class="form-group">
                    <label for="solucion">Solución</label>
                    <textarea class="form-control" name="solucion">{{ $visita->solucion }}</textarea>
                </div>
            
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </form>
            
            <form method="POST" action="{{ route('enviarACola', $visita->id) }}" style="display: inline;">
                @csrf
                @method('PUT')
                
                <!-- Verificar si ambos campos están llenos antes de mostrar el botón -->
                @if($visita->fecha_inicio && $visita->fecha_cierre)
                    <button type="submit" class="btn btn-warning mt-3">Enviar a cola de programación</button>
                @endif
            </form>
            
        </div>
       </div>
    </div>
@stop

@section('css')
    <!-- Puedes agregar estilos personalizados aquí si es necesario -->
@stop

@section('js')
    @livewireScripts  <!-- Livewire debe cargarse antes que cualquier otro script -->
    <!-- Agregar los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Agregar SweetAlert2 desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Aquí incluye tus scripts personalizados -->
    @stack('scripts')
@stop
