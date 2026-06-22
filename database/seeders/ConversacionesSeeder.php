<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conversacion;
use App\Models\Mensaje;
use App\Models\Cliente;
use App\Models\User;
use Carbon\Carbon;

class ConversacionesSeeder extends Seeder
{
    public function run()
    {
        // Obtener clientes existentes
        $clientes = Cliente::all();
        
        if ($clientes->isEmpty()) {
            $this->command->error('❌ No hay clientes. Ejecuta primero ClienteSeeder.');
            return;
        }
        
        // Obtener usuarios existentes
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->error('❌ No hay usuarios. Ejecuta primero UserSeeder.');
            return;
        }
        
        // Datos de conversaciones realistas
        $conversacionesData = [
            [
                'nombre' => 'Gerson Mojica',
                'telefono' => '3001234567',
                'estado' => 'abierto',
                'cliente_index' => 0,
                'mensajes' => [
                    ['tipo' => 'cliente', 'texto' => 'Hola, necesito ayuda con mi servicio de internet, lleva dos días fallando', 'fecha' => '-2 days'],
                    ['tipo' => 'ia', 'texto' => 'Hola Gerson, soy el asistente virtual. ¿Podrías describir el problema específicamente?', 'fecha' => '-2 days +2 hours'],
                    ['tipo' => 'cliente', 'texto' => 'El internet se corta cada 10 minutos y tengo que reiniciar el módem', 'fecha' => '-2 days +3 hours'],
                    ['tipo' => 'ia', 'texto' => 'Entendido. Voy a revisar tu conexión y crear un ticket de soporte', 'fecha' => '-2 days +4 hours'],
                    ['tipo' => 'agente', 'texto' => 'Hola Gerson, soy Carlos del equipo técnico. Ya revisé tu caso y veo que hay una falla en tu zona', 'fecha' => '-1 day'],
                    ['tipo' => 'cliente', 'texto' => '¿Cuándo pueden solucionarlo? Necesito trabajar desde casa', 'fecha' => '-1 day +2 hours'],
                    ['tipo' => 'agente', 'texto' => 'Un técnico estará en tu zona mañana entre 8-10am para revisar la conexión', 'fecha' => '-1 day +3 hours'],
                    ['tipo' => 'cliente', 'texto' => 'Perfecto, estaré pendiente. Muchas gracias', 'fecha' => '-1 day +4 hours'],
                    ['tipo' => 'agente', 'texto' => '¡Excelente! Te confirmamos que el técnico ya está en camino', 'fecha' => 'now -1 hour'],
                    ['tipo' => 'cliente', 'texto' => '¡Ya llegó! Gracias por la pronta atención', 'fecha' => 'now'],
                ]
            ],
            [
                'nombre' => 'Ana Rodríguez',
                'telefono' => '3109876543',
                'estado' => 'cerrado',
                'cliente_index' => 1,
                'mensajes' => [
                    ['tipo' => 'cliente', 'texto' => 'Buenos días, quiero consultar sobre mi factura de este mes', 'fecha' => '-5 days'],
                    ['tipo' => 'ia', 'texto' => 'Hola Ana, te ayudo con tu factura. ¿Tienes el número de tu contrato?', 'fecha' => '-5 days +1 hour'],
                    ['tipo' => 'cliente', 'texto' => 'Sí, mi contrato es 123456789', 'fecha' => '-5 days +2 hours'],
                    ['tipo' => 'ia', 'texto' => 'Gracias. Tu factura de este mes es de $85.000 y vence en 5 días', 'fecha' => '-5 days +3 hours'],
                    ['tipo' => 'agente', 'texto' => 'Hola Ana, soy María de facturación. Veo que tienes un descuento por pronto pago', 'fecha' => '-4 days'],
                    ['tipo' => 'cliente', 'texto' => '¿Cuánto sería con el descuento?', 'fecha' => '-4 days +2 hours'],
                    ['tipo' => 'agente', 'texto' => 'Con el descuento te quedaría en $76.500, ¿deseas proceder con el pago?', 'fecha' => '-4 days +3 hours'],
                    ['tipo' => 'cliente', 'texto' => 'Sí, voy a pagar ahora mismo', 'fecha' => '-4 days +4 hours'],
                    ['tipo' => 'agente', 'texto' => '¡Perfecto! Tu pago ha sido registrado exitosamente', 'fecha' => '-3 days'],
                    ['tipo' => 'sistema', 'texto' => 'Conversación cerrada automáticamente por inactividad', 'fecha' => '-1 day'],
                ]
            ],
            [
                'nombre' => 'María Pérez',
                'telefono' => '3204567890',
                'estado' => 'agente',
                'cliente_index' => 2,
                'mensajes' => [
                    ['tipo' => 'cliente', 'texto' => 'Mi servicio de televisión no tiene señal desde ayer', 'fecha' => '-3 days'],
                    ['tipo' => 'ia', 'texto' => 'Hola María, lamento el inconveniente. Voy a verificar tu servicio', 'fecha' => '-3 days +1 hour'],
                    ['tipo' => 'cliente', 'texto' => 'Ya reinicié el decodificador pero sigue igual', 'fecha' => '-3 days +2 hours'],
                    ['tipo' => 'agente', 'texto' => 'Hola María, soy Pedro del soporte técnico. Vamos a hacer un reinicio remoto', 'fecha' => '-2 days'],
                    ['tipo' => 'cliente', 'texto' => '¿Funcionaría? Es que ya intenté de todo', 'fecha' => '-2 days +1 hour'],
                    ['tipo' => 'agente', 'texto' => 'Sí, he visto que hay una actualización pendiente en tu decodificador', 'fecha' => '-2 days +2 hours'],
                    ['tipo' => 'cliente', 'texto' => '¡Excelente! Ya se actualizó y funciona perfecto', 'fecha' => '-2 days +3 hours'],
                    ['tipo' => 'agente', 'texto' => 'Me alegra mucho. ¿Necesitas ayuda con algo más?', 'fecha' => '-2 days +4 hours'],
                    ['tipo' => 'cliente', 'texto' => 'Todo bien, muchas gracias por tu ayuda', 'fecha' => '-2 days +5 hours'],
                ]
            ],
            [
                'nombre' => 'Juan Carlos López',
                'telefono' => '3507891234',
                'estado' => 'ia',
                'cliente_index' => 3,
                'mensajes' => [
                    ['tipo' => 'cliente', 'texto' => 'Hola, quiero cambiar mi plan a uno más económico', 'fecha' => '-1 days'],
                    ['tipo' => 'ia', 'texto' => 'Hola Juan Carlos, te ayudo con el cambio de plan. ¿Cuál es tu plan actual?', 'fecha' => '-1 days +1 hour'],
                    ['tipo' => 'cliente', 'texto' => 'Tengo el plan Premium, pero ya no lo necesito', 'fecha' => '-1 days +2 hours'],
                    ['tipo' => 'ia', 'texto' => 'Entendido. Tenemos disponibles los planes Básico ($40.000) y Estándar ($55.000)', 'fecha' => '-1 days +3 hours'],
                    ['tipo' => 'cliente', 'texto' => 'Me interesa el plan Básico, ¿cómo hago el cambio?', 'fecha' => '-1 days +4 hours'],
                    ['tipo' => 'ia', 'texto' => 'Te enviaré un enlace al WhatsApp para que confirmes el cambio', 'fecha' => '-1 days +5 hours'],
                    ['tipo' => 'cliente', 'texto' => '¡Listo! Ya confirmé el cambio en el enlace', 'fecha' => 'now -2 hours'],
                    ['tipo' => 'ia', 'texto' => '¡Perfecto! Tu plan ha sido cambiado exitosamente', 'fecha' => 'now -1 hour'],
                ]
            ],
            [
                'nombre' => 'Laura Martínez',
                'telefono' => '3105678901',
                'estado' => 'abierto',
                'cliente_index' => 4,
                'mensajes' => [
                    ['tipo' => 'cliente', 'texto' => 'Buenas tardes, necesito reportar una falla en el servicio', 'fecha' => '-4 hours'],
                    ['tipo' => 'ia', 'texto' => 'Hola Laura, lamento el inconveniente. ¿Puedes describir la falla?', 'fecha' => '-3 hours'],
                    ['tipo' => 'cliente', 'texto' => 'El internet no funciona, el módem se queda en rojo', 'fecha' => '-3 hours +30 minutes'],
                    ['tipo' => 'ia', 'texto' => 'Estamos detectando una falla en tu sector, ya estamos trabajando en ello', 'fecha' => '-2 hours'],
                    ['tipo' => 'cliente', 'texto' => '¿Cuánto tiempo va a durar?', 'fecha' => '-2 hours +15 minutes'],
                    ['tipo' => 'agente', 'texto' => 'Hola Laura, soy Miguel del equipo técnico. La falla es general en tu sector', 'fecha' => '-1 hour'],
                    ['tipo' => 'agente', 'texto' => 'El tiempo estimado de solución es de 2 horas. Te mantendremos informada', 'fecha' => '-1 hour +30 minutes'],
                ]
            ]
        ];
        
