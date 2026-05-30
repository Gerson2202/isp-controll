<div>
    <button wire:click="toggleChat" 
        class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center"
        style="position: fixed; bottom: 25px; right: 25px; width: 60px; height: 60px; z-index: 1050; transition: transform 0.2s; 
               {{ $isOpen ? 'transform: scale(0);' : 'transform: scale(1);' }}">
        
        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-white">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
        </svg>
    </button>

    @if ($isOpen)
        <div class="card shadow-lg border-0"
            style="position: fixed; bottom: 25px; right: 25px; width: 360px; height: 550px; z-index: 1060; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column; animation: slideUp 0.3s ease-out;">
            
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center p-3 border-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <svg width="20" height="20" class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">IA Asistente</h6>
                        <small class="text-white-50" style="font-size: 0.75rem;">En línea</small>
                    </div>
                </div>
                <button wire:click="toggleChat" class="btn-close btn-close-white" aria-label="Cerrar" style="font-size: 0.8rem;"></button>
            </div>

            <div class="card-body overflow-auto p-3 d-flex flex-column gap-3" style="background-color: #f8f9fa; flex: 1;">
                @if (empty($messages))
                    <div class="text-center mt-4">
                        <span class="badge bg-secondary text-wrap fw-normal p-2" style="font-size: 0.8rem;">
                            ¡Hola! Hazme cualquier consulta sobre el sistema (clientes, pagos, deudas).
                        </span>
                    </div>
                @endif

                @foreach ($messages as $msg)
                    <div class="d-flex {{ $msg['role'] === 'user' ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="p-2 px-3 shadow-sm"
                            style="max-width: 85%; font-size: 0.9rem; line-height: 1.4;
                            {{ $msg['role'] === 'user' 
                                ? 'background-color: #0d6efd; color: white; border-radius: 18px 18px 0 18px;' 
                                : 'background-color: white; color: #333; border: 1px solid #e9ecef; border-radius: 18px 18px 18px 0;' }}">
                            {{ $msg['content'] }}
                        </div>
                    </div>
                @endforeach

                <div wire:loading wire:target="sendMessage" class="align-self-start">
                    <div class="bg-white border shadow-sm p-2 px-3 d-flex align-items-center gap-1" style="border-radius: 18px 18px 18px 0;">
                        <div class="spinner-grow text-primary" style="width: 0.5rem; height: 0.5rem;" role="status"></div>
                        <div class="spinner-grow text-primary" style="width: 0.5rem; height: 0.5rem; animation-delay: 0.2s;" role="status"></div>
                        <div class="spinner-grow text-primary" style="width: 0.5rem; height: 0.5rem; animation-delay: 0.4s;" role="status"></div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white border-top-0 p-3 shadow-sm">
                <form wire:submit.prevent="sendMessage" class="d-flex align-items-center gap-2 m-0">
                    <input wire:model="prompt" type="text" 
                        class="form-control rounded-pill border-0 bg-light px-4 py-2 shadow-none" 
                        placeholder="Escribe aquí..." 
                        style="font-size: 0.9rem;" required>
                    
                    <button type="submit" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" 
                        style="width: 45px; height: 45px;"
                        wire:loading.attr="disabled">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <style>
            @keyframes slideUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    @endif
</div>