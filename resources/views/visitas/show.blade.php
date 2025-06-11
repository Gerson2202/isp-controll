@extends('adminlte::page')

@section('title', 'Vista de visita')

@section('content_header')
    <h1 class="ml-2">
        Visita
        <i class="fas fa-user text-blue-500 ml-2"></i>
    </h1>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

@stop
@section('content')
    <div class="card shadow h-100 d-flex flex-column rounded-0">
        <!-- Encabezado fijo -->
        <div class="card-header bg-primary text-white rounded-0 ">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Detalles de la Visita</h3>
                <a href="{{ route('visitas.tabla') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <!-- Cuerpo con scroll -->
        <div class="card-body flex-grow-1 overflow-auto p-4">
            <!-- Información básica de la visita -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2">Información de la Visita</h5>
                    <dl class="row">
                        <dt class="col-sm-4">ID:</dt>
                        <dd class="col-sm-8">{{ $visita->id }}</dd>

                        <dt class="col-sm-4">Estado:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $visita->estado == 'completado' ? 'success' : ($visita->estado == 'en_proceso' ? 'warning' : 'danger') }}">
                                {{ ucfirst(str_replace('_', ' ', $visita->estado)) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Fecha Inicio:</dt>
                        <dd class="col-sm-8">
                            @if($visita->fecha_inicio)
                                {{ is_string($visita->fecha_inicio) ? \Carbon\Carbon::parse($visita->fecha_inicio)->format('d/m/Y H:i') : $visita->fecha_inicio->format('d/m/Y H:i') }}
                            @else
                                No especificada
                            @endif
                        </dd>

                        @if($visita->fecha_cierre)
                        <dt class="col-sm-4">Fecha Cierre:</dt>
                        <dd class="col-sm-8">
                            {{ is_string($visita->fecha_cierre) ? \Carbon\Carbon::parse($visita->fecha_cierre)->format('d/m/Y H:i') : $visita->fecha_cierre->format('d/m/Y H:i') }}
                        </dd>
                        @endif
                    </dl>
                </div>

                <div class="col-md-6">
                    <h5 class="border-bottom pb-2">Descripción y Solución</h5>
                    <div class="mb-3">
                        <strong>Descripción:</strong>
                        <p class="text-muted">{{ $visita->descripcion ?? 'No hay descripción registrada' }}</p>
                    </div>
                    <div>
                        <strong>Solución:</strong>
                        <p class="text-muted">{{ $visita->solucion ?? 'No hay solución registrada' }}</p>
                    </div>
                </div>
            </div>

            <!-- Información del ticket relacionado -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="border-bottom pb-2">Información del Ticket Relacionado</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID Ticket</th>
                                    <th>Tipo de Reporte</th>
                                    <th>Situación</th>
                                    <th>Estado</th>
                                    <th>Fecha Cierre</th>
                                    <th>Solución</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $visita->ticket->id }}</td>
                                    <td>{{ $visita->ticket->tipo_reporte }}</td>
                                    <td>{{ $visita->ticket->situacion }}</td>
                                    <td>
                                        <span class="badge bg-{{ $visita->ticket->estado == 'cerrado' ? 'success' : 'warning' }}">
                                            {{ ucfirst($visita->ticket->estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($visita->ticket->fecha_cierre)
                                            {{ is_string($visita->ticket->fecha_cierre) ? \Carbon\Carbon::parse($visita->ticket->fecha_cierre)->format('d/m/Y H:i') : $visita->ticket->fecha_cierre->format('d/m/Y H:i') }}
                                        @else
                                            Pendiente
                                        @endif
                                    </td>                                    
                                    <td>{{ $visita->ticket->solucion ?? 'No registrada' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Información del cliente -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="border-bottom pb-2">Información del Cliente</h5>
                    <div class="d-flex align-items-center">
                        <div class="me-3 bg-light rounded-circle p-3">
                            <i class="fas fa-user fa-2x text-primary"></i>
                        </div>
                        <div>
                                <h5 class="mb-1">
                                    <a href="{{ route('clientes.show', $visita->ticket->cliente->id ?? '') }}" class="text-decoration-none">
                                        {{ $visita->ticket->cliente->nombre ?? 'Cliente no especificado' }}
                                    </a>
                                </h5>                            <p class="mb-1 text-muted">
                                <i class="fas fa-id-card"></i> ID: {{ $visita->ticket->cliente->id ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Galería de fotos -->
            @if($visita->fotos->count() > 0)
            <div class="row">
                <div class="col-12">
                    <h5 class="border-bottom pb-2">Fotos de la Visita</h5>
                    <div class="row">
                        @foreach($visita->fotos as $foto)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <a href="{{ asset('storage/' . $foto->ruta) }}" data-toggle="lightbox" data-gallery="visita-gallery">
                                    <img src="{{ asset('storage/' . $foto->ruta) }}" class="card-img-top img-fluid" alt="{{ $foto->nombre_original }}" style="height: 200px; object-fit: cover;">
                                </a>
                                <div class="card-body">
                                    <h6 class="card-title">{{ $foto->nombre_original }}</h6>
                                    <p class="card-text small text-muted">{{ $foto->descripcion ?? 'Sin descripción' }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-info">
                No hay fotos registradas para esta visita.
            </div>
            @endif
        </div>

        <!-- Pie de página fijo -->
        <div class="card-footer bg-light rounded-0 ">
            <div class="d-flex justify-content-between">
                <a href="{{ route('visitas.edit', $visita->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
        </div>
    </div>
@endsection


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
