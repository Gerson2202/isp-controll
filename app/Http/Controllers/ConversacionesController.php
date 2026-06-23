<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Conversacion;
use Carbon\Carbon;

class ConversacionesController extends Controller
{
    public function index()
    {
        return view('conversaciones.index');
    }

    public function storeIncoming(Request $request)
    {
        // 1. Validar los datos exactos que vienen de tu JSON de n8n
        $request->validate([
            'telefono' => 'required|string',
            'nombre_contacto' => 'nullable|string',
            'mensaje' => 'required|string',
            'whatsapp_message_id' => 'nullable|string',
        ]);

        // BLINDAJE: Normalizamos el teléfono eliminando el '+' si existe
        $telefonoNormalizado = ltrim($request->telefono, '+');

        // 2. Buscar o crear la conversación usando siempre el número limpio
        $conversacion = Conversacion::updateOrCreate(
            ['telefono' => $telefonoNormalizado],
            [
                'nombre_contacto' => $request->nombre_contacto,
                'ultima_actividad' => Carbon::now(),
                // Se mantiene en estado 'ia' por defecto si es nueva según tus esquemas
            ]
        );

        // 3. Registrar el mensaje en la tabla mensajes
        $conversacion->mensajes()->create([
            'tipo' => 'cliente',
            'tipo_contenido' => 'texto',
            'mensaje' => $request->mensaje,
            'whatsapp_message_id' => $request->whatsapp_message_id,
            'fecha_mensaje' => Carbon::now(),
        ]);

        // 4. Retornar el ID para el siguiente paso en n8n
        return response()->json([
            'success' => true,
            'conversacion_id' => $conversacion->id,
            'ia_activa' => $conversacion->ia_activa
        ], 200);
    }

    public function storeOutgoing(Request $request)
    {
        // 1. Validar los datos que vienen desde n8n
        $request->validate([
            'conversacion_id' => 'required|integer',
            'mensaje' => 'required|string',
        ]);

        // 2. Buscar la conversación existente (aquí ya busca directo por ID incremental)
        $conversacion = Conversacion::findOrFail($request->conversacion_id);

        // 3. Actualizar la última actividad de la conversación
        $conversacion->update([
            'ultima_actividad' => Carbon::now()
        ]);

        // 4. Registrar el mensaje generado por la IA
        $conversacion->mensajes()->create([
            'tipo' => 'ia',
            'tipo_contenido' => 'texto',
            'mensaje' => $request->mensaje,
            'fecha_mensaje' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Respuesta de la IA almacenada correctamente.'
        ], 200);
    }

    public function storeSystemMessage(Request $request)
    {
        // 1. Validar los datos de entrada
        $request->validate([
            'telefono' => 'required|string',
            'mensaje' => 'required|string',
            'whatsapp_message_id' => 'nullable|string',
        ]);

        // BLINDAJE: Normalizamos el teléfono eliminando el '+' si existe
        $telefonoNormalizado = ltrim($request->telefono, '+');

        // 2. Buscar o crear la conversación usando el número limpio
        $conversacion = Conversacion::updateOrCreate(
            ['telefono' => $telefonoNormalizado],
            [
                'ultima_actividad' => Carbon::now(),
            ]
        );

        // 3. Registrar el mensaje automático en la tabla mensajes
        $conversacion->mensajes()->create([
            'tipo' => 'sistema', // Identifica notificación automática de tu plataforma
            'tipo_contenido' => 'texto',
            'mensaje' => $request->mensaje,
            'whatsapp_message_id' => $request->whatsapp_message_id,
            'estado_whatsapp' => 'enviado', // Ya sabemos que WhatsApp lo aceptó en n8n
            'fecha_mensaje' => Carbon::now(),
        ]);

        return response()->json(['success' => true]);
    }
}
