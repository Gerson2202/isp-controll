<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VisitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('calendario.index');
    }

    public function getEvents(Request $request)
    {
        $events = Visita::with(['ticket', 'ticket.cliente'])
            ->whereNotNull('fecha_inicio')  // Asegurarte de que 'fecha_inicio' no sea nulo
            ->whereNotNull('fecha_cierre')  // Asegurarte de que 'fecha_cierre' no sea nulo
            ->get();

        // Transforma los eventos para que sean compatibles con FullCalendar
        $formattedEvents = $events->map(function($event) {
            // Asignar color según el estado
            switch ($event->estado) {
                case 'Pendiente':
                    $color = 'green';  // Color verde
                    break;
                case 'En Progreso':
                    $color = 'blue';  // Color azul
                    break;
                case 'Completada':
                    $color = 'gray';  // Color gris
                    break;
                default:
                    $color = 'gray';  // Valor por defecto si no hay estado reconocido
            }

            // Obtener el tipo_reporte de la relación con Ticket
            $tipoReporte = $event->ticket ? $event->ticket->tipo_reporte : 'Sin tipo de reporte';
            $situacion = $event->ticket ? $event->ticket->situacion : 'Sin situación';
            $cliente = $event->ticket->cliente ? $event->ticket->cliente->nombre : 'Sin cliente';
            $estado = $event->estado;
            $solucion= $event->solucion ?? 'No se ha solucionado';
            
            return [
                'title' => $tipoReporte, // Mostrar el tipo_reporte en el título
                'start' => $event->fecha_inicio,
                'end' => $event->fecha_cierre,
                'color' => $color,  // Asignar el color basado en el estado
                'descripcion' => $event->descripcion,  // Información de la visita
                'ticket_id' => $event->ticket_id,  // ID del ticket
                'tipo_reporte' => $tipoReporte,  // Tipo de reporte
                'situacion' => $situacion,  // Situación
                'cliente' => $cliente,  // Nombre del cliente
                'estado' => $estado,  // estado de la visita  
                'visita_id' => $event->id,  // Asegúrate de incluir el ID de la visita
                'solucion' => $solucion, 
            ];
        });

        return response()->json($formattedEvents);
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
       
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_cierre' => 'required|date',
            'descripcion' => 'nullable|string',
            'estado' => 'required|string',
            'solucion' => 'nullable|string',
        ]);

        // Buscar la visita y actualizarla
        $visita = Visita::find($visita_id);

        if (!$visita) {
            return redirect()->route('events.index')->with('error', 'Visita no encontrada.');
        }

        // Actualizamos los campos de la visita
        $visita->update([
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_cierre' => $request->fecha_cierre,
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
