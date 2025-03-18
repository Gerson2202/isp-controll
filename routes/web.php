<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\FotoTicketController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\NodoController;
use App\Http\Controllers\PoolController;

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
// Ruta para mostrar los clientes con IP nula
Route::get('/cliente/asignarip', [ClienteController::class, 'asignarIPindex'])->name('asignarIPindex');
// Ruta para mostrar el unico cliente y asigarle la ip
Route::get('/cliente/asignar/{id}', [ClienteController::class, 'asignarIpCliente'])->name('asignarIpCliente');

// Rutas para Planes
Route::get('/planes', [PlanController::class, 'index'])->name('planesIndex');
Route::get('/planes/create', [PlanController::class, 'create'])->name('planesCreate');

// Rutas para Tickets
Route::get('/tickets', [TicketController::class, 'index'])->name('ticketsIndex');

// Rutas para Fotos de Tickets
Route::get('/fotos-tickets', [FotoTicketController::class, 'index'])->name('fotosTicketsIndex');

// Rutas para Inventario
Route::get('/inventario', [InventarioController::class, 'index'])->name('inventarioIndex');


Route::get('/nodos', [NodoController::class, 'index'])->name('nodosIndex');
Route::get('/Nonitoreo', [NodoController::class, 'index1'])->name('MonitoreoIndex');
// Rutas para Contratos
Route::get('/contratos', [ContratoController::class, 'index'])->name('contratoIndex');
Route::get('asignar-contrato/{cliente}', [ContratoController::class, 'asignarContrato'])->name('asignarContrato');
Route::post('guardar-contrato', [ContratoController::class, 'guardarContrato'])->name('guardarContrato');

// Rutas para Pooles
// ---Vista Gestionar Pooles--
Route::get('/pooles', [PoolController::class, 'index'])->name('poolIndex');

});
