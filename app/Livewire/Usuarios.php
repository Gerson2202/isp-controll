<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class Usuarios extends Component
{
    use WithPagination;

    public $search = '';
    public $user_id, $name, $email, $password;
    public $selectedRoles = [];
    public $selectedPermissions = [];

    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user_id),
            ],
            'password' => $this->user_id ? 'nullable|min:6' : 'required|min:6',
        ];
    }
    // Mensajes para las validaciones
    protected function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }

    public function render()
    {
        $users = User::where('id', '!=', 1) // Excluye al superadmin
        ->where(function ($query) {
            $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%");
        })
        ->paginate(10);


        $roles = Role::pluck('name', 'id');
        $permissions = Permission::pluck('name', 'id');

        return view('livewire.usuarios', compact('users', 'roles', 'permissions'));
    }

    public function resetForm()
    {
        $this->reset(['user_id', 'name', 'email', 'password', 'selectedRoles', 'selectedPermissions']);
        $this->resetValidation();
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('show-create-modal');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        $this->selectedPermissions = $user->permissions->pluck('name')->toArray();

        $this->dispatch('show-edit-modal');
    }

    public function save()
    {
        // Si se intenta editar el usuario con ID = 1 (SuperAdmin)
        if ($this->user_id == 1) {
            $this->dispatch('notify', 
                type: 'error',
                message: '⚠️ No está permitido modificar al usuario SuperAdmin.'
            );
            return;
        }

        // Validar datos
        $data = $this->validate();

        // Crear o actualizar usuario
        $user = User::updateOrCreate(
            ['id' => $this->user_id],
            [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password
                    ? bcrypt($this->password)
                    : User::find($this->user_id)?->password,
            ]
        );

        // Asignar roles y permisos
        $user->syncRoles($this->selectedRoles);
        $user->syncPermissions($this->selectedPermissions);

        // Cerrar modal y notificar éxito
        $this->dispatch('hide-modals');
        $this->dispatch('notify', 
            type: 'success',
            message: '¡Usuario actualizado con éxito!'
        );    
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        session()->flash('message', 'Usuario eliminado');
    }
}
