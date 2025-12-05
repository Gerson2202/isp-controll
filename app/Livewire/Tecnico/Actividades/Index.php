<?php

namespace App\Livewire\Tecnico\Actividades;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Visita;
use App\Models\Ticket;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public $fechaHoy;
    public $fechaManana;
    public $actividadesHoy = [];
    public $actividadesManana = [];
    public $mostrarManana = false;
    public $actividadSeleccionada = null; // Para el modal de detalles
    public $mostrarModal = false;

    public function mount()
    {
        $this->fechaHoy = Carbon::today();
        $this->fechaManana = Carbon::tomorrow();
        $this->cargarActividades();
    }

    public function cargarActividades()
    {
        $usuarioId = Auth::id();
        
        
        // CORRECCIÓN IMPORTANTE: Buscar por fecha_inicio en visita_user, no por created_at del ticket
        // Cargar actividades de hoy
        $visitasHoy = Visita::with(['ticket.cliente', 'users' => function($query) use ($usuarioId) {
            $query->where('user_id', $usuarioId);
        }])
        ->whereHas('users', function($query) use ($usuarioId) {
            $query->where('user_id', $usuarioId)
                  ->whereDate('fecha_inicio', $this->fechaHoy);
        })
        ->orderBy('created_at', 'asc')
        ->get();


        $this->actividadesHoy = $visitasHoy->map(function($visita) {
            return $this->formatearActividad($visita);
        })->toArray();

        // Cargar actividades de mañana
        $visitasManana = Visita::with(['ticket.cliente', 'users' => function($query) use ($usuarioId) {
            $query->where('user_id', $usuarioId);
        }])
        ->whereHas('users', function($query) use ($usuarioId) {
            $query->where('user_id', $usuarioId)
                  ->whereDate('fecha_inicio', $this->fechaManana);
        })
        ->orderBy('created_at', 'asc')
        ->get();


        $this->actividadesManana = $visitasManana->map(function($visita) {
            return $this->formatearActividad($visita);
        })->toArray();
    }

    private function formatearActividad($visita)
    {
        $userPivot = $visita->users->first()->pivot ?? null;
        $fechaInicio = $userPivot->fecha_inicio ?? null;
        $fechaCierre = $userPivot->fecha_cierre ?? null;
        
        return [
            'id' => $visita->id,
            'titulo' => $visita->titulo ?? 'Visita programada',
            'descripcion' => $visita->descripcion ?? 'Sin descripción',
            'solucion' => $visita->solucion ?? 'Sin solución',
            'observacion' => $visita->observacion ?? 'Sin observaciones',
            'estado' => $visita->estado,
            'estado_color' => $this->obtenerColorEstado($visita->estado),
            'hora_inicio' => $fechaInicio ? Carbon::parse($fechaInicio)->format('H:i') : '--:--',
            'hora_fin' => $fechaCierre ? Carbon::parse($fechaCierre)->format('H:i') : '--:--',
            'fecha_inicio' => $fechaInicio ? Carbon::parse($fechaInicio)->format('Y-m-d H:i:s') : null,
            'fecha_cierre' => $fechaCierre ? Carbon::parse($fechaCierre)->format('Y-m-d H:i:s') : null,
            'ticket' => $visita->ticket ? [
                'id' => $visita->ticket->id,
                'tipo_reporte' => $visita->ticket->tipo_reporte,
                'situacion' => $visita->ticket->situacion,
                'estado' => $visita->ticket->estado,
                'fecha_cierre' => $visita->ticket->fecha_cierre,
                'solucion' => $visita->ticket->solucion,
                'created_at' => $visita->ticket->created_at,
            ] : null,
            'cliente' => $visita->ticket && $visita->ticket->cliente ? [
                'id' => $visita->ticket->cliente->id,
                'nombre' => $visita->ticket->cliente->nombre,
                'telefono' => $visita->ticket->cliente->telefono,
                'cedula' => $visita->ticket->cliente->cedula,
                'direccion' => $visita->ticket->cliente->direccion ?? 'Sin dirección',
                'punto_referencia' => $visita->ticket->cliente->punto_referencia ?? 'Sin punto de referencia',
                'latitud' => $visita->ticket->cliente->latitud,
                'longitud' => $visita->ticket->cliente->longitud,
                'ip' => $visita->ticket->cliente->ip,
                'correo' => $visita->ticket->cliente->correo,
                'descripcion' => $visita->ticket->cliente->descripcion,
                'estado' => $visita->ticket->cliente->estado,
                'pool_id' => $visita->ticket->cliente->pool_id,
            ] : null,
            'visita_info' => [
                'created_at' => $visita->created_at->format('d/m/Y H:i'),
                'updated_at' => $visita->updated_at->format('d/m/Y H:i'),
            ]
        ];
    }

    private function obtenerColorEstado($estado)
    {
        return match($estado) {
            'Pendiente' => 'warning',
            'En progreso' => 'info',
            'Completada' => 'success',
            default => 'secondary',
        };
    }

    // Método para ver detalles
    public function verDetalles($actividadId)
    {
        // Buscar en ambas listas
        $actividad = collect($this->actividadesHoy)
            ->merge($this->actividadesManana)
            ->firstWhere('id', $actividadId);
        
        if ($actividad) {
            $this->actividadSeleccionada = $actividad;
            $this->mostrarModal = true;
        }
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->actividadSeleccionada = null;
    }

    public function toggleManana()
    {
        $this->mostrarManana = !$this->mostrarManana;
    }

    public function render()
    {
        return view('livewire.tecnico.actividades.index', [
            'fechaHoyFormateada' => $this->fechaHoy->format('d/m/Y'),
            'fechaMananaFormateada' => $this->fechaManana->format('d/m/Y'),
        ]);
    }
}