        foreach ($conversacionesData as $data) {
            // Obtener el cliente por índice
            $cliente = $clientes->get($data['cliente_index']);
            
            if (!$cliente) {
                $this->command->warn("⚠️ Cliente no encontrado para: {$data['nombre']}");
                continue;
            }
            
            // Asignar a un usuario aleatorio si el estado es 'agente'
            $asignadoA = null;
            if ($data['estado'] === 'agente') {
                $asignadoA = $users->random()->id;
            }
            
            // Crear conversación
            $conversacion = Conversacion::create([
                'cliente_id' => $cliente->id,
                'telefono' => $data['telefono'],
                'nombre_contacto' => $data['nombre'],
                'estado' => $data['estado'],
                'ia_activa' => $data['estado'] === 'ia' || $data['estado'] === 'abierto',
                'asignado_a' => $asignadoA,
                'ultima_actividad' => now(),
                'created_at' => now()->subDays(rand(1, 10)),
            ]);
            
            // Crear mensajes
            foreach ($data['mensajes'] as $mensajeData) {
                $fecha = $this->parseFecha($mensajeData['fecha']);
                
                Mensaje::create([
                    'conversacion_id' => $conversacion->id,
                    'tipo' => $mensajeData['tipo'],
                    'tipo_contenido' => 'texto',
                    'mensaje' => $mensajeData['texto'],
                    'archivo_url' => null,
                    'whatsapp_message_id' => 'wamid.' . rand(100000, 999999) . '.' . time(),
                    'estado_whatsapp' => $this->getEstadoWhatsapp($mensajeData['tipo']),
                    'fecha_mensaje' => $fecha,
                    'created_at' => $fecha,
                    'updated_at' => $fecha,
                ]);
            }
            
            // Actualizar última actividad de la conversación
            $ultimoMensaje = $conversacion->mensajes()->latest('fecha_mensaje')->first();
            if ($ultimoMensaje) {
                $conversacion->ultima_actividad = $ultimoMensaje->fecha_mensaje;
                $conversacion->save();
            }
        }
        
