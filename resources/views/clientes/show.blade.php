@extends('adminlte::page')
@section('title', 'Informacion')
@section('content_header')
   <h1 class="ml-3">Informacion del cliente</h1>
   @livewireStyles
   
   <!-- Agrega los estilos de Bootstrap -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
   <!-- Toastr -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

@stop

@section('content')

    <div class="container-fluid min-vh-100 d-flex flex-column">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informacion del Cliente</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if(!$cliente->ip)
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Atención:</strong> Este cliente no tiene IP asignada. No se pueden realizar cambios ni suspensiones en MikroTik.
                </div>
                @endif
                @if(Session::has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ Session::get('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
            </div>
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs card-header-tabs" id="clienteTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab">Información</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="equipos-tab" data-toggle="tab" href="#equipos" role="tab">Ver Equipos Asignados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="nueva-pestana-tab" data-toggle="pill" href="#nueva-pestana" role="tab">Modificar estado</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="modificar-plan-tab" data-toggle="pill" href="#modificar-plan" role="tab">Modificar Plan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="modificar-nodo-tab" data-toggle="pill" href="#modificar-nodo" role="tab">Cambiar de Nodo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="facturas-tab" data-toggle="pill" href="#facturas" role="tab">Estado de Factura</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" id="grafica-tab" data-toggle="pill" href="#grafica" role="tab">Grafica de consumo</a>
                    </li> --}}
                </ul>
                
                <!-- Tab panes -->
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="info" role="tabpanel">
                        {{-- Datos personales --}}
                        <div class="row">
                            <div class="col-lg-4">
                                <h3 class="text-primary"><i class="fas fa-paint-brush"></i> {{$cliente->nombre}}</h3>
                                
                                <h5 class="mt-3 text-center text-muted"><strong>Datos personales</strong></h5>
                                <div>
                                    <!-- Botón para abrir el modal -->
                                    <button class="btn btn-warning btn-sm mb-3 me-2 mt-3" data-bs-toggle="modal" data-bs-target="#editClienteModal">
                                        <i class="fas fa-edit me-1"></i> Editar Información
                                    </button>

                                    <!-- Botón para ir a la vista de imágenes del cliente -->
                                    <a href="{{ route('cliente.imagenes', $cliente->id) }}" class="btn btn-info btn-sm mb-3 mt-3">
                                        <i class="fas fa-image me-1"></i> Ver Imágenes
                                    </a>


                                                    
                                    <ul class="list-unstyled">
                                        <li class="text-secondary"><i class="far fa-fw fa-file-word"></i><strong> Cedula:</strong> {{$cliente->cedula}}</li>
                                        <li class="text-secondary"><i class="far fa-fw fa-file-word"></i><strong> Telefono:</strong> {{$cliente->telefono}}</li>
                                        <li class="text-secondary"><i class="far fa-fw fa-envelope"></i><strong> Correo:</strong> {{$cliente->correo}}</li>
                                        <li class="text-secondary"><i class="far fa-fw fa-image"></i><strong> Direccion:</strong> {{$cliente->direccion}}</li>
                                        <li class="text-secondary">
                                            <i class="fas fa-fw fa-file-lines"></i>
                                            <strong>Descripcion:</strong> 
                                            @if($cliente->descripcion)
                                                @php
                                                    $partes = explode('-', $cliente->descripcion, 2);
                                                    $texto = $partes[0];
                                                    $url = count($partes) > 1 ? $partes[1] : null;
                                                @endphp
                                                
                                                {{ $texto }}
                                                @if($url && filter_var($url, FILTER_VALIDATE_URL))
                                                    - <a href="{{ $url }}" target="_blank" class="text-primary">Acceder</a>
                                                @elseif($url)
                                                    - {{ $url }}
                                                @endif
                                            @else
                                                Sin datos
                                            @endif
                                        </li>
                                        <li class="text-secondary">
                                            <i class="fas fa-map-marker-alt me-2"></i><strong> Coordenada:</strong>
                                            @if($cliente->latitud && $cliente->longitud)
                                                <a href="https://www.google.com/maps?q={{ $cliente->latitud }},{{ $cliente->longitud }}" 
                                                    target="_blank" 
                                                    class="btn btn-sm btn-outline-info ms-2" 
                                                    title="Ver en Google Maps">
                                                    <i class="fas fa-map-marked-alt"></i> Ver en mapa
                                                </a>
                                                @else()
                                                Sin datos de coordenadas
                                            @endif
                                        </li>
                                        <li class=" text-secondary"><i class="far fa-calendar-alt fa-fw"></i><strong> Fecha de registro:</strong> {{$cliente->created_at}}</li>
                                    </ul>
                                    
                                    <!-- Modal de Edición -->
                                    <div class="modal fade" id="editClienteModal" tabindex="-1" aria-labelledby="editClienteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editClienteModalLabel">Editar Información del Cliente</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('clientes.update', $cliente->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label for="nombre" class="form-label">Nombre</label>
                                                                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $cliente->nombre }}">
                                                                </div>                      
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="cedula" class="form-label">Cédula</label>
                                                                    <input type="text" class="form-control" id="cedula" name="cedula" value="{{ $cliente->cedula }}">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="telefono" class="form-label">Teléfono</label>
                                                                    <input type="text" class="form-control" id="telefono" name="telefono" value="{{ $cliente->telefono }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="correo" class="form-label">Correo</label>
                                                                    <input type="email" class="form-control" id="correo" name="correo" value="{{ $cliente->correo }}">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="direccion" class="form-label">Dirección</label>
                                                                    <input type="text" class="form-control" id="direccion" name="direccion" value="{{ $cliente->direccion }}" >
                                                                </div>
                                                            </div>
                                                        </div>
                                                      <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="latitud" class="form-label">Latitud</label>
                                                                <input type="number" class="form-control" id="latitud" name="latitud" 
                                                                    value="{{ $cliente->latitud }}"
                                                                    min="-90" max="90" step="0.000001"
                                                                    placeholder="Ej: -12.345678"
                                                                    title="La latitud debe ser entre -90 y 90 con hasta 6 decimales">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="longitud" class="form-label">Longitud</label>
                                                                <input type="number" class="form-control" id="longitud" name="longitud" 
                                                                    value="{{ $cliente->longitud }}"
                                                                    min="-180" max="180" step="0.000001"
                                                                    placeholder="Ej: -76.123456"
                                                                    title="La longitud debe ser entre -180 y 180 con hasta 6 decimales">
                                                            </div>
                                                        </div>
                                                     </div>
                                                     <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="descripcion" class="form-label">Descripcion</label>
                                                                <input type="text" class="form-control" id="descripcion" name="descripcion" value="{{ $cliente->descripcion }}">

                                                            </div>
                                                        </div>
                                                     </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h5 class="mt-1 text-center text-muted"><strong>Datos de contrato</strong></h5>
                                <ul class="list-unstyled">
                                    <li class="text-secondary">
                                        <i class="fas fa-file-contract fa-fw me-1 text-primary"></i>
                                        <strong>Contrato:</strong> #{{ $cliente->contrato->id ?? 'N/A' }}
                                    </li>

                                    <li class="text-secondary">
                                        <i class="fas fa-dollar-sign fa-fw me-1 text-success"></i>
                                        <strong>Precio:</strong> 
                                        @if(isset($cliente->contrato->precio))
                                            ${{ number_format($cliente->contrato->precio, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </li>

                                    <li>
                                        @php
                                            $estado = strtolower($cliente->contrato->estado ?? '');
                                            $estados = [
                                                'activo' => ['icon' => 'fas fa-check-circle', 'color' => 'text-success'],
                                                'suspendido' => ['icon' => 'fas fa-exclamation-circle', 'color' => 'text-warning'],
                                                'cancelado' => ['icon' => 'fas fa-times-circle', 'color' => 'text-danger'],
                                            ];
                                            $icon = $estados[$estado]['icon'] ?? 'fas fa-question-circle';
                                            $color = $estados[$estado]['color'] ?? 'text-muted';
                                            $estadoTexto = ucfirst($estado ?: 'N/A');
                                        @endphp
                                        <i class="{{ $icon }} fa-fw me-1 {{ $color }}"></i>
                                        <strong class="{{ $color }}">Estado:</strong> 
                                        <span class="{{ $color }}">{{ $estadoTexto }}</span>
                                    </li>
                                </ul>


                                <hr class="my-4 border-opacity-25">
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header bg-white border-0 py-3">
                                        <h5 class="card-title mb-0 text-center">
                                            <i class="fas fa-network-wired me-2 text-primary"></i>
                                            <span class="text-gradient">Datos de Red</span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            @if ($cliente->ip == null)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <i class="fas fa-server me-2 text-muted"></i>
                                                        <strong>Nodo:</strong>
                                                    </span>
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">No disponible</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <i class="fas fa-globe me-2 text-muted"></i>
                                                        <strong>IP:</strong>
                                                    </span>
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">No disponible</span>
                                                </li>
                                            @else
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <i class="fas fa-server me-2 text-primary"></i>
                                                        <strong>Nodo:</strong>
                                                    </span>
                                                    <span class="badge bg-primary bg-opacity-10 text-dark">{{ $cliente->contrato->plan->nodo->nombre }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <i class="fas fa-globe me-2 text-primary"></i>
                                                        <strong>IP:</strong>
                                                    </span>
                                                    @php
                                                        $ip = $cliente->ip;
                                                        $urlPrivada = "http://{$ip}:8080";
                                                    @endphp

                                                    <a 
                                                        href="{{ $urlPrivada }}" 
                                                        target="_blank"
                                                        class="badge bg-primary bg-opacity-10 text-dark font-monospace text-decoration-none"
                                                        title="Abrir {{ $urlPrivada }}">
                                                        {{ $ip }}
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>

                                        @if ($cliente->ip)
                                            <div class="d-grid mt-3">
                                                <a href="{{ route('clientes.graficas', ['id' => $cliente->id]) }}" 
                                                class="btn btn-primary btn-hover-gradient">
                                                    <i class="fas fa-chart-line me-2"></i> Ver Consumo en Vivo
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <style>
                                    .text-gradient {
                                        background: linear-gradient(135deg, #3a7bd5 0%, #00d2ff 100%);
                                        -webkit-background-clip: text;
                                        background-clip: text;
                                        -webkit-text-fill-color: transparent;
                                        font-weight: 600;
                                    }
                                    
                                    .btn-hover-gradient:hover {
                                        background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%);
                                        border-color: transparent;
                                        transform: translateY(-1px);
                                    }
                                    
                                    .font-monospace {
                                        font-family: 'Courier New', monospace;
                                        letter-spacing: 0.5px;
                                    }
                                </style>
                            </div>
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-sm-4">
                                    @php
                                        // Definir clases según el estado
                                        $colorClass = [
                                            'activo' => 'border-success text-success',
                                            'suspendido' => 'border-warning text-warning',
                                            'cortado' => 'border-danger text-danger'
                                        ];
                                        
                                        $estado = strtolower($cliente->estado); // Convertir a minúsculas por seguridad
                                        $classes = $colorClass[$estado] ?? 'border-secondary text-secondary'; // Asignar clases de borde y texto
                                    @endphp
                                
                                    <div class="info-box bg-light border {{ explode(' ', $classes)[0] }}">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-center text-muted">Estado Actual</span>
                                            <span class="info-box-number text-center {{ explode(' ', $classes)[1] }} mb-0">
                                                {{ ucfirst($cliente->estado) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                    <div class="col-sm-4">
                                    <div class="info-box bg-light border border-info">
                                            <div class="info-box-content">
                                                <span class="info-box-text text-center text-muted">Plan actual</span>
                                                @if ($cliente->contrato==null)
                                                    <span class="info-box-number text-center text-muted mb-0">Sin contrato asignado</span>

                                                @else
                                                    <span class="info-box-number text-center text-muted mb-0">{{ $cliente->contrato->plan->nombre}}</span>
            
                                                @endif
                                            </div>
                                    </div>
                                    </div>
                                    <div class="col-sm-4">
                                    <div class="info-box bg-light border border-success">
                                            <div class="info-box-content">
                                                <span class="info-box-text text-center text-muted">Tickets Abiertos</span>
                                                <span class="info-box-number text-center text-muted mb-0">{{$totalTicketsAbiertos}}</span>
                                            </div>
                                    </div>
                                    </div>
                                </div>
                                <hr>
                                <h4><strong>Crear Ticket</strong></h4>
                                @livewire('crear-ticket', ['cliente_id' => $cliente->id])
                            </div>
                            
                        </div>
                    </div>
                    {{-- Tab equipos asigandos --}}
                    <div class="tab-pane fade" id="equipos" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="text-primary mb-0">
                                <i class="fas fa-network-wired me-2"></i>Equipos Asignados
                            </h3>
                            <span class="badge bg-primary rounded-pill">
                                {{ $inventarios->count() }} equipos
                            </span>
                        </div>
                    
                        @if($inventarios->isEmpty())
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> No hay equipos asignados actualmente.
                            </div>
                        @else
                            <div class="row g-4">
                                @foreach ($inventarios as $inventario)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 shadow-sm border-start border-3 ">
                                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                <h5 class="card-title mb-0 text-truncate">
                                                    <i class="fas fa-desktop me-2 text-primary"></i>
                                                    {{ $inventario->modelo->nombre }}
                                                </h5>
                                                <span class="badge bg-primary rounded-pill">
                                                    {{ $loop->iteration }}
                                                </span>
                                            </div>
                                            
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="badge bg-secondary me-2">
                                                            <i class="fas fa-ethernet"></i>
                                                        </span>
                                                        <div>
                                                            <small class="text-muted d-block">MAC Address</small>
                                                            <span class="fw-bold">{{ $inventario->mac }}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-flex align-items-center mb-3">
                                                        <span class="badge bg-secondary me-2">
                                                            <i class="fas fa-calendar-alt"></i>
                                                        </span>
                                                        <div>
                                                            <small class="text-muted d-block">Fecha de Asignación</small>
                                                            <span class="fw-bold">{{ $inventario->fecha }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="text-center py-3">
                                                    @if (!empty($inventario->modelo->foto) && file_exists(public_path('storage/' . $inventario->modelo->foto)))
                                                        <img src="{{ asset('storage/' . $inventario->modelo->foto) }}" 
                                                            alt="Foto del modelo" 
                                                            class="img-fluid rounded shadow" 
                                                            style="max-height: 120px;">
                                                    @else
                                                        <div class="py-3 bg-light rounded">
                                                            <i class="fas fa-image fa-2x text-muted mb-2"></i>
                                                            <p class="small text-muted mb-0">Imagen no disponible</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="card-footer bg-transparent">
                                                <a href="{{ route('equipos.show', $inventario->id) }}" 
                                                class="btn btn-sm btn-outline-primary w-100"
                                                target="_blank"
                                                rel="noopener noreferrer">
                                                    <i class="fas fa-eye me-1"></i> Ver Detalles
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif                    
                        
                    
                    </div>

                    <!-- Cortar o suspender cliente -->
                    <div class="tab-pane fade" id="nueva-pestana" role="tabpanel">
                        @livewire('actualizar-estado-cliente', ['cliente' => $cliente])
                    </div>
                        {{-- Tab modificar plan --}}
                    <div class="tab-pane fade" id="modificar-plan" role="tabpanel">

                        @livewire('editar-plan-cliente', ['cliente' => $cliente])

                    </div>
                        {{-- Tab cambio de nodo  --}}
                    <div class="tab-pane fade" id="modificar-nodo" role="tabpanel">
                        @livewire('editar-nodo-cliente', ['cliente' => $cliente])
                    </div>
                    <div class="tab-pane fade" id="facturas" role="tabpanel">
                        @livewire('facturacion.detalle-factura', ['cliente' => $cliente]) 
                    </div>
                    {{-- <div class="tab-pane fade" id="grafica" role="tabpanel">
                        @livewire('clientes.graficas-consumo-cliente', ['cliente' => $cliente]) 
                    </div> --}}
                    
                </div>
            </div>
        </div>
         {{-- SECCION DE TicketS Creados --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Historial de Ticket</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" style="display: block;">
                <div class="row">
                    
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <div class="table-responsive">
                            <table id="ticketsTable" class="table table-hover" style="width:100%">
                            <thead>
                                <tr>
                                <th>ID</th>
                                <th>Tipo de reporte</th>
                                <th class="situacion-column">Situacion</th>
                                <th>Fecha de creacion</th>
                                <th>Fecha de cierre</th>
                                <th>Estado/Solución</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tickets as $ticket)
                                <tr>
                                <td>{{$ticket->id}}</td>
                                <td>{{$ticket->tipo_reporte}}</td>
                                <td class="situacion-cell">{{$ticket->situacion}}</td>
                                <td>{{$ticket->created_at->format('d/m/Y H:i')}}</td>
                                <td>
                                    @if($ticket->fecha_cierre)
                                    {{ \Carbon\Carbon::parse($ticket->fecha_cierre)->format('d/m/Y H:i') }}
                                    @else
                                    N/A
                                    @endif
                                <td>
                                    @if ($ticket->estado == 'abierto')
                                        <span class="badge bg-warning">{{ $ticket->estado }}</span>
                                    @else
                                        @if ($ticket->solucion == 'Se agendo visita')
                                            <a href="{{ route('visitas.show', $ticket->visita->id) }}" 
                                            class="text-success"
                                            title="Ver visita">
                                                {{ $ticket->solucion }}
                                            </a>

                                        @else
                                            <span class="text-success">{{ $ticket->solucion }}</span>
                                        @endif
                                    @endif
                                </td>
                                </tr>
                                @endforeach
                            </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->
                
                </div>
            </div>
            <!-- /.card-body -->
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
 <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@stop

@section('js')

   @livewireScripts
   
   
    <!-- jQuery (requerido por Toastr) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Agregar los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    @stack('scripts')
    <!-- JavaScript -->
    {{-- <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script> --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#ticketsTable').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                columnDefs: [
                    { 
                        targets: [2], // Columna Situación
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return '<div class="situacion-content">' + data + '</div>';
                            }
                            return data;
                        }
                    },
                    { 
                        targets: '_all',
                        className: 'dt-head-center dt-body-center'
                    }
                ],
                initComplete: function() {
                    // Ajustar altura de filas después de cargar
                    this.api().columns.adjust().responsive.recalc();
                }
            });

            // Redimensionar cuando cambia el tamaño de la ventana
            $(window).on('resize', function() {
                table.columns.adjust().responsive.recalc();
            });
        });
    </script>
    <script>
        
        // 1. Primero verificamos si Livewire está cargado
        function initializeLivewireEvents() {
            // Configuración de Toastr
            toastr.options = {
                "positionClass": "toast-top-right",
                "progressBar": true,
                "timeOut": 5000,
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "preventDuplicates": true
            };

            // Eventos Livewire
            window.Livewire.on('notify', (data) => {
                toastr[data.type](data.message, data.title || 'Mensaje del sistema');
            });
        }

        // 2. Esperamos a que todo esté listo
        if (window.Livewire) {
            initializeLivewireEvents();
        } else {
            document.addEventListener('livewire:load', function () {
                initializeLivewireEvents();
            });
        }

        // 3. Manejador alternativo por si falla lo anterior
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initializeLivewireEvents, 1000);
        });
    </script>
    <!-- Logo en sidebar-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var logoItem = document.querySelector('li#sidebar-logo-item');
            if (logoItem) {
                logoItem.innerHTML = '<img src="{{ asset('img/logo.png') }}" style="max-width:120px;max-height:90px; margin-left:70px; margin-top:30px;" alt="Logo" />';
            }
        });
    </script>

@stop
