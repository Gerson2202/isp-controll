<div class="d-flex justify-content-start"> <!-- Fondo claro -->
    <div class="w-50 p-4 bg-white shadow rounded"> <!-- Fondo blanco, sombra y bordes redondeados -->
        <!-- Campo de búsqueda -->
        <input 
            type="text" 
            wire:model.live="query" 
            placeholder="Buscar cliente..." 
            class="form-control mb-3"
        >

        <!-- Lista de resultados -->
        <ul class="list-group">
            @foreach ($clientes as $cliente)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $cliente->nombre }}</span>
                    <button 
                        class="btn btn-sm btn-primary"
                        wire:click="verCliente({{ $cliente->id }})"
                    >
                        Ver Detalles
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>