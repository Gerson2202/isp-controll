
    <div class="row justify-content-start">
        <!-- Card para el formulario -->
        <div class="col-md-6 ">
            <div class="card">
                

                <div class="card-body">
                    <!-- Mensaje de éxito -->
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif
                        <!-- Información del Cliente -->
                        @if ($cliente)
                            <div class="mb-3">
                                <p><strong>Nombre del Cliente:</strong> {{ $cliente->nombre }}</p>
                                <p><strong>Contrato:</strong> {{ $contrato ? $contrato->id: 'No disponible' }}</p>
                                <p><strong>Plan:</strong> {{ $plan ? $plan->nombre : 'No disponible' }}</p>
                                <p><strong>nodo:</strong> {{ $plan ? $plan->nodo->nombre : 'No disponible' }}</p>
                            </div>
                        @else
                            <p>No se encontró información del cliente.</p>
                        @endif
                    <!-- Formulario -->
                    <form wire:submit.prevent="asignarIp">
                        <!-- Campo IP -->
                        <div class="mb-3">
                            <label for="ip" class="form-label">Asignar IP</label>
                            <input type="text" wire:model="ip" id="ip" class="form-control" placeholder="Ingrese la IP" required>
                            @error('ip') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Asignar IP</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

