<div class="p-3 bg-white rounded-3 shadow-sm">

    <h5 class="fw-bold mb-4 text-success d-flex align-items-center">
        <i class="fas fa-boxes me-2"></i> Consumibles Asignados y en Bodegas
    </h5>

    <!-- 🔎 Buscador -->
    <input type="text"
           wire:model.live.debounce.500ms="buscar"
           class="form-control mb-4 shadow-sm"
           placeholder="Buscar consumible por nombre...">

    <!-- 👤 Consumibles del usuario -->
    <div class="mb-5">
        <h6 class="fw-bold text-primary mb-3">
            👤 Tus consumibles personales
        </h6>

        @if ($consumiblesUsuario->count())
            <div class="table-responsive shadow-sm"
                 style="max-height: 320px; overflow-y: auto; border-radius: 10px;">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-primary sticky-top">
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th class="text-center">Cantidad</th>
                            <th>Unidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($consumiblesUsuario as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->consumible->nombre ?? 'Sin nombre' }}</td>
                                <td>{{ $item->consumible->descripcion ?? '-' }}</td>
                                <td class="text-center fw-bold">{{ $item->cantidad }}</td>
                                <td>{{ $item->consumible->unidad ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-center">
                {{ $consumiblesUsuario->onEachSide(1)->links('vendor.livewire.simple-pagination') }}
            </div>
        @else
            <div class="alert alert-info text-center mt-3">
                No tienes consumibles asignados.
            </div>
        @endif
    </div>

    <!-- 🏬 Consumibles por bodega -->
    @foreach ($bodegas as $bodega)
        @php
            $consumibles = $bodega->consumiblesStock()
                ->with('consumible')
                ->whereHas('consumible', function ($q) use ($buscar) {
                    $q->where('nombre', 'like', "%{$buscar}%");
                })
                ->get();
        @endphp

        <div class="mb-5">
            <h6 class="fw-bold text-success mb-3">
                🏬 {{ $bodega->nombre }}
            </h6>

            @if ($consumibles->count())
                <div class="table-responsive shadow-sm"
                     style="max-height: 320px; overflow-y: auto; border-radius: 10px;">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-success sticky-top">
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th class="text-center">Cantidad</th>
                                <th>Unidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($consumibles as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->consumible->nombre ?? 'Sin nombre' }}</td>
                                    <td>{{ $item->consumible->descripcion ?? '-' }}</td>
                                    <td class="text-center fw-bold">{{ $item->cantidad }}</td>
                                    <td>{{ $item->consumible->unidad ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-light text-center border mt-3">
                    No hay consumibles en esta bodega.
                </div>
            @endif
        </div>
    @endforeach

</div>
    