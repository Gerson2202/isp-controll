<div>   
    <div>
        @if($isLoading)
            <div class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 text-muted">Cargando información del cliente...Cliente no tiene contrato</p>
            </div>
        @else
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Editar Plan para: {{ $cliente->nombre }}</h5>
                </div>
                
                <div class="card-body">
                    @if(!empty($mensaje))
                        <div class="alert alert-{{ $tipoMensaje == 'success' ? 'success' : 'danger' }}">
                            {{ $mensaje }}
                        </div>
                    @endif
                    
                    @if(!$cliente->contrato)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Este cliente no tiene un contrato asociado. No se pueden mostrar los planes disponibles.
                        </div>
                    @else
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nodo actual:</label>
                                <p class="form-control-plaintext">{{ $cliente->contrato->plan->nodo->nombre }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Plan actual:</label>
                                <p class="form-control-plaintext">{{ $cliente->contrato->plan->nombre }}</p>
                            </div>
                        </div>
                        {{-- Solo apareceran los campos si el cliente ya tiene una ip asignada --}}
                        @if ($cliente->ip == null)
                            <h5 class="text-red">Cliente sin ip asignada </h5>
                        @else
                            <form wire:submit.prevent="actualizarPlan">
                                <div class="mb-3">
                                    <label for="plan_seleccionado" class="form-label">Seleccionar nuevo plan</label>
                                    <select
                                        wire:model="plan_seleccionado"
                                        id="plan_seleccionado"
                                        class="form-select"
                                        wire:loading.attr="disabled"
                                    >
                                        @foreach($planes as $plan)
                                            <option value="{{ $plan->id }}" @selected($plan->id == $cliente->contrato->plan_id)>
                                                {{ $plan->nombre }}
                                                @if($plan->id == $cliente->contrato->plan_id) (Actual) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="precio" class="form-label">Precio</label>
                                    <input
                                        type="text"
                                        wire:model="precio"
                                        id="precio"
                                        class="form-control @error('precio') is-invalid @enderror"
                                        placeholder="Ej: 80.000, 50.000, 1.000"
                                    >
                                    @error('precio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Formato aceptado: 80.000, 50.000, 1.000</small>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                        wire:loading.attr="disabled"
                                    >
                                        <span wire:loading.remove>Actualizar Plan</span>
                                        <span wire:loading>
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            Procesando...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        @endif
                        
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
