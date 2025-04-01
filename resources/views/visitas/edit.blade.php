@extends('adminlte::page')

@section('title', 'Editar Visita')

@section('content_header')
    <h1 class="ml-1">Editar Visita</h1>
@stop

@section('content')
    <div class="container-fluid">
       <div class="card">
        <div class="card-header">
            <h5>Editar visita</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('visitas.update', $visita->ticket_id) }}" method="POST">
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
    <script>
        // Obtener el campo de "Fecha de Inicio" y "Fecha de Cierre"
        const fechaInicioInput = document.getElementById('fecha_inicio');
        const fechaCierreInput = document.getElementById('fecha_cierre');
    
        // Agregar un evento de escucha para detectar cambios en el campo "Fecha de Inicio"
        fechaInicioInput.addEventListener('input', function() {
            const fechaInicio = new Date(fechaInicioInput.value);
    
            // Verificar si la fecha de inicio es válida
            if (!isNaN(fechaInicio.getTime())) {
                // Sumar 2 horas a la fecha de inicio
                fechaInicio.setHours(fechaInicio.getHours() + 2);
    
                // Formatear la nueva fecha de cierre como 'YYYY-MM-DDTHH:MM'
                const fechaCierreFormateada = fechaInicio.toISOString().slice(0, 16);
    
                // Actualizar el valor del campo "Fecha de Cierre"
                fechaCierreInput.value = fechaCierreFormateada;
            }
        });
    </script>
@stop
