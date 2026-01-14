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
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TecnicoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VisitaController;
use Twilio\Rest\Client;

use App\Livewire\AgendarVisita;
// use GuzzleHttp\Psr7\Request;
use App\Livewire\Facturacion\PanelFacturacion;
use App\Livewire\Facturacion\ListaFacturas;
use App\Livewire\Facturacion\GenerarFacturasMensuales;
use App\Livewire\Facturacion\ProcesarCortes;
use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\Nodo;
use App\Models\Contrato;
use Illuminate\Http\Request;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Visita;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard', [
            'clientesCount' => Cliente::whereHas('contratos', function ($query) {
                $query->where('estado', 'activo');
            })->count(),
            'equiposCount' => Inventario::count(),
            'nodosCount' => Nodo::count(),
            'ticketsAbiertos' => Ticket::where('estado', 'Abierto')->count(),
            'ticketsRecientes' => Ticket::with('cliente')->latest()->take(5)->get(),
        ]);
    })->name('dashboard');


    // Rutas para Clientes
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientesIndex');
    Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientesCreate');
    Route::get('/clientes/search', [ClienteController::class, 'search'])->name('clientesBuscar');
    Route::get('/clientes/{id}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/clientes/grafica/{id}', [ClienteController::class, 'graficas'])->name('clientes.graficas');

    Route::put('/clientes/edit/{id}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::get('/clientes/{cliente}/historial-facturas', [ClienteController::class, 'historialFacturas'])->name('clientes.historial-facturas');
    Route::get('/clientes/imagenes/{id}', [ClienteController::class, 'imagenes'])->name('cliente.imagenes');


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

    // Ruta para página de bodega
    Route::get('/bodega', [InventarioController::class, 'bodegaIndex'])->name('bodegasIndex');

    // Ruta para consumibles
    Route::get('/Consumibles', [InventarioController::class, 'consumiblesIndex'])->name('consumiblesIndex');

    // Ruta para ver show bodegas
    Route::get('/bodegas/{bodega}', [InventarioController::class, 'showBodega'])->name('bodega.show');

    // Ruta para movimientos
    Route::get('/Movimientos', [InventarioController::class, 'movimientosIndex'])->name('movimientosIndex');

    // Ruta para dashboard de inventario
    Route::get('/inventario/dashboard', [InventarioController::class, 'dashboard'])->name('inventarioDashboard');

    // Ruta para detalle de inventario
    Route::get('/inventario/{tipo}/{id}', [InventarioController::class, 'detalle'])->name('inventario.detalle');

    // Ruta para historial de inventario
    Route::get('/inventario/Historial', [InventarioController::class, 'historial'])->name('inventarioHistorial');

    Route::get('/nodos', [NodoController::class, 'index'])->name('nodosIndex');
    Route::get('/nodos/detalle/{nodo}', [NodoController::class, 'show'])->name('nodos.show');
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

    // Ruta para visitas
    Route::post('/visitas', [VisitaController::class, 'store'])->name('visitas.store');
    Route::get('/visitas/{visita_id}/edit', [VisitaController::class, 'edit'])->name('visitas.edit');

    // Mostrar vista formulario para agregar usuarios
    Route::get('/visitas/{visita}/agregarUsuario', [VisitaController::class, 'agregarUsuario'])
        ->name('visitas.agregarUsuario');

    // Guardar los usuarios seleccionados
    Route::post('/visitas/{visita}/guardar-usuarios', [VisitaController::class, 'guardarUsuarios'])
        ->name('visitas.guardarUsuarios');

    Route::delete('/visitas/{visita}/usuarios/{usuario}', [VisitaController::class, 'eliminarUsuario'])
        ->name('visitas.eliminarUsuario');

    Route::put('/visitas/{visita_id}', [VisitaController::class, 'update'])->name('visitas.update');
    // Ruta para enviar a cola de programación (actualiza los campos a null)
    Route::put('/visitas/{visita}/enviar-a-cola', [VisitaController::class, 'enviarACola'])->name('enviarACola');
    // Ruta para ver las visitas sin programar
    Route::get('/visitas/cola', [VisitaController::class, 'colaDeProgramacion'])->name('visitas.cola');
    Route::put('/events/{id}', [VisitaController::class, 'updateEvent'])->name('events.update');
    // Ruta para ver pagina de tabla de visitas
    Route::get('/visitas/tabla', [VisitaController::class, 'visitasTabla'])->name('visitas.tabla');
    Route::get('/visitas/show/{id}', [VisitaController::class, 'visitasShow'])->name('visitas.show');



    // RUTAS PARA CALENDARIO
    // Ruta para consultar los eventos en el calendario(visitas)
    Route::get('/visitas/calendario', function () {
        $visitas = App\Models\Visita::with(['ticket.cliente.contrato.plan.nodo', 'users'])->get();

        $eventos = [];

        foreach ($visitas as $visita) {
            // Determinar el texto del ticket
            $ticketText = $visita->ticket_id ? 'Ticket #' . $visita->ticket_id : 'Visita #' . $visita->id;

            // Datos del cliente (con validaciones)
            $clienteNombre = 'No especificado';
            $clienteId = null;
            $latitud = null;
            $longitud = null;
            $clienteIp = 'No especificado';
            $clienteNodo = 'No especificado'; // Inicializar con valor por defecto

            if ($visita->ticket && $visita->ticket->cliente) {
                $clienteNombre = $visita->ticket->cliente->nombre ?? 'No especificado';
                $clienteId = $visita->ticket->cliente->id;
                $latitud = $visita->ticket->cliente->latitud;
                $longitud = $visita->ticket->cliente->longitud;
                $clienteIp = $visita->ticket->cliente->ip;

                // CORREGIR: Validar toda la cadena de relaciones
                if (
                    $visita->ticket->cliente->contrato &&
                    $visita->ticket->cliente->contrato->plan &&
                    $visita->ticket->cliente->contrato->plan->nodo
                ) {
                    $clienteNodo = $visita->ticket->cliente->contrato->plan->nodo->nombre;
                }
            }

            // Si la visita tiene usuarios asignados
            if ($visita->users->count() > 0) {
                foreach ($visita->users as $usuario) {
                    $eventos[] = [
                        'id' => $visita->id . '-' . $usuario->id, // id único por usuario
                        'title' => $ticketText . ' - ' . $usuario->name,
                        'titleVisita' => $visita->titulo,
                        'start' => $usuario->pivot->fecha_inicio,
                        'end'   => $usuario->pivot->fecha_cierre,
                        'descripcion' => $visita->descripcion,
                        'estado' => $visita->estado,
                        'ticket_id' => $visita->ticket_id,
                        'latitud' => $latitud,
                        'longitud' => $longitud,
                        'cliente_nombre' => $clienteNombre,
                        'ipCliente' => $clienteIp,
                        'nodoCliente' => $clienteNodo, // ← CORREGIDO: misma variable
                        'cliente_id' => $clienteId,
                        'usuario_id' => $usuario->id,
                        'usuario_nombre' => $usuario->name,
                        'color' => $visita->estado === 'Completada'
                            ? '#5A6268' // color fijo si está completada
                            : ($usuario->color ?? '#3AA8FF'), // color del usuario o uno por defecto
                    ];
                }
            } else {
                // Si no tiene usuarios asignados, mostrarla igual
                $eventos[] = [
                    'id' => $visita->id,
                    'title' => $ticketText . ' - Sin técnico',
                    'titleVisita' => $visita->titulo, // ← AGREGAR ESTO TAMBIÉN
                    'start' => $visita->fecha_inicio,
                    'end' => $visita->fecha_cierre,
                    'descripcion' => $visita->descripcion,
                    'estado' => $visita->estado,
                    'ticket_id' => $visita->ticket_id,
                    'latitud' => $latitud,
                    'longitud' => $longitud,
                    'cliente_nombre' => $clienteNombre,
                    'ipCliente' => $clienteIp, // ← AGREGAR ESTO
                    'nodoCliente' => $clienteNodo, // ← AGREGAR ESTO
                    'cliente_id' => $clienteId,
                    'color' => match ($visita->estado) {
                        'Pendiente' => '#28A745',
                        'En progreso' => '#FFC107',
                        'Completada' => '#5A6268',
                        default => '#3AA8FF'
                    }
                ];
            }
        }

        return response()->json($eventos);
    })->name('visitas.calendario');


    // Ruta para modificar los eventos en el calendario(visitas)

    Route::patch('/visitas/{visita}/actualizar-fechas', function ($visita, Request $request) {
        $visita = Visita::findOrFail($visita);

        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'fecha_inicio' => 'required|date',
            'fecha_cierre' => 'required|date',
        ]);

        // Actualizar solo la fila correspondiente en la tabla pivote
        $visita->users()->updateExistingPivot($request->user_id, [
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_cierre' => $request->fecha_cierre,
        ]);

        return response()->json(['success' => true]);
    })->middleware('auth');

    // incio de rutas de facturacion
    // Facturación
    Route::get('/facturacion/index', [FacturaController::class, 'index'])->name('facturacion.index');
    Route::get('/pagos/index', [PagoController::class, 'index'])->name('pagos.index');
    Route::get('/facturacion/dashboard', [FacturaController::class, 'dashboard'])->name('facturacion.dashboard');
    Route::get('/facturacion/corte', [FacturaController::class, 'cortes'])->name('facturacion.corte');

    //  RUTA PARA EXPORTAR EXCEL

    Route::get('/exportar-contratos-excel', function () {
        $contratos = Contrato::query()
            ->with(['cliente', 'plan.nodo'])
            ->join('clientes', 'clientes.id', '=', 'contratos.cliente_id')
            ->join('plans', 'plans.id', '=', 'contratos.plan_id')
            ->join('nodos', 'nodos.id', '=', 'plans.nodo_id')
            ->select([
                'contratos.*', // Esto trae todos los campos de contratos
                'clientes.nombre as cliente_nombre',
                'clientes.ip as cliente_ip',
                'plans.nombre as plan_nombre',
                'nodos.nombre as nodo_nombre'
            ])
            ->get()
            ->map(function ($contrato) {
                return [
                    'Cliente' => $contrato->cliente_nombre,
                    'Plan' => str_replace('_REHUSO', '', $contrato->plan_nombre),
                    'Tecnologia' => ucfirst($contrato->tecnologia),
                    'Precio' => number_format($contrato->precio, 0, ',', '.'),
                    'Fecha inicio' => $contrato->fecha_inicio ? date('d/m/Y', strtotime($contrato->fecha_inicio)) : 'N/A',
                    'Fecha Fin' => $contrato->fecha_fin ? date('d/m/Y', strtotime($contrato->fecha_fin)) : 'N/A',
                    'Estado' => ucfirst($contrato->estado), // Ahora sí viene de contratos
                    'IP' => $contrato->cliente_ip ?? 'sin ip',
                    'Nodo' => $contrato->nodo_nombre ?? 'sin Nodo',
                ];
            });

        return response()->json($contratos);
    });

    // RUTAS ADMINISTRACION  

    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');

    // Route::get('/test-whatsapp', function() {
    //         // Configuración de Twilio
    //         $sid = env('TWILIO_SID');
    //         $token = env('TWILIO_AUTH_TOKEN');
    //         $twilio = new Client($sid, $token);

    //         // Números (usa tu número sandbox de Twilio)
    //         $from = env('TWILIO_WHATSAPP_FROM'); // Ej: 'whatsapp:+14155238886'
    //         $to = 'whatsapp:+573215852059'; // Tu número de prueba

    //         // Crear mensaje con emojis
    //         $message = "✨ *Recordatorio de Pago* ✨\n\n" .
    //                 "📋 *Factura #:* INV-2023-789\n" .
    //                 "👤 *Cliente:* Cliente de Prueba\n" .
    //                 "📅 *Vencimiento:* " . now()->addDays(3)->format('d/m/Y') . "\n" .
    //                 "💰 *Total:* $1,500,000 COP\n\n" .
    //                 "🔗 [Pagar Ahora](https://tudominio.com/pagar)\n\n" .
    //                 "📲 *Métodos de pago:*\n" .
    //                 "💳 Tarjeta crédito/débito\n" .
    //                 "🏦 Transferencia bancaria\n" .
    //                 "📱 Nequi/Daviplata\n\n" .
    //                 "❓ ¿Necesitas ayuda? Escríbenos";

    //         try {
    //             // Enviar mensaje
    //             $response = $twilio->messages->create($to, [
    //                 'from' => $from,
    //                 'body' => $message
    //             ]);

    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Mensaje enviado correctamente',
    //                 'data' => [
    //                     'to' => $to,
    //                     'message' => $message,
    //                     'status' => $response->status
    //                 ]
    //             ]);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'success' => false,
    //                 'error' => $e->getMessage()
    //             ], 500);
    //         }
    // });

    // RUTAS AREA TECNICO
    Route::get('/tecnico', [TecnicoController::class, 'index'])->name('tecnico.index');
    Route::get('/tecnico/bodega', [TecnicoController::class, 'bodega'])->name('tecnico.bodega');
    Route::get('/tecnico/visitas', [TecnicoController::class, 'visitas'])->name('tecnico.visitas');
    Route::get('/tecnico/actividades', [TecnicoController::class, 'actividades'])->name('tecnico.actividades');
    Route::get('/tecnico/asignaciones', [TecnicoController::class, 'asignaciones'])->name('tecnico.asignaciones');

    Route::get('/tecnico/visitas/{visita}/cerrar', [TecnicoController::class, 'cerrar'])->name('tecnico.visitas.cerrar');
});
