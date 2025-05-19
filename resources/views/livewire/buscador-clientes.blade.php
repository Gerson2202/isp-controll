<div class="w-100 p-4 bg-white shadow rounded">
    <!-- Campo de búsqueda -->
    <input 
        type="text" 
        wire:model.live="query" 
        placeholder="Buscar cliente por nombre o ID..." 
        class="form-control mb-3"
    >

    <!-- Tabla de resultados -->
    <div class="table-responsive">
        <table class="table table-hover table-sm">
    <thead>
        <tr class="small">
            <th>Cliente</th>
            <th>Ubicación</th>
            <th>id</th>
            <th class="text-end"><i class="fas fa-cog"></i></th>
        </tr>
    </thead>
    <tbody>
        @foreach($clientes as $cliente)
        <tr>
            <td><strong>{{ Str::limit($cliente->nombre, 20) }}</strong></td>
            <td class="small">{{ Str::limit($cliente->direccion, 25) }}</td>
            <td>
                <span class="badge bg-primary">
                    #{{ $cliente->id ?? '--' }}
                </span>
            </td>
            <td class="text-end">
                <button class="btn btn-xs btn-outline-primary" wire:click="verCliente({{ $cliente->id }})">
                    <i class="fas fa-search"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
    </div>

    
</div>