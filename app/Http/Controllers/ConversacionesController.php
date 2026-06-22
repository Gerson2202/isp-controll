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

        // 2. Buscar o crear la conversación usando el 'chatId' (telefono)
        $conversacion = Conversacion::updateOrCreate(
            ['telefono' => $request->telefono],
            [
                'nombre_contacto' => $request->nombre_contacto,
                'ultima_actividad' => Carbon::now(),
                // Se mantiene en estado 'ia' por defecto si es nueva
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

        // 2. Buscar la conversación existente
        $conversacion = Conversacion::findOrFail($request->conversacion_id);

        // 3. Actualizar la última actividad de la conversación
        $conversacion->update([
            'ultima_actividad' => \Carbon\Carbon::now()
        ]);

        // 4. Registrar el mensaje generado por la IA
        $conversacion->mensajes()->create([
            'tipo' => 'ia',
            'tipo_contenido' => 'texto',
            'mensaje' => $request->mensaje,
            'fecha_mensaje' => \Carbon\Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Respuesta de la IA almacenada correctamente.'
        ], 200);
    }
}
