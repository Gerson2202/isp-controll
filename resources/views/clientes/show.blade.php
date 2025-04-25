@extends('adminlte::page')
@section('title', 'Dashboard') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
   <h1>Informacion del cliente</h1>
   @livewireStyles
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

@stop

@section('content')
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
        </ul>
        
        <!-- Tab panes -->
        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <div class="row">
                    <div class="col-lg-4">
                        <h3 class="text-primary"><i class="fas fa-paint-brush"></i> {{$cliente->nombre}}</h3>
                        <p class="text-muted">{{$cliente->descripcion}}</p>
                        <h5 class="mt-5 text-center text-muted"><strong>Datos personales</strong></h5>
                        <div>
                            <!-- Botón para abrir el modal -->
                            <button class="btn btn-warning btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#editClienteModal">
                                <i class="fas fa-edit me-1"></i> Editar Información
                            </button>
                        
                            <ul class="list-unstyled">
                                <li class="btn-link text-secondary"><i class="far fa-fw fa-file-word"></i><strong> Cedula:</strong> {{$cliente->cedula}}</li>
                                <li class="btn-link text-secondary"><i class="far fa-fw fa-file-word"></i><strong> Telefono:</strong> {{$cliente->telefono}}</li>
                                <li class="btn-link text-secondary"><i class="far fa-fw fa-envelope"></i><strong> Correo:</strong> {{$cliente->correo}}</li>
                                <li class="btn-link text-secondary"><i class="far fa-fw fa-image"></i><strong> Direccion:</strong> {{$cliente->direccion}}</li>
                                <li class="btn-link text-secondary">
                                    <i class="fas fa-map-marker-alt me-2"></i><strong> Coordenada:</strong>
                                    @if($cliente->latitud && $cliente->longitud)
                                    <a href="https://www.google.com/maps?q={{ $cliente->latitud }},{{ $cliente->longitud }}" 
                                    target="_blank" 
                                    class="btn btn-sm btn-outline-info ms-2" 
                                    title="Ver en Google Maps">
                                        <i class="fas fa-map-marked-alt"></i> Ver en mapa
                                    </a>
                                @endif
                                </li>
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
                                                            <input type="text" class="form-control" id="direccion" name="direccion" value="{{ $cliente->direccion }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="latitud" class="form-label">Latitud</label>
                                                            <input type="text" class="form-control" id="latitud" name="latitud" value="{{ $cliente->latitud }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="longitud" class="form-label">Longitud</label>
                                                            <input type="text" class="form-control" id="longitud" name="longitud" value="{{ $cliente->longitud }}">
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
                        <h5 class="mt-5 text-center text-muted"><strong>Datos De red </strong></h5>
                        <ul class="list-unstyled">
                            @if ($cliente->ip == null)
                            <li class=" text-secondary"><i class="far fa-fw fa-file-word"></i><strong> Nodo:</strong>No disponible</li>
                            <li class=" text-secondary"><i class="far fa-fw fa-file-word"></i><strong> Ip:</strong>No disponible</li>

                            @else
                            <li class=" text-secondary"><i class="far fa-fw fa-file-word"></i><strong> Nodo:</strong> cucuta</li>
                            <li class=" text-secondary"><i class="far fa-fw fa-file-word"></i><strong> Ip:</strong> {{$cliente->ip}}</li>

                            @endif
                            
                        </ul>
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
                <h5 class="mt-3 text-success text-center"><strong>Equipos Asignados</strong></h5>
                <br>
                <div class="row">
                    @foreach ($inventarios as $inventario)
                        <div class="col-md-6"> <!-- 2 columnas por fila en pantallas medianas y grandes -->
                            <div class="card border-info mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><strong>Modelo:</strong> {{$inventario->modelo->nombre}}</h5>
                                    <p class="card-text"><strong>Mac:</strong> {{$inventario->mac}}</p>
                                    @if (!empty($inventario->modelo->foto) && file_exists(public_path('storage/' . $inventario->modelo->foto)))
                                        <div class="text-center">
                                            <img src="{{ asset('storage/' . $inventario->modelo->foto) }}" alt="Foto del modelo" class="img-thumbnail" style="max-width: 150px;">
                                        </div>
                                    @else
                                        <p class="text-muted">No hay imagen disponible.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
            
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
                          </td>                          <td>
                            @if ($ticket->estado == 'abierto')
                              <span class="badge bg-warning">{{$ticket->estado}}</span>
                            @else
                              <span class="text-success">{{$ticket->solucion}}</span>
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
    

@stop

@section('css')
 <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@stop

@section('js')

   @livewireScripts
    <!-- Agregar los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
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
    
    @stop
