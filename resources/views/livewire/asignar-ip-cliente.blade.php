<div class="row">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Asignación de IP</h5>
            </div>
            
            <div class="card-body">
                <!-- Mensajes flash -->
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Información del Cliente -->
                @if ($cliente)
                    <div class="mb-4 p-3 border rounded bg-light">
                        <h6 class="fw-bold mb-3">Información del Cliente</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nombre:</strong> {{ $cliente->nombre }}</p>
                                <p><strong>Documento:</strong> {{ $cliente->cedula }}</p>
                                <p><strong>Direccion:</strong> {{ $cliente->direccion }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Contrato:</strong> {{ $contrato ? '#' . $contrato->id : 'No disponible' }}</p>
                                <p><strong>Plan:</strong> {{ $plan ? $plan->nombre : 'No disponible' }}</p>
                                <p><strong>Nodo:</strong> {{ $plan->nodo->nombre ?? 'No disponible' }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        No se encontró información del cliente.
                    </div>
                @endif

                <!-- Sección de IPs usadas en el nodo -->
                {{-- @if($pool_id && !empty($ipsUsadas))
                    <div class="mb-3 p-3 border rounded bg-light">
                        <h6 class="fw-bold mb-2">IPs en uso en este nodo:</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($ipsUsadas as $ip)
                                <span class="badge bg-danger text-white me-1 mb-1">{{ $ip }}</span>
                            @endforeach
                        </div>
                        <small class="text-muted">Estas IPs no están disponibles para asignación</small>
                    </div>
                @endif --}}

                <!-- Formulario de asignación -->
                <form wire:submit.prevent="asignarIp">
                    <!-- Selector de Pool -->
                    <div class="mb-3">
                        <label for="pool_id" class="form-label">Seleccionar Pool</label>
                        <select wire:model.live="pool_id" 
                                id="pool_id" 
                                class="form-select @error('pool_id') is-invalid @enderror"
                                required>
                            <option value="">Seleccione un pool...</option>
                            @foreach($pools as $pool)
                                <option value="{{ $pool->id }}">
                                    {{ $pool->nombre }} ({{ $pool->start_ip }} - {{ $pool->end_ip }})
                                </option>
                            @endforeach
                        </select>
                        @error('pool_id') 
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($pool_id)
                            <small class="text-muted">
                                Pool seleccionado: {{ $pools->firstWhere('id', $pool_id)->descripcion }}
                            </small>
                        @endif
                    </div>

                    <!-- Selector de IP -->
                    <div class="mb-3">
                        <label for="ip" class="form-label">Seleccionar Dirección IP</label>
                        <select wire:model.live="ip" 
                                id="ip" 
                                class="form-select @error('ip') is-invalid @enderror"
                                @if(!$pool_id) disabled @endif
                                required>
                            <option value="">Seleccione una IP...</option>
                            @if($pool_id)
                                @foreach($availableIps as $ipAddress)
                                    <option value="{{ $ipAddress }}"
                                        @if(in_array($ipAddress, $ipsUsadas))
                                            disabled
                                            class="text-muted"
                                        @endif>
                                        {{ $ipAddress }}
                                        @if(in_array($ipAddress, $ipsUsadas)) (En uso) @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('ip') 
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($pool_id)
                            <small class="text-muted">
                                Total IPs disponibles: {{ count($availableIps) - count($ipsUsadas) }} de {{ count($availableIps) }}
                            </small>
                        @endif
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="{{ route('asignarIPindex') }}" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                @if(!$ip || !$pool_id) disabled @endif>
                            <span wire:loading.remove>
                                <i class="fas fa-save me-1"></i> Asignar IP
                            </span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                Procesando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>