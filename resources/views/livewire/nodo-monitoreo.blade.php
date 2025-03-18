<div>
    <div>
        <ul>   
            <div class="row">
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Lista de Nodos</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <table class="table table-bordered">                        
                        <thead>
                          <tr>
                            <th style="width: 10px">#</th>
                            <th>Nombre</th>
                            <th>Ver Grafica</th>
                            <th style="width: 40px">Label</th>
                          </tr>
                        </thead>
                        <tbody>
                         @foreach($nodos as $nodo)
                            <tr>
                                <td>{{$nodo->id}}</td>
                                <td>{{$nodo->nombre}}</td>
                                <td>           
                                    <button  wire:click="selectNodo({{ $nodo->id }})" type="button" class="btn btn-block btn-outline-success btn-sm">Ver Nodo</button>                
                                </td>
                                <td><span class="badge bg-danger">55%</span></td>
                            </tr>
                            <tr>
                            @endforeach
                        </tbody>
                      </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                      <ul class="pagination pagination-sm m-0 float-right">
                        <li class="page-item"><a class="page-link" href="#">«</a></li>
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">»</a></li>
                      </ul>
                    </div>
                  </div>
                  <!-- /.card -->
      
                  {{-- <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Condensed Full Width Table</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                      <table class="table table-sm">
                        <thead>
                          <tr>
                            <th style="width: 10px">#</th>
                            <th>Task</th>
                            <th>Progress</th>
                            <th style="width: 40px">Label</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1.</td>
                            <td>Update software</td>
                            <td>
                              <div class="progress progress-xs">
                                <div class="progress-bar progress-bar-danger" style="width: 55%"></div>
                              </div>
                            </td>
                            <td><span class="badge bg-danger">55%</span></td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>Clean database</td>
                            <td>
                              <div class="progress progress-xs">
                                <div class="progress-bar bg-warning" style="width: 70%"></div>
                              </div>
                            </td>
                            <td><span class="badge bg-warning">70%</span></td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>Cron job running</td>
                            <td>
                              <div class="progress progress-xs progress-striped active">
                                <div class="progress-bar bg-primary" style="width: 30%"></div>
                              </div>
                            </td>
                            <td><span class="badge bg-primary">30%</span></td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>Fix and squish bugs</td>
                            <td>
                              <div class="progress progress-xs progress-striped active">
                                <div class="progress-bar bg-success" style="width: 90%"></div>
                              </div>
                            </td>
                            <td><span class="badge bg-success">90%</span></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <!-- /.card-body -->
                  </div> --}}
                  <!-- /.card -->
                </div>
                <!-- /.col -->
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Monitor de nodo</h3>
      
                      <div class="card-tools">
                        <ul class="pagination pagination-sm float-right">
                          <li class="page-item"><a class="page-link" href="#">«</a></li>
                          <li class="page-item"><a class="page-link" href="#">1</a></li>
                          <li class="page-item"><a class="page-link" href="#">2</a></li>
                          <li class="page-item"><a class="page-link" href="#">3</a></li>
                          <li class="page-item"><a class="page-link" href="#">»</a></li>
                        </ul>
                      </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                      <!-- Mostrar interfaces del nodo seleccionado -->
                      @if($selectedNodoId)
                          <div class="mt-8">
                              <h2 class="text-xl font-bold m-4">Interfaces del Nodo {{ $nodoNombre }}</h2>
                  
                              <!-- Botón para cargar interfaces -->
                              <button
                                  wire:click="loadInterfaces"
                                  class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600 m-4 "
                              >
                                  Cargar Interfaces
                              </button>
                  
                              <!-- Mostrar mensajes de error -->
                              @if($errorMessage)
                                  <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                      {{ $errorMessage }}
                                  </div>
                              @endif

                              
                              
                              <!-- Mostrar interfaces y estadísticas -->
                              @if(!empty($interfaces))
                                  <table class="min-w-full bg-white border border-gray-300 m-4">
                                      <thead>
                                          <tr>
                                              <th class="py-2 px-4 border-b">Nombre</th>
                                              <th class="py-2 px-4 border-b">Tipo</th>
                                              <th class="py-2 px-4 border-b">Estado</th>
                                              <th class="py-2 px-4 border-b">Rx (Mbps)</th>
                                              <th class="py-2 px-4 border-b">Tx (Mbps)</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          @foreach($interfaces as $interface)
                                              @php
                                                  // Obtener las estadísticas de la interfaz actual
                                                  $stats = collect($interfaceStats)->firstWhere('name', $interface['name']);
                                              @endphp
                                              <tr>
                                                  <td class="py-2 px-4 border-b ">{{ $interface['name'] }}</td>
                                                  <td class="py-2 px-4 border-b">{{ $interface['type'] }}</td>
                                                  <td class="py-2 px-4 border-b">{{ $interface['running'] ? 'Activo' : 'Inactivo' }}</td>
                                                  <td class="py-2 px-4 border-b  text-blue text-center rounded-start shadow-sm hover:shadow-lg transition">
                                                    <i class="bi bi-arrow-down-circle me-2"></i> {{ $stats['rx'] ?? 'N/A' }}
                                                  </td>
                                                  <td class="py-2 px-4 border-b  text-green text-center rounded-end shadow-sm hover:shadow-lg transition">
                                                    <i class="bi bi-arrow-up-circle me-2"></i> {{ $stats['tx'] ?? 'N/A' }}
                                                  </td>
                                              </tr>
                                          @endforeach
                                      </tbody>
                              {{-- Para que se actualice cada 1seg --}}
                              <div wire:poll.1s="loadInterfaces">
                                <!-- El contenido de la tabla y las estadísticas se actualizará cada 5 segundos -->
                              </div>
                                  </table>
                              @else
                                  <p class="text-gray-600 m-4">No se han cargado interfaces aún.</p>
                              @endif
                          </div>
                      @endif
                  </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
      
                  {{-- <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Striped Full Width Table</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th style="width: 10px">#</th>
                            <th>Task</th>
                            <th>Progress</th>
                            <th style="width: 40px">Label</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1.</td>
                            <td>Update software</td>
                            <td>
                              <div class="progress progress-xs">
                                <div class="progress-bar progress-bar-danger" style="width: 55%"></div>
                              </div>
                            </td>
                            <td><span class="badge bg-danger">55%</span></td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>Clean database</td>
                            <td>
                              <div class="progress progress-xs">
                                <div class="progress-bar bg-warning" style="width: 70%"></div>
                              </div>
                            </td>
                            <td><span class="badge bg-warning">70%</span></td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>Cron job running</td>
                            <td>
                              <div class="progress progress-xs progress-striped active">
                                <div class="progress-bar bg-primary" style="width: 30%"></div>
                              </div>
                            </td>
                            <td><span class="badge bg-primary">30%</span></td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>Fix and squish bugs</td>
                            <td>
                              <div class="progress progress-xs progress-striped active">
                                <div class="progress-bar bg-success" style="width: 90%"></div>
                              </div>
                            </td>
                            <td><span class="badge bg-success">90%</span></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <!-- /.card-body -->
                  </div> --}}
                  <!-- /.card -->
                </div>
                <!-- /.col -->
              </div>
        </ul>
    </div>
</div>

