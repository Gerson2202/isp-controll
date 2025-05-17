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

{{-- Footer section --}}
@section('footer')
    <footer class="main-footer text-xs py-1" style="line-height: 1.2;">
        <div class="container-fluid">
            <div class="row align-items-center">
                <!-- Logo y texto -->
                <div class="col-8 col-sm-6">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('img/logo.png') }}" alt="Isprotik Logo" style="height: 18px; margin-right: 8px;">
                        <div>
                            <strong class="text-sm">© {{ date('Y') }} <a href="{{ route('dashboard') }}" class="text-primary" style="text-decoration: none;">Isprotik</a></strong>
                            <span class="text-muted d-none d-md-inline" style="font-size: 0.75rem;"> | Gestión para ISPs</span>
                        </div>
                    </div>
                </div>
                
                <!-- Versión y soporte -->
                <div class="col-4 col-sm-6 text-right">
                    <span class="d-none d-sm-inline text-muted mr-2" style="font-size: 0.75rem;"><strong>v1.0</strong></span>
                    <a href="https://wa.me/573215852059" target="_blank" class="text-muted" style="font-size: 0.75rem; text-decoration: none;">
                        <i class="fas fa-headset"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <style>
        .main-footer {
            background: #f4f6f9;
            border-top: 1px solid #dee2e6;
            padding: 4px 0 !important;
        }
        .main-footer a:hover {
            color: var(--primary) !important;
        }
        .main-footer img {
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        .main-footer img:hover {
            opacity: 1;
        }
    </style>

    <style>
        .main-footer {
            background: #f4f6f9;
            padding: 1rem;
            border-top: 1px solid #dee2e6;
        }
        .main-footer a:hover {
            color: var(--primary) !important;
            text-decoration: none;
        }
    </style>
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
    <!-- Logo en sidebar-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var logoItem = document.querySelector('li#sidebar-logo-item');
            if (logoItem) {
                logoItem.innerHTML = '<img src="{{ asset('img/logo.png') }}" style="max-width:120px;max-height:90px; margin-left:70px;" alt="Logo" />';
            }
        });
    </script>

    @stack('scripts')
@stop