        $this->command->info('✅ Se crearon ' . count($conversacionesData) . ' conversaciones con sus mensajes');
    }
    
    private function parseFecha($fechaStr)
    {
        $now = Carbon::now();
        
        switch ($fechaStr) {
            case 'now':
                return $now;
            case 'now -1 hour':
                return $now->copy()->subHour();
            case 'now -2 hours':
                return $now->copy()->subHours(2);
            case 'now -1 day':
                return $now->copy()->subDay();
            case 'now -2 days':
                return $now->copy()->subDays(2);
            case 'now -3 days':
                return $now->copy()->subDays(3);
            case 'now -4 days':
                return $now->copy()->subDays(4);
            case 'now -5 days':
                return $now->copy()->subDays(5);
            default:
                // Para fechas con incrementos como '-2 days +2 hours'
                if (strpos($fechaStr, '+') !== false) {
                    $parts = explode(' +', $fechaStr);
                    $base = $this->parseFecha($parts[0]);
                    $parts2 = explode(' ', $parts[1]);
                    $value = (int)$parts2[0];
                    $unit = $parts2[1];
                    
                    switch ($unit) {
                        case 'hours':
                        case 'hour':
                            return $base->copy()->addHours($value);
                        case 'minutes':
                        case 'minute':
                            return $base->copy()->addMinutes($value);
                        default:
                            return $base;
                    }
                }
                
                // Para fechas base
                if (strpos($fechaStr, 'days') !== false) {
                    $parts = explode(' ', $fechaStr);
                    $value = (int)$parts[0];
                    return $now->copy()->subDays(abs($value));
                }
                
                if (strpos($fechaStr, 'hours') !== false || strpos($fechaStr, 'hour') !== false) {
                    $parts = explode(' ', $fechaStr);
                    $value = (int)$parts[0];
                    return $now->copy()->subHours(abs($value));
                }
                
                return $now;
        }
    }
    
    private function getEstadoWhatsapp($tipo)
    {
        $estados = ['enviado', 'entregado', 'leido'];
        if ($tipo === 'cliente') {
            return 'leido';
        }
        return $estados[array_rand($estados)];
    }
}