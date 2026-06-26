<?php

namespace App\Livewire\Finanzas;

use App\Models\Gasto;
use App\Models\GastoAdjunto;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class GastoAdjuntos extends Component
{
    use WithFileUploads;

    public $gastoId; // Cambiado a gastoId para coincidir con la vista
    public $gasto;
    public $adjuntos;
    public $archivo;
    public $descripcion;

    protected $rules = [
        'archivo' => 'required|file|max:10240', // Máximo 10MB
    ];

    protected $messages = [
        'archivo.required' => 'Debes seleccionar un archivo',
        'archivo.file' => 'El archivo no es válido',
        'archivo.max' => 'El archivo no debe pesar más de 10MB',
    ];

    public function mount($gastoId)
    {
        $this->gastoId = $gastoId;
        $this->gasto = Gasto::findOrFail($gastoId);
        $this->cargarAdjuntos();
    }

    public function cargarAdjuntos()
    {
        $this->adjuntos = GastoAdjunto::where('gasto_id', $this->gastoId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function subirArchivo()
    {
        $this->validate();

        try {
            // Guardar archivo
            $path = $this->archivo->store('gastos/' . $this->gastoId, 'public');
            
            // Crear registro en BD
            GastoAdjunto::create([
                'gasto_id' => $this->gastoId,
                'archivo' => $path,
                'nombre_original' => $this->archivo->getClientOriginalName(),
                'mime_type' => $this->archivo->getMimeType(),
                'size' => $this->archivo->getSize(),
            ]);

            // Limpiar campo
            $this->reset('archivo');
            
            // Recargar lista
            $this->cargarAdjuntos();

            $this->dispatch(
                'notify',
                type: 'success',
                message: 'Archivo subido correctamente'
            );

        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'Error al subir el archivo: ' . $e->getMessage()
            );
        }
    }

    public function eliminarAdjunto($id)
    {
        try {
            $adjunto = GastoAdjunto::findOrFail($id);
            
            // Eliminar archivo físico
            Storage::disk('public')->delete($adjunto->archivo);
            
            // Eliminar registro
            $adjunto->delete();
            
            // Recargar lista
            $this->cargarAdjuntos();

            $this->dispatch(
                'notify',
                type: 'success',
                message: 'Archivo eliminado correctamente'
            );

        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'Error al eliminar el archivo'
            );
        }
    }

    public function descargarArchivo($id)
    {
        try {
            $adjunto = GastoAdjunto::findOrFail($id);
            
            return Storage::disk('public')->download(
                $adjunto->archivo,
                $adjunto->nombre_original
            );

        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'Error al descargar el archivo'
            );
        }
    }

    public function render()
    {
        return view('livewire.finanzas.gasto-adjuntos');
    }
}