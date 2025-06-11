<?php

namespace App\Http\Controllers;
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
        return view('calendario.index');
    }

    // Enviar a pagina de tabla de visitas 
    public function visitasTabla()
    {
        return view('visitas.tabla');
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
        return view('visitas.edit', compact('visita'));
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
        // Obtener las visitas que están en cola (fecha_inicio y fecha_cierre son null)
        $visitasEnCola = Visita::whereNull('fecha_inicio')
            ->whereNull('fecha_cierre')
            ->get();

        // Pasar las visitas a la vista
        return view('visitas.tablaVisitasEncola', compact('visitasEnCola'));
    }

    // Actualizar la visita
    public function update(Request $request, $visita_id)
    {
        // Validar las fechas y los demás campos
        // $request->validate([
        //     'fecha_inicio' => 'required|date',
        //     'fecha_cierre' => 'required|date',
        //     'descripcion' => 'nullable|string',
        //     'estado' => 'required|string',
        //     'solucion' => 'nullable|string',
        // ]);

        // Buscar la visita y actualizarla
         $visita = Visita::find($visita_id);
        if (!$visita) {
            return redirect()->route('calendarioIndex')->with('error', 'Visita no encontrada.');
        }
        
        // Convertir las fechas a objetos Carbon para poder manipularlas
        $fecha_inicio = Carbon::parse($request->fecha_inicio);
        $fecha_cierre = Carbon::parse($request->fecha_cierre);

        // Validar que la fecha de inicio no sea igual a la fecha de cierre
        if ($fecha_inicio->equalTo($fecha_cierre)) {
            return redirect()->back()->with('error', 'La fecha de inicio no puede ser la misma que la fecha de cierre.');
        }

        // Validar que la fecha de cierre no sea menor que la fecha de inicio
        if ($fecha_cierre->lt($fecha_inicio)) {
            return redirect()->back()->with('error', 'La fecha de cierre no puede ser menor que la fecha de inicio.');
        }

        // Actualizamos los campos de la visita
        $visita->update([
            'fecha_inicio' => $fecha_inicio->format('Y-m-d H:i:s'), // Aseguramos que el formato es correcto
            'fecha_cierre' => $fecha_cierre->format('Y-m-d H:i:s'), // Aseguramos que el formato es correcto
            'descripcion' => $request->descripcion,
            'estado' => $request->estado,
            'solucion' => $request->solucion,
        ]);

        // Redirigir con éxito
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

    /**
     * Show the form for editing the specified resource.
     */
  

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    

}
