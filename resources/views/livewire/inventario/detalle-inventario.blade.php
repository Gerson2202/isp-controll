<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <!-- Header Mejorado -->
            <div class="card-header bg-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box-seam fs-3 me-3"></i>
                        <div>
                            <h4 class="mb-1 fw-bold">{{ $titulo }}</h4>
                            <small class="opacity-75">Gestión completa de inventario</small>
                        </div>
                    </div>
                    <a href="{{ url('/inventario/dashboard') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left me-2"></i>Volver al Dashboard
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Estadísticas Mejoradas -->
                <div class="row mb-4">
                    <div class="col-xl-4 col-md-4 col-sm-6 mb-3">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="bi bi-box fs-1 text-primary"></i>
                                </div>
                                <h3 class="text-primary fw-bold">{{ $totalProductos }}</h3>
                                <p class="text-muted mb-1 fw-semibold">Productos Diferentes</p>
                                <small class="text-muted">Variedad en inventario</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 col-sm-6 mb-3">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="bi bi-layers fs-1 text-success"></i>
                                </div>
                                <h3 class="text-success fw-bold">{{ $totalItems }}</h3>
                                <p class="text-muted mb-1 fw-semibold">Total de Items</p>
                                <small class="text-muted">Unidades en stock</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 col-sm-6 mb-3">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="bi bi-hdd-network fs-1 text-info"></i>
                                </div>
                                <h3 class="text-info fw-bold">{{ $totalEquipos }}</h3>
                                <p class="text-muted mb-1 fw-semibold">Total de Equipos</p>
                                <small class="text-muted">Equipos registrados</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabs -->
                <ul class="nav nav-tabs nav-justified mb-4" id="inventarioTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="consumibles-tab" data-bs-toggle="tab"
                            data-bs-target="#consumibles" type="button" role="tab" aria-controls="consumibles"
                            aria-selected="true">
                            <i class="bi bi-list-ul me-2"></i>Consumibles
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="equipos-tab" data-bs-toggle="tab" data-bs-target="#equipos"
                            type="button" role="tab" aria-controls="equipos" aria-selected="false">
                            <i class="bi bi-hdd-network me-2"></i>Equipos
                        </button>
                    </li>
                </ul>

                <!-- Contenido de los Tabs -->
                <div class="tab-content" id="inventarioTabsContent">

                    <!-- TAB: Consumibles -->
                    <div class="tab-pane fade show active" id="consumibles" role="tabpanel"
                        aria-labelledby="consumibles-tab">

                        <!-- Buscador con JavaScript -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">
                                            <i class="bi bi-search text-primary me-2"></i>Buscar Consumibles
                                        </h6>
                                        <input type="text" id="searchConsumible" class="form-control"
                                            placeholder="Buscar por nombre de consumible..."
                                            onkeyup="filterConsumibles()">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de consumibles -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="tableConsumibles">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">ID</th>
                                                <th>Consumible</th>
                                                <th class="text-center">Cantidad</th>
                                                <th class="text-end pe-4">Última Actualización</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($stocks as $stock)
                                                <tr>
                                                    <td class="ps-4 fw-semibold text-muted">#{{ $stock->id }}</td>
                                                    <td>
                                                        @if ($stock->consumible)
                                                            {{ $stock->consumible->nombre }}
                                                        @else
                                                            <span class="text-danger">Consumible no encontrado</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge bg-primary fs-6">{{ $stock->cantidad }}</span>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        <small class="text-muted">
                                                            {{ $stock->updated_at->format('d/m/Y H:i') }}
                                                        </small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: Equipos -->
                    <div class="tab-pane fade" id="equipos" role="tabpanel" aria-labelledby="equipos-tab">

                        <!-- Buscador con JavaScript -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">
                                            <i class="bi bi-search text-primary me-2"></i>Buscar Equipos
                                        </h6>
                                        <input type="text" id="searchEquipo" class="form-control"
                                            placeholder="Buscar por modelo o MAC..." onkeyup="filterEquipos()">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de equipos -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="tableEquipos">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">Foto</th>
                                                <th>Modelo</th>
                                                <th>MAC</th>
                                                <th class="text-end pe-4">Fecha de Registro</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($equipos as $equipo)
                                                <tr>
                                                    <td class="ps-4">
                                                        @if ($equipo->modelo && $equipo->modelo->foto)
                                                            <img src="{{ asset('storage/' . $equipo->modelo->foto) }}"
                                                                alt="{{ $equipo->modelo->nombre }}" class="rounded"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                                                style="width: 50px; height: 50px;">
                                                                <i class="bi bi-hdd text-white"></i>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $equipo->modelo->nombre ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        <code class="text-dark">{{ $equipo->mac ?? 'N/A' }}</code>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        @if ($equipo->movimientos->count() > 0)
                                                            <small class="text-muted">
                                                                {{ $equipo->movimientos->first()->created_at->format('d/m/Y H:i') }}
                                                            </small>
                                                        @else
                                                            <span class="text-muted">Sin registro</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Filtro para consumibles
    function filterConsumibles() {
        const input = document.getElementById('searchConsumible');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('tableConsumibles');
        const tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) {
            const td = tr[i].getElementsByTagName('td')[1];
            if (td) {
                const txtValue = td.textContent || td.innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = '';
                } else {
                    tr[i].style.display = 'none';
                }
            }
        }
    }

    // Filtro para equipos
    function filterEquipos() {
        const input = document.getElementById('searchEquipo');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('tableEquipos');
        const tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) {
            const tdModelo = tr[i].getElementsByTagName('td')[1];
            const tdMAC = tr[i].getElementsByTagName('td')[2];

            let show = false;
            if (tdModelo) {
                const txtValueModelo = tdModelo.textContent || tdModelo.innerText;
                if (txtValueModelo.toLowerCase().indexOf(filter) > -1) {
                    show = true;
                }
            }
            if (tdMAC && !show) {
                const txtValueMAC = tdMAC.textContent || tdMAC.innerText;
                if (txtValueMAC.toLowerCase().indexOf(filter) > -1) {
                    show = true;
                }
            }

            tr[i].style.display = show ? '' : 'none';
        }
    }
</script>
