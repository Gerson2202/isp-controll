<?php

namespace App\Livewire\Tecnico\Visitas;

use Livewire\Component;
use App\Models\Visita;
use App\Models\ConsumibleStock;
use App\Models\ConsumibleMovimiento;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CerrarVisita extends Component
{
    use WithFileUploads;

    public $visita, $solucion, $fotos = [];

    // Recursos disponibles
    public $consumibles;
    public $inventarios;
    public $bodegas = [];
    public $buscar = '';


    // Selecciones actuales
    public $consumibleSeleccionado;
    public $cantidadConsumible;
    public $origenSeleccionado; // "usuario" o ID de bodega
    public $inventarioSeleccionado;

    // Listas de acción
    public $consumiblesUsados = [];
    public $inventariosUsados = [];
    public $inventariosCliente = [];
    public $inventarioClienteSeleccionado;
    public $inventariosRetirados = [];
    public $destinoRetiro = [];
    public $filtroInventario = '';

    protected $rules = [
        'solucion' => 'required|string',
        'fotos' => 'required|array|min:1|max:5',
        'fotos.*' => 'image|max:2048',
    ];

    protected $messages = [
        'solucion.required' => 'Debes ingresar la solución aplicada.',
        'fotos.required' => 'Debes subir al menos una foto de la visita.',
        'fotos.min' => 'Debes subir al menos una foto de la visita.',
        'fotos.max' => 'Solo puedes subir un máximo de 5 fotos por visita.',
        'fotos.*.image' => 'Cada archivo debe ser una imagen válida.',
        'fotos.*.max' => 'Cada imagen no debe superar los 2 MB.',
    ];

    public function mount($visitaId)
    {
        $this->visita = Visita::with('ticket.cliente')->findOrFail($visitaId);
        $user = Auth::user();

        // 🔹 Bodegas del usuario
        $this->bodegas = $user->bodegas()->select('bodegas.id', 'bodegas.nombre')->get();
        $bodegaIds = $this->bodegas->pluck('id');

        // 🔹 Consumibles disponibles
        $this->consumibles = ConsumibleStock::with('consumible')
            ->where(function ($q) use ($user, $bodegaIds) {
                $q->where('usuario_id', $user->id)
                    ->orWhereIn('bodega_id', $bodegaIds);
            })
            ->where('cantidad', '>', 0)
            ->get();

        $this->inventarios = Inventario::with(['modelo', 'bodega'])
            ->where(function ($q) use ($user, $bodegaIds) {
                $q->where('user_id', $user->id)
                    ->orWhereIn('bodega_id', $bodegaIds);
            })
            ->whereNull('visita_id')
            ->get();



        // 🔹 Inventarios actualmente en el cliente
        $this->inventariosCliente = $this->visita->ticket && $this->visita->ticket->cliente_id
            ? Inventario::with('modelo')->where('cliente_id', $this->visita->ticket->cliente_id)->get()
            : collect();

        $this->consumiblesUsados = [];
        $this->inventariosUsados = [];
        $this->inventariosRetirados = [];
    }

    /** =========================
     * AGREGAR CONSUMIBLE
     * ========================= */
    public function agregarConsumible()
    {
        if (!$this->consumibleSeleccionado || !$this->cantidadConsumible) {
            session()->flash('error', 'Debe seleccionar un consumible y cantidad.');
            return;
        }

        $user = Auth::user();

        // Buscar el stock según origen elegido
        $stock = null;
        if ($this->origenSeleccionado === 'usuario') {
            $stock = ConsumibleStock::where('usuario_id', $user->id)
                ->where('consumible_id', $this->consumibleSeleccionado)
                ->first();
        } elseif (is_numeric($this->origenSeleccionado)) {
            $stock = ConsumibleStock::where('bodega_id', $this->origenSeleccionado)
                ->where('consumible_id', $this->consumibleSeleccionado)
                ->first();
        } else {
            // automático: primero busca en usuario, luego en bodegas
            $stock = ConsumibleStock::where('usuario_id', $user->id)
                ->where('consumible_id', $this->consumibleSeleccionado)
                ->first();

            if (!$stock || $stock->cantidad < $this->cantidadConsumible) {
                $bodegaIds = $this->bodegas->pluck('id');
                $stock = ConsumibleStock::whereIn('bodega_id', $bodegaIds)
                    ->where('consumible_id', $this->consumibleSeleccionado)
                    ->orderByDesc('cantidad')
                    ->first();
            }
        }

        if (!$stock || $stock->cantidad < $this->cantidadConsumible) {
            session()->flash('error', 'No hay suficiente stock disponible en el origen seleccionado.');
            return;
        }

        $this->consumiblesUsados[] = [
            'id' => $stock->consumible_id,
            'nombre' => $stock->consumible->nombre,
            'cantidad' => $this->cantidadConsumible,
            'origen' => $stock->usuario_id ? 'usuario' : 'bodega',
            'bodega_id' => $stock->bodega_id,
            'bodega_nombre' => $stock->bodega_id
                ? $this->bodegas->firstWhere('id', $stock->bodega_id)?->nombre
                : null,
        ];

        $this->reset(['consumibleSeleccionado', 'cantidadConsumible', 'origenSeleccionado']);
    }

    public function eliminarConsumible($index)
    {
        unset($this->consumiblesUsados[$index]);
        $this->consumiblesUsados = array_values($this->consumiblesUsados);
    }

    /** =========================
     * AGREGAR INVENTARIO
     * ========================= */
    public function agregarInventario()
    {
        if ($this->inventarioSeleccionado) {
            $inv = $this->inventarios->firstWhere('id', $this->inventarioSeleccionado);
            if ($inv) {
                $this->inventariosUsados[] = [
                    'id' => $inv->id,
                    'nombre' => $inv->modelo->nombre,
                    'serial' => $inv->serial,
                ];
            }
            $this->inventarioSeleccionado = null;
        }
    }
    //  buscador de inventario
    // Método helper para obtener el texto del inventario (opcional, pero útil)
    public function getInventarioText($inventario)
    {
        return ($inventario->modelo->nombre ?? 'Sin modelo') . ' ' .
            ($inventario->mac ?? 'N/A') . ' ' .
            ($inventario->serial ?? 'N/A') . ' ' .
            ($inventario->bodega ? $inventario->bodega->nombre : 'Bodega personal');
    }

    // Método para verificar si hay resultados
    public function tieneResultados($filtro)
    {
        foreach ($this->inventarios as $inv) {
            if (stripos($this->getInventarioText($inv), $filtro) !== false) {
                return true;
            }
        }
        return false;
    }

    public function eliminarInventario($index)
    {
        unset($this->inventariosUsados[$index]);
        $this->inventariosUsados = array_values($this->inventariosUsados);
    }

    /** =========================
     * RETIRO DE EQUIPOS CLIENTE
     * ========================= */
    public function agregarRetiro()
    {
        if ($this->inventarioClienteSeleccionado) {
            $inv = $this->inventariosCliente->firstWhere('id', $this->inventarioClienteSeleccionado);
            if ($inv) {
                $this->inventariosRetirados[] = [
                    'id' => $inv->id,
                    'nombre' => $inv->modelo->nombre ?? 'Sin modelo',
                    'serial' => $inv->serial,
                    'mac' => $inv->mac,
                ];
                $this->destinoRetiro[$inv->id] = ['tipo' => null, 'id' => null];
            }
            $this->inventarioClienteSeleccionado = null;
        }
    }

    public function eliminarRetiro($index)
    {
        $item = $this->inventariosRetirados[$index] ?? null;
        if ($item && isset($item['id'])) {
            unset($this->destinoRetiro[$item['id']]);
        }
        unset($this->inventariosRetirados[$index]);
        $this->inventariosRetirados = array_values($this->inventariosRetirados);
    }

    /** =========================
     * CERRAR VISITA
     * ========================= */
    public function cerrarVisita()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // Guardar fotos
                foreach ($this->fotos as $foto) {
                    $path = $foto->store('visitas/' . $this->visita->id, 'public');
                    $this->visita->fotos()->create([
                        'ruta' => $path,
                        'nombre_original' => $foto->getClientOriginalName(),
                    ]);
                }

                // 🔹 Procesar consumibles usados (origen -> cliente/visita)
                foreach ($this->consumiblesUsados as $item) {
                    // Obtener stock origen (usuario o bodega)
                    $stock = $item['origen'] === 'usuario'
                        ? ConsumibleStock::where('usuario_id', Auth::id())
                        ->where('consumible_id', $item['id'])
                        ->first()
                        : ConsumibleStock::where('bodega_id', $item['bodega_id'])
                        ->where('consumible_id', $item['id'])
                        ->first();

                    if (!$stock) {
                        throw new \Exception("No se encontró el stock origen para {$item['nombre']}.");
                    }

                    if ($stock->cantidad < $item['cantidad']) {
                        throw new \Exception("Stock insuficiente para {$item['nombre']} (disponible: {$stock->cantidad}).");
                    }

                    // Descontar cantidad del origen
                    $stock->decrement('cantidad', $item['cantidad']);

                    // (Opcional) eliminar si queda en 0
                    // if ($stock->cantidad <= 0) $stock->delete();

                    // Definir destino (cliente o visita)
                    $clienteId = $this->visita->ticket->cliente_id ?? null;
                    $descripcion = 'Uso en visita #' . $this->visita->id;
                    if ($clienteId) $descripcion .= ' (Cliente asociado al ticket)';

                    // Registrar movimiento (trazabilidad)
                    ConsumibleMovimiento::create([
                        'consumible_id'   => $item['id'],
                        'cantidad'        => $item['cantidad'],
                        'tipo_movimiento' => 'salida',
                        'origen_tipo'     => $item['origen'],
                        'origen_id'       => $item['origen'] === 'usuario' ? Auth::id() : $item['bodega_id'],
                        'destino_tipo'    => $clienteId ? 'cliente' : 'visita',
                        'destino_id'      => $clienteId ?? $this->visita->id,
                        'descripcion'     => $descripcion,
                        'user_id'         => Auth::id(),
                    ]);

                    // Crear nuevo registro de stock en destino (cliente o visita)
                    $nuevoStock = new ConsumibleStock();
                    $nuevoStock->consumible_id = $item['id'];
                    $nuevoStock->cantidad      = $item['cantidad'];
                    $nuevoStock->bodega_id     = null;              // ❌ No se asigna a ninguna bodega
                    $nuevoStock->usuario_id    = null;              // ❌ No pertenece a un usuario
                    $nuevoStock->cliente_id    = $clienteId;        // ✅ Cliente si hay ticket
                    $nuevoStock->visita_id     = $clienteId ? null : $this->visita->id; // ✅ Si no hay cliente, asignar a visita
                    $nuevoStock->nodo_id       = $this->visita->ticket->nodo_id ?? null;
                    $nuevoStock->save();
                }

                // 🔹 Inventarios instalados (traslado hacia cliente)
                foreach ($this->inventariosUsados as $item) {
                    $inventario = Inventario::find($item['id']);
                    if (!$inventario) continue;

                    // 🔹 Determinar si la visita tiene ticket
                    $tieneTicket = !empty($this->visita->ticket_id) && !empty($this->visita->ticket);
                    $clienteId = $tieneTicket ? ($this->visita->ticket->cliente_id ?? null) : null;

                    // 🔹 Definir destino según si tiene ticket o no
                    if ($tieneTicket) {
                        // CON TICKET: Equipo va al CLIENTE
                        $clienteNuevoId = $clienteId;
                        $visitaNuevoId = null;
                        $bodegaNuevaId = null;
                        $userNuevoId = null;
                        $descripcion = 'Traslado al cliente #' . $clienteId . ' desde visita #' . $this->visita->id;
                    } else {
                        // SIN TICKET: Equipo queda en la VISITA
                        $clienteNuevoId = null;
                        $visitaNuevoId = $this->visita->id;
                        $bodegaNuevaId = null;
                        $userNuevoId = null;
                        $descripcion = 'Traslado a visita #' . $this->visita->id . ' (sin ticket asignado)';
                    }

                    // 🔹 Registrar movimiento completo
                    MovimientoInventario::create([
                        'inventario_id'       => $inventario->id,
                        'tipo_movimiento'     => 'salida',
                        'descripcion'         => $descripcion,

                        // 🔹 Campos de ubicación ANTERIOR (donde estaba)
                        'bodega_anterior_id'  => $inventario->bodega_id,
                        'user_anterior_id'    => $inventario->user_id,
                        'cliente_anterior_id' => $inventario->cliente_id,
                        'nodo_anterior_id'    => $inventario->nodo_id,
                        'visita_anterior_id'  => $inventario->visita_id,

                        // 🔹 Campos de ubicación NUEVA (a dónde va)
                        'bodega_nueva_id'     => $bodegaNuevaId,
                        'user_nuevo_id'       => $userNuevoId,
                        'cliente_nuevo_id'    => $clienteNuevoId,  // ✅ Cliente si tiene ticket
                        'nodo_nuevo_id'       => $inventario->nodo_id, // Mantiene el mismo nodo
                        'visita_nuevo_id'     => $visitaNuevoId,   // ✅ Visita si NO tiene ticket
                        'user_id'             => Auth::id(),
                    ]);

                    // 🔹 Actualizar el inventario principal
                    $inventario->update([
                        'cliente_id' => $clienteNuevoId,
                        'user_id'    => $userNuevoId,
                        'bodega_id'  => $bodegaNuevaId,
                        'visita_id'  => $visitaNuevoId,
                    ]);

                    
                }

                // 🔹 Retiro de equipos
                foreach ($this->inventariosRetirados as $item) {
                    $inventario = Inventario::find($item['id']);
                    if (!$inventario) continue;

                    $dest = $this->destinoRetiro[$inventario->id] ?? null;
                    if ($dest && $dest['tipo'] === 'usuario' && empty($dest['id'])) {
                        $dest['id'] = Auth::id();
                    }

                    if (!$dest || empty($dest['tipo']) || empty($dest['id'])) {
                        throw new \Exception("Debes seleccionar el destino (usuario o bodega) para el equipo {$inventario->modelo->nombre}.");
                    }

                    MovimientoInventario::create([
                        'inventario_id' => $inventario->id,
                        'tipo_movimiento' => 'retiro',
                        'descripcion' => 'Equipo retirado en visita #' . $this->visita->id,
                        'cliente_anterior_id' => $inventario->cliente_id,
                        'user_nuevo_id' => $dest['tipo'] === 'usuario' ? $dest['id'] : null,
                        'bodega_nueva_id' => $dest['tipo'] === 'bodega' ? $dest['id'] : null,
                        'user_id' => Auth::id(),
                    ]);

                    $inventario->update([
                        'cliente_id' => null,
                        'visita_id' => null,
                        'user_id' => $dest['tipo'] === 'usuario' ? $dest['id'] : null,
                        'bodega_id' => $dest['tipo'] === 'bodega' ? $dest['id'] : null,
                    ]);
                }

                // 🔹 Preparar detalles automáticos de instalación y retiro
                $detalleInstalados = '';
                $detalleRetirados = '';

                if (count($this->inventariosUsados) > 0) {
                    $detalleInstalados = "\n\nEquipos instalados:";
                    foreach ($this->inventariosUsados as $item) {
                        $linea = "\n - {$item['nombre']}";
                        if (!empty($item['serial'])) $linea .= " | Serial: {$item['serial']}";
                        if (!empty($item['mac'])) $linea .= " | MAC: {$item['mac']}";
                        $detalleInstalados .= $linea;
                    }
                }

                if (count($this->inventariosRetirados) > 0) {
                    $detalleRetirados = "\n\nEquipos retirados:";
                    foreach ($this->inventariosRetirados as $item) {
                        $linea = "\n - {$item['nombre']}";
                        if (!empty($item['serial'])) $linea .= " | Serial: {$item['serial']}";
                        if (!empty($item['mac'])) $linea .= " | MAC: {$item['mac']}";
                        $detalleRetirados .= $linea;
                    }
                }

                // 🔹 Cerrar visita y guardar solución + observaciones
                $this->visita->update([
                    'estado'        => 'Completada',
                    'solucion'      => trim($this->solucion), // solo la solución técnica
                    'observacion' => trim($detalleInstalados . $detalleRetirados), // los detalles
                ]);
            });

            session()->flash('message', 'La visita se cerró correctamente.');
            return redirect()->route('tecnico.visitas');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.tecnico.visitas.cerrar-visita');
    }
}
