<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class RolesComponent extends Component
{
    use WithPagination;

    public $name, $role_id, $search = '';
    public $selectedPermissions = [];
    public $modalTitle = 'Crear Rol';

    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($this->role_id),
            ],
            'selectedPermissions' => 'array',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Ya existe un rol con este nombre.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',
        ];
    }

    public function render()
    {
        $roles = Role::where('name', 'like', "%{$this->search}%")->paginate(10);
        $permissions = Permission::orderBy('name')->get();

        return view('livewire.roles-component', compact('roles', 'permissions'));
    }

   public function create()
    {
        $this->reset(['name', 'role_id', 'selectedPermissions']);
        $this->modalTitle = 'Crear Rol';
        $this->dispatch('show-create-modal');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->role_id = $role->id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->modalTitle = 'Editar Rol';
        $this->dispatch('show-edit-modal');
    }

   public function save()
    {
        // Evitar que se modifique el Super Admin
        if ($this->role_id) {
            $role = Role::find($this->role_id);
            if ($role && $role->name === 'admin') {
                $this->dispatch('notify', type: 'error', message: 'No puedes modificar el rol  Admin.');
                return;
            }
        }
        $this->validate();

        $role = Role::updateOrCreate(
            ['id' => $this->role_id],
            [
                'name' => $this->name,
                'guard_name' => 'web', // 👈 esto es clave
            ]
        );

        // Aseguramos que los permisos existan con guard_name = 'web'
        $permissions = Permission::whereIn('name', $this->selectedPermissions)
            ->where('guard_name', 'web')
            ->get();

        $role->syncPermissions($permissions);

        $this->dispatch('hide-modals');
        $this->dispatch('notify', 
            type: 'success', 
            message: '¡Rol guardado con éxito!'
        );
    }

    public function delete($id)
    {
        $role = Role::findOrFail($id);


          // ✅ Verificar si algún usuario tiene asignado este rol (maneja error si no existe)
            $hasUsers = \App\Models\User::whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role->name);
            })->exists();

            if ($hasUsers) {
                $this->dispatch('notify', type: 'error', message: 'No puedes eliminar este rol porque está asignado a uno o más usuarios.');
                return;
            }

            $role->delete();
            $this->dispatch('notify', 
                type: 'success', 
                message: 'Rol eliminado correctamente.'
            );
    }
}
