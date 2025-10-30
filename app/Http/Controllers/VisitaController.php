<?php

namespace App\Http\Controllers;

use App\Livewire\Usuarios;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Visita;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class VisitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ver calendario
        if (!auth()->user()->can('ver calendario')) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('calendario.index');
    }

    // Enviar a pagina de tabla de visitas 
    public function visitasTabla()
    {
        // ver todas las  visitas 
        if (!auth()->user()->can('ver programacion')) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('visitas.tabla');
    }

    // 🔹 Mostrar vista para agregar usuarios a una visita
    public function agregarUsuario(Request $request, $visitaId)
    {
        $visita = Visita::with('users')->findOrFail($visitaId);

        // Obtener los IDs de los usuarios que ya están en la visita
        $usuariosAsociados = $visita->users->pluck('id');

        // Filtrar los usuarios que no estén en esa lista
        $usuarios = User::whereNotIn('id', $usuariosAsociados)->get();

        // Recibir fechas desde la URL
        $fecha_inicio = $request->query('fecha_inicio');
        $fecha_cierre = $request->query('fecha_cierre');

        return view('visitas.agregarUsuario', compact('visita', 'usuarios', 'fecha_inicio', 'fecha_cierre'));
    }


    // 🔹 Guardar usuarios agregados a la visita
    public function guardarUsuarios(Request $request, $visitaId)
    {
        $visita = Visita::findOrFail($visitaId);

        $request->validate([
            'usuarios' => 'required|array',
            'usuarios.*' => 'exists:users,id',
            'fecha_inicio' => 'required|date',
            'fecha_cierre' => 'required|date',
        ]);

        foreach ($request->usuarios as $usuarioId) {
            // Evitar duplicados
            if (!$visita->users()->where('user_id', $usuarioId)->exists()) {
                $visita->users()->attach($usuarioId, [
                    'fecha_inicio' => $request->fecha_inicio,
                    'fecha_cierre' => $request->fecha_cierre,
                ]);
            }
        }

        return redirect()->route('calendarioIndex');
    }
    // Enviar a pagina de visita show

    public function visitasShow($id)
    {

        $visita = Visita::with(['ticket.cliente', 'fotos'])->findOrFail($id);
        return view('visitas.show', compact('visita'));
    }


    // Mostrar el formulario de edición
    public function edit($visita_id)
    {

        $visita = Visita::findOrFail($visita_id); // Encuentra la visita por ID
        $usuarios = User::orderBy('name')->get();

        // Verificar si las fechas son null, si son null, no hacer nada, y si no lo son, convertirlas a Carbon
        if ($visita->fecha_inicio && is_string($visita->fecha_inicio)) {
            $visita->fecha_inicio = Carbon::parse($visita->fecha_inicio); // Convierte la fecha a Carbon si es una cadena
        }

        if ($visita->fecha_cierre && is_string($visita->fecha_cierre)) {
            $visita->fecha_cierre = Carbon::parse($visita->fecha_cierre); // Convierte la fecha a Carbon si es una cadena
        }

        // Si las fechas no son nulas, las formateamos
        if ($visita->fecha_inicio) {
            $visita->fecha_inicio = $visita->fecha_inicio->format('Y-m-d\TH:i'); // Formato adecuado para datetime-local
        }

        if ($visita->fecha_cierre) {
            $visita->fecha_cierre = $visita->fecha_cierre->format('Y-m-d\TH:i'); // Formato adecuado para datetime-local
        }

        // Pasar la visita a la vista
        return view('visitas.edit', compact('visita', 'usuarios'));
    }
    // Funcion para enviar a cola de programacion
    public function enviarACola(Request $request, $id)
    {

        // Obtener la visita por su ID
        $visita = Visita::findOrFail($id);

        // Establecer los campos 'fecha_inicio' y 'fecha_cierre' a null
        $visita->fecha_inicio = null;
        $visita->fecha_cierre = null;
        $visita->estado = 'Pendiente';

        // Guardar los cambios
        $visita->save();

        // Redirigir al calendario
        return redirect()->route('calendarioIndex')->with('success', 'Visita enviada a la cola de programación.');
    }

    public function colaDeProgramacion()
    {


        $visitasEnCola = Visita::whereDoesntHave('users')
            ->orWhereHas('users', function ($query) {
                $query->whereNull('visita_user.fecha_inicio')
                    ->orWhereNull('visita_user.fecha_cierre');
            })
            ->get();

        return view('visitas.tablaVisitasEncola', compact('visitasEnCola'));
    }


    // Actualizar la visita
    public function update(Request $request, $visita_id)
    {
        $visita = Visita::find($visita_id);

        if (!$visita) {
            return redirect()->route('calendarioIndex')->with('error', 'Visita no encontrada.');
        }

        // Validación con mensajes personalizados en español
        $validated = $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_cierre' => 'nullable|date|after:fecha_inicio',
            'estado' => 'required|in:Pendiente,En Progreso,Completada',
            'usuarios' => 'required|array',
            'usuarios.*' => 'exists:users,id',
        ], [
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_cierre.date' => 'La fecha de cierre debe ser una fecha válida.',
            'fecha_cierre.after' => 'La fecha de cierre debe ser mayor que la fecha de inicio.',
            'estado.required' => 'El estado es obligatorio.',
            'usuarios.required' => 'Debe seleccionar al menos un técnico.',
            'usuarios.*.exists' => 'Uno o más técnicos seleccionados no existen.',
        ]);


        // Actualizamos los campos de la visita
        $visita->update([
            'descripcion' => $request->descripcion,
            'estado' => $request->estado,
            'solucion' => $request->solucion,
        ]);

        // Sincronizar usuarios con las fechas
        $syncData = [];
        foreach ($request->usuarios as $userId) {
            $syncData[$userId] = [
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_cierre' => $request->fecha_cierre,
            ];
        }
        $visita->users()->sync($syncData);

        return redirect()->route('calendarioIndex')->with('success', 'Visita actualizada con éxito.');
    }


    public function create()
    {
        //
    }

    public function updateEvent(Request $request, $id)
    {
        // Validar los datos recibidos
        $validated = $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        // Obtener el evento de la base de datos
        $event = Visita::findOrFail($id);

        // Actualizar las fechas del evento
        $event->fecha_inicio = Carbon::parse($validated['start']);
        $event->fecha_cierre = Carbon::parse($validated['end']);

        // Guardar los cambios en la base de datos
        $event->save();

        // Retornar una respuesta JSON de éxito
        return response()->json(['message' => 'Evento actualizado correctamente']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function eliminarUsuario($visitaId, $usuarioId)
    {
        $visita = Visita::findOrFail($visitaId);

        // Verificar si el usuario pertenece a la visita
        if (!$visita->users()->where('user_id', $usuarioId)->exists()) {
            return response()->json(['success' => false, 'message' => 'El usuario no está asignado a esta visita.']);
        }

        // Eliminar relación en la tabla pivote
        $visita->users()->detach($usuarioId);

        return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente.']);
    }
}
