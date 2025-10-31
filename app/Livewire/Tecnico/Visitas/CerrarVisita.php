<?php

namespace App\Livewire\Tecnico\Visitas;

use Livewire\Component;
use App\Models\Visita;
use App\Models\Consumible;
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

    public $visita;
    public $solucion;
    public $fotos = [];

    public $consumibles; // stock disponible
    public $inventarios; // inventarios disponibles

    // Campos de selección actual
    public $consumibleSeleccionado;
    public $cantidadConsumible;
    public $inventarioSeleccionado;

    // Listas finales
    public $consumiblesUsados = [];
    public $inventariosUsados = [];

    protected $rules = [
        'solucion' => 'required|string',
        'fotos' => 'required|array|min:1|max:5', // mínimo 1, máximo 5
        'fotos.*' => 'image|max:2048', // cada imagen máximo 2MB
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

        $this->consumibles = ConsumibleStock::with('consumible')
            ->where('usuario_id', Auth::id())
            ->where('cantidad', '>', 0)
            ->get();

        $this->inventarios = Inventario::with('modelo')
            ->where('user_id', Auth::id())
            ->whereNull('visita_id')
            ->get();

        // 🔹 Evita el error count() sobre null
        $this->consumiblesUsados = [];
        $this->inventariosUsados = [];
    }


    public function agregarConsumible()
    {
        if ($this->consumibleSeleccionado && $this->cantidadConsumible > 0) {
            $consumible = $this->consumibles->firstWhere('consumible_id', $this->consumibleSeleccionado);
            if ($consumible) {
                $this->consumiblesUsados[] = [
                    'id' => $consumible->consumible_id,
                    'nombre' => $consumible->consumible->nombre,
                    'cantidad' => $this->cantidadConsumible,
                ];
            }

            $this->consumibleSeleccionado = null;
            $this->cantidadConsumible = null;
        }
    }

    public function eliminarConsumible($index)
    {
        unset($this->consumiblesUsados[$index]);
        $this->consumiblesUsados = array_values($this->consumiblesUsados);
    }

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

    public function eliminarInventario($index)
    {
        unset($this->inventariosUsados[$index]);
        $this->inventariosUsados = array_values($this->inventariosUsados);
    }

    public function cerrarVisita()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // 1️⃣ Actualizar visita
                $this->visita->update([
                    'solucion' => $this->solucion,
                    'estado' => 'Completada',
                ]);

                // 2️⃣ Guardar fotos
                foreach ($this->fotos as $foto) {
                    $path = $foto->store('visitas/' . $this->visita->id, 'public');
                    $this->visita->fotos()->create([
                        'ruta' => $path,
                        'nombre_original' => $foto->getClientOriginalName(),
                    ]);
                }

                // 3️⃣ Consumibles usados (agrupados por ID)
                $consumiblesAgrupados = [];

                foreach ($this->consumiblesUsados as $item) {
                    if (!isset($consumiblesAgrupados[$item['id']])) {
                        $consumiblesAgrupados[$item['id']] = $item;
                    } else {
                        // Si ya existe, sumamos la cantidad
                        $consumiblesAgrupados[$item['id']]['cantidad'] += $item['cantidad'];
                    }
                }

                // 🔹 Ahora procesamos solo uno por consumible
                foreach ($consumiblesAgrupados as $item) {
                    $stock = ConsumibleStock::where('usuario_id', Auth::id())
                        ->where('consumible_id', $item['id'])
                        ->first();

                    // Validar existencia de stock
                    if (!$stock) {
                        throw new \Exception("No tienes stock del consumible seleccionado ({$item['nombre']}).");
                    }

                    // Validar cantidad disponible
                    if ($item['cantidad'] > $stock->cantidad) {
                        throw new \Exception("La cantidad solicitada ({$item['cantidad']}) supera el stock disponible ({$stock->cantidad}) del consumible {$item['nombre']}.");
                    }

                    // Descontar cantidad al técnico
                    $stock->cantidad -= $item['cantidad'];
                    $stock->save();

                    // Registrar movimiento (una sola vez por consumible)
                    ConsumibleMovimiento::create([
                        'consumible_id'   => $item['id'],
                        'cantidad'        => $item['cantidad'],
                        'tipo_movimiento' => 'salida',
                        'origen_tipo'     => 'usuario',
                        'origen_id'       => Auth::id(),
                        'destino_tipo'    => 'visita',
                        'destino_id'      => $this->visita->id,
                        'descripcion'     => 'Uso en visita #' . $this->visita->id,
                        'user_id'         => Auth::id(),
                    ]);

                    // Registrar o actualizar el stock en la visita
                    $stockVisita = ConsumibleStock::firstOrNew([
                        'visita_id'      => $this->visita->id,
                        'consumible_id'  => $item['id'],
                    ]);

                    // Si ya existía, sumamos
                    $stockVisita->cantidad = ($stockVisita->cantidad ?? 0) + $item['cantidad'];

                    // Copiar contexto del stock original
                    $stockVisita->bodega_id  = $stock->bodega_id;
                    $stockVisita->cliente_id = $this->visita->ticket->cliente_id ?? null;
                    $stockVisita->nodo_id    = $this->visita->ticket->nodo_id ?? null;
                    $stockVisita->usuario_id = null;

                    $stockVisita->save();
                }


                // 4️⃣ Inventarios usados
                foreach ($this->inventariosUsados as $item) {
                    // Asegurar que existe el ID
                    if (!isset($item['id'])) continue;

                    $inventario = Inventario::find($item['id']);
                    if ($inventario) {

                        // 🔹 Si la visita tiene ticket → el inventario va al cliente
                        if ($this->visita->ticket) {
                            $clienteId = $this->visita->ticket->cliente_id;

                            // Registrar movimiento → destino: cliente
                            MovimientoInventario::create([
                                'inventario_id'     => $inventario->id,
                                'tipo_movimiento'   => 'salida',
                                'descripcion'       => 'Traslado al cliente #' . $clienteId . ' desde visita #' . $this->visita->id,
                                'user_anterior_id'  => $inventario->user_id,
                                'cliente_nuevo_id'  => $clienteId,
                                'user_id'           => Auth::id(),
                            ]);

                            // Actualizar inventario → asignarlo al cliente
                            $inventario->update([
                                'cliente_id' => $clienteId,
                                'user_id'    => null,          // liberar del técnico
                                'visita_id'  => null,          // ya no pertenece a la visita
                            ]);
                        }
                        // 🔹 Si la visita NO tiene ticket → el inventario se queda en la visita
                        else {
                            // Registrar movimiento → destino: visita
                            MovimientoInventario::create([
                                'inventario_id'     => $inventario->id,
                                'tipo_movimiento'   => 'salida',
                                'descripcion'       => 'Traslado a visita #' . $this->visita->id,
                                'user_anterior_id'  => $inventario->user_id,
                                'visita_nuevo_id'   => $this->visita->id,
                                'user_id'           => Auth::id(),
                            ]);

                            // Actualizar inventario → asignarlo a la visita
                            $inventario->update([
                                'visita_id'  => $this->visita->id,
                                'user_id'    => null,          // liberar del técnico
                                'cliente_id' => null,          // sin cliente asignado
                            ]);
                        }
                    }
                }
            });

            session()->flash('message', 'La visita se cerró correctamente.');
            return redirect()->route('tecnico.visitas');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
        $this->validate();
    }

    public function render()
    {
        return view('livewire.tecnico.visitas.cerrar-visita');
    }
}
