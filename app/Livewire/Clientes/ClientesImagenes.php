<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ClienteFoto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClientesImagenes extends Component
{
    use WithFileUploads;

    public $clienteId;
    public $descripcion;
    public $fotos = [];
    public $fotosSubidas = [];
    public $fotosTemporales = [];
    public $descripciones = [];
    public $maxFotos = 4;
    public $cliente ;

    protected $rules = [
        'fotos.*' => 'required|image|max:2048', // 2MB máximo
        'descripciones.*' => 'nullable|string|max:255',
    ];

    protected $messages = [
        'fotos.*.required' => 'Debes seleccionar al menos una foto.',
        'fotos.*.image' => 'El archivo debe ser una imagen válida.',
        'fotos.*.max' => 'La imagen no debe pesar más de 2MB.',
        'descripciones.*.max' => 'La descripción no debe exceder los 255 caracteres.',
    ];

    public function mount($clienteId)
    {
        $this->clienteId = $clienteId;
        $this->cliente = Cliente::find($this->clienteId);
        $this->cargarFotos();
    }

    public function cargarFotos()
    {
        $this->fotosSubidas = ClienteFoto::where('cliente_id', $this->clienteId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        // Inicializar descripciones
        foreach ($this->fotosSubidas as $foto) {
            $this->descripciones[$foto['id']] = $foto['descripcion'] ?? '';
        }
    }

    public function updatedFotos()
    {
        $this->validate([
            'fotos.*' => 'image|max:2048',
        ]);

        // Verificar límite máximo
        $totalFotos = count($this->fotosSubidas) + count($this->fotosTemporales) + count($this->fotos);
        if ($totalFotos > $this->maxFotos) {
            $this->addError('limite', 'Solo se permiten máximo ' . $this->maxFotos . ' fotos por cliente.');
            $this->reset('fotos');
            return;
        }

        // Agregar a fotos temporales
        foreach ($this->fotos as $foto) {
            $this->fotosTemporales[] = $foto;
        }

        $this->reset('fotos');
    }

    public function guardarFotos()
    {
        // Validar antes de guardar
        if (empty($this->fotosTemporales)) {  // Se añadió el paréntesis de cierre faltante
            $this->dispatch('notificar', [
                'tipo' => 'warning',
                'mensaje' => 'No hay fotos nuevas para guardar.'
            ]);
            return;
        }

        // Verificar límite
        $totalFotos = count($this->fotosSubidas) + count($this->fotosTemporales);
        if ($totalFotos > $this->maxFotos) {
            $this->addError('limite', 'Solo se permiten máximo ' . $this->maxFotos . ' fotos por cliente.');
            return;
        }

        try {
            // Guardar fotos temporales
            foreach ($this->fotosTemporales as $fotoTemp) {
                $nombreArchivo = Str::random(20) . '.' . $fotoTemp->getClientOriginalExtension();
                $ruta = $fotoTemp->storeAs('clientes/' . $this->clienteId, $nombreArchivo, 'public');

                ClienteFoto::create([
                    'cliente_id' => $this->clienteId,
                    'ruta' => $ruta,
                    'nombre_original' => $fotoTemp->getClientOriginalName(),
                    'descripcion' => '',
                ]);
            }


            // Limpiar y recargar
            $this->reset(['fotosTemporales']);
            $this->cargarFotos();

             $this->dispatch('notify', 
                type: 'success',
                message: 'Imagenes guardadas con exito'
            );


        } catch (\Exception $e) {
            $this->dispatch('notify', 
               type: 'error',
                message: 'Error al guardar las fotos: ' . $e->getMessage()
             );
        }
    }

    public function eliminarFoto($id)
    {
        try {
            $foto = ClienteFoto::findOrFail($id);

            // Eliminar archivo físico
            if (Storage::disk('public')->exists($foto->ruta)) {
                Storage::disk('public')->delete($foto->ruta);
            }

            // Eliminar registro
            $foto->delete();

            // Recargar fotos
            $this->cargarFotos();

            $this->dispatch('notify', 
                type: 'success',
                message: 'Foto eliminada con exito'
            );

        } catch (\Exception $e) {
            $this->dispatch('notificar', [
                'tipo' => 'error',
                'mensaje' => 'Error al eliminar la foto: ' . $e->getMessage()
            ]);
        }
    }

    public function eliminarTemporal($index)
    {
        if (isset($this->fotosTemporales[$index])) {
            unset($this->fotosTemporales[$index]);
            $this->fotosTemporales = array_values($this->fotosTemporales);
        }
    }
    
    public function guardarDescripcion($fotoId)
    {
        if (!isset($this->descripciones[$fotoId])) {
            return;
        }

        try {
            ClienteFoto::where('id', $fotoId)
                ->update(['descripcion' => $this->descripciones[$fotoId]]);
                
            $this->dispatch('notify', 
                type: 'success',
                message: 'Descripción guardada correctamente'
            );
        } catch (\Exception $e) {
            $this->dispatch('notificar', [
                'tipo' => 'error',
                'mensaje' => 'Error al guardar la descripción: ' . $e->getMessage()
            ]);
        }
    }
    public function render()
    {
        return view('livewire.clientes.clientes-imagenes');
    }
}