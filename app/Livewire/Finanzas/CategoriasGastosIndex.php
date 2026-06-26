<?php

namespace App\Livewire\Finanzas;

use App\Models\CategoriaGasto;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriasGastosIndex extends Component
{
    use WithPagination;

    public $buscar = '';
    public $categoria_id;
    public $nombre;
    public $color = '#0d6efd';
    public $descripcion;
    public $activo = true;

    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:categorias_gastos,nombre,' . ($this->categoria_id ?? 'NULL'),
            'color' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string|max:500',
            'activo' => 'boolean',
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre de la categoría es obligatorio',
        'nombre.unique' => 'Ya existe una categoría con este nombre',
        'nombre.max' => 'El nombre no puede tener más de 255 caracteres',
        'descripcion.max' => 'La descripción no puede tener más de 500 caracteres',
    ];

    public function guardar()
    {
        $this->validate();

        CategoriaGasto::updateOrCreate(
            ['id' => $this->categoria_id],
            [
                'nombre' => $this->nombre,
                'color' => $this->color,
                'descripcion' => $this->descripcion,
                'activo' => $this->activo,
            ]
        );

        $this->resetFormulario();

        $this->dispatch(
            'notify',
            type: 'success',
            message: $this->categoria_id ? 'Categoría actualizada correctamente' : 'Categoría creada correctamente'
        );
    }

    public function editar($id)
    {
        $categoria = CategoriaGasto::findOrFail($id);

        $this->categoria_id = $categoria->id;
        $this->nombre = $categoria->nombre;
        $this->color = $categoria->color;
        $this->descripcion = $categoria->descripcion;
        $this->activo = $categoria->activo;

        $this->dispatch(
            'notify',
            type: 'info',
            message: 'Editando categoría: ' . $categoria->nombre
        );
    }

    public function cambiarEstado($id)
    {
        $categoria = CategoriaGasto::findOrFail($id);
        $nuevoEstado = !$categoria->activo;
        
        $categoria->update(['activo' => $nuevoEstado]);

        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Categoría ' . ($nuevoEstado ? 'activada' : 'desactivada') . ' correctamente'
        );
    }

    public function resetFormulario()
    {
        $this->reset([
            'categoria_id',
            'nombre',
            'descripcion'
        ]);

        $this->color = '#0d6efd';
        $this->activo = true;
        
        $this->resetValidation();

    
    }

    public function render()
    {
        $categorias = CategoriaGasto::query()
            ->when($this->buscar, function ($query) {
                $query->where('nombre', 'like', '%' . $this->buscar . '%')
                      ->orWhere('descripcion', 'like', '%' . $this->buscar . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.finanzas.categorias-gastos-index', compact('categorias'));
    }
}