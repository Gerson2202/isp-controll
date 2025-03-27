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
      </div>
      <div class="card-body" style="display: block;">
      <div class="row">
         <div class="col-12 col-md-12 col-lg-8 order-2 order-md-2">
            <div class="row">
               <div class="col-12 col-sm-4">
                  @if ($cliente->estado=='suspendido')
                  <div class="info-box bg-light border border-danger">
                     <div class="info-box-content">
                     <span class="info-box-text text-center text-muted">Estado Actual</span>
                     <span class="info-box-number text-center text-muted mb-0">Suspedido</span>
                     </div>
                  </div>
                  @else
                     <div class="info-box bg-light border border-success">
                        <div class="info-box-content">
                        <span class="info-box-text text-center text-muted">Estado Actual</span>
                        <span class="info-box-number text-center text-muted mb-0">Activo</span>
                        </div>
                     </div>
                  @endif
                  
               </div>
               <div class="col-12 col-sm-4">
                  <div class="info-box bg-light border border-info">
                     <div class="info-box-content">
                     <span class="info-box-text text-center text-muted">Plan actual</span>
                     <span class="info-box-number text-center text-muted mb-0">10 Megas</span>
                     </div>
                  </div>
               </div>
               <div class="col-12 col-sm-4">
                  <div class="info-box bg-light border border-success">
                     <div class="info-box-content">
                     <span class="info-box-text text-center text-muted">Tickets Abiertos</span>
                     <span class="info-box-number text-center text-muted mb-0">{{$totalTicketsAbiertos}}</span>
                     </div>
                  </div>
               </div>
            </div>
            <hr>
            <div class="row">
            <div class="col-12">
               <h4><strong>Crear Ticket</strong></h4>
                  <div class="post">
                  <div class="user-block">
                     {{-- <img class="img-circle img-bordered-sm" src="../../dist/img/user1-128x128.jpg" alt="user image"> --}}
                     {{-- <span class="username">
                        <a href="#">Crear Ticket</a>
                     </span> --}}
                     {{-- <span class="description">Shared publicly - 7:45 PM today</span> --}}
                  </div>
                  {{-- Componente livwriew para crear-ticket --}}
                  @livewire('crear-ticket', ['cliente_id' => $cliente->id])
                  {{-- FIN Componente livwriew para crear-ticket --}}
                  </div>

                  {{-- <div class="post clearfix">
                     <div class="user-block">
                        <img class="img-circle img-bordered-sm" src="../../dist/img/user7-128x128.jpg" alt="User Image">
                        <span class="username">
                           <a href="#">Sarah Ross</a>
                        </span>
                        <span class="description">Sent you a message - 3 days ago</span>
                     </div>
                     <!-- /.user-block -->
                     <p>
                        Lorem ipsum represents a long-held tradition for designers,
                        typographers and the like. Some people hate it and argue for
                        its demise, but others ignore.
                     </p>
                     <p>
                        <a href="#" class="link-black text-sm"><i class="fas fa-link mr-1"></i> Demo File 2</a>
                     </p>
                  </div> --}}

                  <div class="post">
                  <div class="user-block">
                     <img class="img-circle img-bordered-sm" src="../../dist/img/user1-128x128.jpg" alt="user image">
                     <span class="username">
                        <a href="#">Jonathan Burke Jr.</a>
                     </span>
                     <span class="description">Shared publicly - 5 days ago</span>
                  </div>
                  <!-- /.user-block -->
                  <p>
                     Lorem ipsum represents a long-held tradition for designers,
                     typographers and the like. Some people hate it and argue for
                     its demise, but others ignore.
                  </p>

                  <p>
                     <a href="#" class="link-black text-sm"><i class="fas fa-link mr-1"></i> Demo File 1 v1</a>
                  </p>
                  </div>
            </div>
            </div>
         </div>
         <div class="col-12 col-md-12 col-lg-4 order-1 order-md-1">
            <h3 class="text-primary"><i class="fas fa-paint-brush"></i>{{$cliente->nombre}}</h3>
            <p class="text-muted">{{$cliente->descripcion}}</p>
            <br>
            <div class="text-muted">
            <p class="text-sm">Client Company
               <b class="d-block">Deveint Inc</b>
            </p>
            <p class="text-sm">Project Leader
               <b class="d-block">Tony Chicken</b>
            </p>
            </div>

            <h5 class="mt-5 text-muted"><strong>Datos personales</strong> <h5>
            <ul class="list-unstyled">
            <li  class="btn-link text-secondary" ><i class="far fa-fw fa-file-word"></i><strong>cedula:</strong>{{$cliente->cedula}}  </li>
            <li  class="btn-link text-secondary" ><i class="far fa-fw fa-file-word"></i><strong>Telefono: </strong>{{$cliente->telefono}}  </li>
            <li  class="btn-link text-secondary" ><i class="far fa-fw fa-envelope"></i><strong>Correo: </strong>{{$cliente->correo}}</li>
            <li  class="btn-link text-secondary" ><i class="far fa-fw fa-image"></i><strong>Direccion: </strong>{{$cliente->direccion}}</li>

           
            <li>
               <a href="" class="btn-link text-secondary"><i class="far fa-fw fa-file-word"></i> Contract-10_12_2014.docx</a>
            </li>
            </ul>
            <hr>
            <h5 class="mt-5 text-success text-center"><strong>Equipos Asignados</strong> <h5>
               <br>
               @foreach ($inventarios as $inventario)
                  <ul class="list-unstyled">
                     <li  class="text-secondary" ><strong>Modelo:</strong>{{$inventario->modelo->nombre}}  </li>
                     <li  class="text-secondary" ><strong>Mac: </strong>{{$inventario->mac}}  </li>
                     @if (!empty($inventario->modelo->foto) && file_exists(public_path('storage/' . $inventario->modelo->foto)))
                        <div class="text mt-3">
                            <img src="{{ asset('storage/' . $inventario->modelo->foto) }}" alt="Foto del modelo" class="img-thumbnail" style="max-width: 150px;">
                        </div>
                    @else
                        <p class="text-muted">No hay imagen disponible.</p>
                    @endif
                  </ul>
                  <hr>
               @endforeach
               
               
         </div>
      </div>
      </div>
      <!-- /.card-body -->
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
            
            <div class="card-header">
               <h3 class="card-title">Histórico PQR Cliente </h3>

               <div class="card-tools">
                 <div class="input-group input-group-sm" style="width: 150px;">
                   <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                   <div class="input-group-append">
                     <button type="submit" class="btn btn-default">
                       <i class="fas fa-search"></i>
                     </button>
                   </div>
                 </div>
               </div>
             </div>
             <!-- /.card-header -->
             <div class="card-body table-responsive p-0">
               <table class="table table-hover text-nowrap">
                 <thead>
                   <tr>
                     <th>ID</th>
                     <th>Tipo de reporte</th>
                     <th>Situacion</th>
                     <th>Fecha de creacion</th>
                     <th>Fecha de cierre</th>
                     <th>Solucion</th>
                   </tr>
                 </thead>
                 <tbody>
                  @foreach ($tickets as $ticket)
                  <tr>
                     <td>{{$ticket->id}}</td>
                     <td>{{$ticket->tipo_reporte}}</td>
                     <td>{{$ticket->situacion}}</td>
                     <td>{{$ticket->created_at}}</td>
                     <td>{{$ticket->fecha_cierre}}</td>
                     @if ($ticket->estado== 'abierto')
                      <td>{{$ticket->estado}}</td></tr>
                     @else
                      <td>{{$ticket->solucion}}</td></tr>
                     @endif
                  @endforeach
                   
                  
                 </tbody>
               </table>
             </div>
             <!-- /.card-body -->
           
         </div>
      </div>
      <!-- /.card-body -->
   </div>
    

@stop

@section('css')
 
@stop

@section('js')

   @livewireScripts
    <!-- Agregar los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    
@stop
