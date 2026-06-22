<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WhatsappController;
use App\Http\Controllers\ConversacionesController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route::post('/whatsapp/consulta', [WhatsappController::class, 'consulta']);
Route::get('/whatsapp/consulta/{cedula}', [WhatsappController::class, 'consulta']);

Route::prefix('whatsapp')->group(function () {
    // Consulta por cédula (GET o POST)
    Route::match(['get', 'post'], '/consulta', [WhatsappController::class, 'consulta']);
    Route::match(['get', 'post'], '/consulta/{cedula}', [WhatsappController::class, 'consulta']);

    // Validar si cliente existe
    Route::post('/validar-cliente', [WhatsappController::class, 'validarCliente']);

    // Resumen para WhatsApp formateado
    Route::post('/resumen-whatsapp', [WhatsappController::class, 'resumenWhatsapp']);
});

// Ruta Para recibir mensaje inicial de whatsApp desde n8n y guardarlo en la BD
Route::post('/webhook/chat/incoming', [ConversacionesController::class, 'storeIncoming']);
// Ruta Para recibir mensaje del segundo WeebhooK con mensaje de la IA desde n8n y guardarlo en la BD
Route::post('/webhook/chat/outgoing', [ConversacionesController::class, 'storeOutgoing']);
