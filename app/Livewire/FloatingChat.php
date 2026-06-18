<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class FloatingChat extends Component
{
    public $isOpen = false;
    public $prompt = '';
    public $messages = [];

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function sendMessage()
    {
        if (trim($this->prompt) === '') {
            return;
        }

        // Guardar mensaje del usuario en el historial local
        $this->messages[] = [
            'role' => 'user',
            'content' => $this->prompt
        ];

        $userMessage = $this->prompt;

        // Limpiar el input inmediatamente para mejorar la experiencia del usuario
        $this->prompt = '';

        try {
            // ENVIAR MENSAJE AL WEBHOOK DE N8N EN EASYPANEL
            $response = Http::timeout(45)
                ->withoutVerifying() // ◄— Agrega esto para saltar bloqueos de SSL del VPS
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post('https://automatizacion-isprotik1-n8n.ijnhto.easypanel.host/webhook/chat-isp', [
                    'message'   => $userMessage,
                    'sessionId' => session()->getId(),
                ]);

            if ($response->successful()) {
                $data = $response->json();

                // n8n mapea el resultado del AI Agent en la propiedad 'output'
                $botReply = $data['output'] ?? 'Sin respuesta del asistente.';

                $this->messages[] = [
                    'role' => 'bot',
                    'content' => $botReply
                ];
            } else {
                $this->messages[] = [
                    'role' => 'bot',
                    'content' => '⚠️ Error de comunicación con el asistente técnico.'
                ];
            }
        } catch (\Exception $e) {
            $this->messages[] = [
                'role' => 'bot',
                'content' => '⚠️ El servicio de asistencia no se encuentra disponible.'
            ];

            // Guardar el error en los logs de Laravel para debuguear en el VPS
            logger()->error("Error en FloatingChat con n8n: " . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.floating-chat');
    }
}
