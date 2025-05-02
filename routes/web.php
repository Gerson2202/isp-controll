<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\FotoTicketController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\NodoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PoolController;
use App\Http\Controllers\VisitaController;
use App\Livewire\AgendarVisita;
use GuzzleHttp\Psr7\Request;
use App\Livewire\Facturacion\PanelFacturacion;
use App\Livewire\Facturacion\ListaFacturas;
use App\Livewire\Facturacion\GenerarFacturasMensuales;
use App\Livewire\Facturacion\ProcesarCortes;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    
// Rutas para Clientes
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientesIndex');
Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientesCreate');
Route::get('/clientes/search', [ClienteController::class, 'search'])->name('clientesBuscar');
Route::get('/clientes/{id}', [ClienteController::class, 'show'])->name('clientes.show');
Route::put('/clientes/edit/{id}', [ClienteController::class, 'update'])->name('clientes.update');
Route::get('/clientes/{cliente}/historial-facturas', [ClienteController::class, 'historialFacturas'])->name('clientes.historial-facturas');


// Ruta para mostrar los clientes con IP nula
Route::get('/cliente/asignarip', [ClienteController::class, 'asignarIPindex'])->name('asignarIPindex');
// Ruta para mostrar el unico cliente y asigarle la ip
Route::get('/cliente/asignar/{id}', [ClienteController::class, 'asignarIpCliente'])->name('asignarIpCliente');

// Rutas para Planes
Route::get('/planes', [PlanController::class, 'index'])->name('planesIndex');
Route::get('/planes/create', [PlanController::class, 'create'])->name('planesCreate');

// Rutas para Tickets
Route::get('/tickets', [TicketController::class, 'index'])->name('ticketsIndex');
Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
Route::get('/tickets/historial', [TicketController::class, 'Tablahistorial'])->name('tickets.historial');

// Rutas para Fotos de Tickets
Route::get('/fotos-tickets', [FotoTicketController::class, 'index'])->name('fotosTicketsIndex');

// Rutas para Inventario
Route::get('/inventario', [InventarioController::class, 'index'])->name('inventarioIndex');


Route::get('/nodos', [NodoController::class, 'index'])->name('nodosIndex');
Route::get('/Nonitoreo', [NodoController::class, 'index1'])->name('MonitoreoIndex');
// Rutas para Contratos
Route::get('/contratos', [ContratoController::class, 'index'])->name('contratoIndex');
Route::get('/contratos/list', [ContratoController::class, 'list'])->name('contratos.list');
Route::get('/asignar-contrato/{cliente}', [ContratoController::class, 'asignarContrato'])->name('asignarContrato');
Route::post('/guardar-contrato', [ContratoController::class, 'guardarContrato'])->name('guardarContrato');

// Rutas para Pooles
// ---Vista Gestionar Pooles--
Route::get('/pooles', [PoolController::class, 'index'])->name('poolIndex');

// Rutas para inventarios
// ---Vista para formulario--
Route::get('/Inventario', [InventarioController::class, 'index'])->name('inventarioIndex');
Route::get('/Modelo', [InventarioController::class, 'ModeloIndex'])->name('ModeloIndex');
Route::get('/equiposlist', [InventarioController::class, 'list'])->name('inventarioList');
Route::get('/equipos/{id}', [InventarioController::class, 'show'])->name('equipos.show');

// Route::get('/agendar-visita', AgendarVisita::class)->name('agendar.visita');
Route::get('/calendario', [VisitaController::class, 'index'])->name('calendarioIndex');
Route::get('/events', [VisitaController::class, 'getEvents'])->name('events.index');

// Ruta para editar la visita
Route::get('/visitas/{visita_id}/edit', [VisitaController::class, 'edit'])->name('visitas.edit');
Route::put('/visitas/{visita_id}', [VisitaController::class, 'update'])->name('visitas.update');
// Ruta para enviar a cola de programación (actualiza los campos a null)
Route::put('/visitas/{visita}/enviar-a-cola', [VisitaController::class, 'enviarACola'])->name('enviarACola');
// Ruta para ver las visitas sin programar
Route::get('/visitas/cola', [VisitaController::class, 'colaDeProgramacion'])->name('visitas.cola');
Route::put('/events/{id}', [VisitaController::class, 'updateEvent'])->name('events.update');


// RUTAS PARA CALENDARIO
// Ruta para consultar los eventos en el calendario(visitas)
Route::get('/visitas/calendario', function() {
    $visitas = App\Models\Visita::with(['ticket', 'ticket.cliente'])->get();
    
    return response()->json($visitas->map(function($visita) {
        return [
            'id' => $visita->id,
            'title' => 'Ticket #' . $visita->ticket_id . ' - ' . optional($visita->ticket->cliente)->nombre ?? 'Sin cliente',
            'start' => $visita->fecha_inicio,
            'end' => $visita->fecha_cierre,
            'descripcion' => $visita->descripcion,
            'estado' => $visita->estado,
            'ticket_id' => $visita->ticket_id,
            'cliente_nombre' => optional($visita->ticket->cliente)->nombre ?? 'No especificado',
            'cliente_id' => optional($visita->ticket->cliente)->id, // Nuevo campo
            'color' => match($visita->estado) {
                'Pendiente' => '#28A745',
                'En progreso' => '#FFC107',
                'Completada' => '#5A6268',
                default => '#3AA8FF'
            }
        ];
    }));
})->name('visitas.calendario');

// Ruta para modificar los eventos en el calendario(visitas)

Route::patch('/visitas/{visita}/actualizar-fechas', function($visita) {
    $visita = App\Models\Visita::findOrFail($visita);
    
    $visita->update([
        'fecha_inicio' => request('fecha_inicio'),
        'fecha_cierre' => request('fecha_cierre')
    ]);

    return response()->json(['success' => true]);
})->middleware('auth');



// incio de rutas de facturacion
 // Facturación
 Route::get('/facturacion/index', [FacturaController::class, 'index'])->name('facturacion.index');
 Route::get('/pagos/index', [PagoController::class, 'index'])->name('pagos.index');
 Route::get('/facturacion/dashboard', [FacturaController::class, 'dashboard'])->name('facturacion.dashboard');
 Route::get('/facturacion/corte', [FacturaController::class, 'cortes'])->name('facturacion.corte');


//  Route::prefix('facturacion')->group(function () {
//     Route::get('/panel', PanelFacturacion::class)->name('facturacion.panel');
//     Route::get('/facturas', ListaFacturas::class)->name('facturas.index');
//     Route::get('/generar-facturas', GenerarFacturasMensuales::class)->name('facturas.generar');
//     Route::get('/procesar-cortes', ProcesarCortes::class)->name('facturas.cortes');
//     });

});
