<div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            <i class="fas fa-boxes me-2"></i>Inventario de la Bodega: {{ $bodega->nombre }}
        </h4>
        
    </div>
    
    <div class="card-body p-4">
        <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" wire:model.live="search" class="form-control" 
                        placeholder="Buscar consumible por nombre...">
                </div>
            </div>
         <div class="table-responsive rounded-3 border" style="max-height: 600px; overflow-y: auto;">
                <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Consumible</th>
                        <th>Unidad</th>
                        <th>Cantidad</th>
                        <th>Fecha de Ingreso</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $currentConsumible = null;
                        $total = 0;
                    @endphp

                    @foreach($stocks as $stock)
                        @if($currentConsumible !== $stock->consumible_id)
                            @if($currentConsumible !== null)
                                <tr class="table-secondary fw-bold">
                                    <td colspan="2">Total {{ $stocks->where('consumible_id', $currentConsumible)->first()->consumible->nombre }}</td>
                                    <td>{{ $total }}</td>
                                    <td></td>
                                </tr>
                            @endif
                            @php
                                $currentConsumible = $stock->consumible_id;
                                $total = 0;
                            @endphp
                        @endif

                        <tr>
                            <td>{{ $stock->consumible->nombre }}</td>
                            <td>{{ $stock->consumible->unidad }}</td>
                            <td>{{ $stock->cantidad }}</td>
                            <td>{{ \Carbon\Carbon::parse($stock->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>

                        @php
                            $total += $stock->cantidad;
                        @endphp
                    @endforeach

                    @if($currentConsumible !== null)
                        <tr class="table-secondary fw-bold">
                            <td colspan="2">Total {{ $stocks->where('consumible_id', $currentConsumible)->first()->consumible->nombre }}</td>
                            <td>{{ $total }}</td>
                            <td></td>
                        </tr>
                    @endif

                    @if($stocks->isEmpty())
                        <tr>
                            <td colspan="4" class="text-center text-muted">No hay consumibles registrados en esta bodega.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
