<?php

namespace Database\Factories;

use App\Models\Conversacion;
use Illuminate\Database\Eloquent\Factories\Factory;

class MensajeFactory extends Factory
{
    public function definition()
    {
        $tipos = ['cliente', 'agente', 'ia', 'sistema'];
        $tipoContenido = ['texto', 'imagen', 'audio', 'video', 'documento'];
        
        $mensajesCliente = [
            'Hola, necesito ayuda con mi servicio',
            'Buenos días, ¿pueden ayudarme?',
            'Tengo un problema con mi factura',
            'El servicio no funciona correctamente',
            '¿Cuándo pueden venir a revisar?',
            'Gracias por su atención',
            'Necesito cambiar mi plan',
            '¿Qué promociones tienen disponibles?',
            'Mi servicio está caído desde ayer',
            '¿Pueden darme más información?',
        ];
        
        $mensajesAgente = [
            'Hola, ¿cómo puedo ayudarte?',
            'Voy a revisar tu caso inmediatamente',
            'Perfecto, ya encontré tu información',
            'Un técnico se contactará contigo en 15 minutos',
            '¿Podrías darme más detalles del problema?',
            'Entendido, voy a procesar tu solicitud',
            'Gracias por la información proporcionada',
            'Tu servicio ya está activo nuevamente',
            '¿Algo más en lo que pueda ayudarte?',
            'Quedo atento a cualquier consulta',
        ];
        
        $mensajesIA = [
            'Hola, soy el asistente virtual, ¿cómo puedo ayudarte?',
            'He detectado que tu servicio presenta intermitencia',
            'Te sugiero reiniciar tu módem',
            'Ya he creado un ticket para tu problema',
            'Te confirmo que tu factura está al día',
            '¿Necesitas que te transfiera con un agente?',
            'He enviado un código de verificación a tu correo',
            'Tu consulta ha sido registrada correctamente',
            'Puedes seguir estos pasos para solucionarlo',
            'En breve un agente se comunicará contigo',
        ];
        
        $tipo = $this->faker->randomElement($tipos);
        
        switch ($tipo) {
            case 'cliente':
                $mensaje = $this->faker->randomElement($mensajesCliente);
                break;
            case 'agente':
                $mensaje = $this->faker->randomElement($mensajesAgente);
                break;
            case 'ia':
                $mensaje = $this->faker->randomElement($mensajesIA);
                break;
            default:
                $mensaje = 'Sistema: Mensaje automático';
                break;
        }
        
        $fechaMensaje = $this->faker->dateTimeBetween('-30 days', 'now');
        
        return [
            'conversacion_id' => Conversacion::inRandomOrder()->first()?->id ?? 1,
            'tipo' => $tipo,
            'tipo_contenido' => $this->faker->randomElement($tipoContenido),
            'mensaje' => $mensaje,
            'archivo_url' => null,
            'whatsapp_message_id' => $this->faker->uuid(),
            'estado_whatsapp' => $this->faker->randomElement(['enviado', 'entregado', 'leido', 'error']),
            'fecha_mensaje' => $fechaMensaje,
            'created_at' => $fechaMensaje,
            'updated_at' => $fechaMensaje,
        ];
    }
}