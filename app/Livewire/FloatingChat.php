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

        // Guardar mensaje usuario
        $this->messages[] = [
            'role' => 'user',
            'content' => $this->prompt
        ];

        $userMessage = $this->prompt;

        // Limpiar input
        $this->prompt = '';

        try {

            // Enviar mensaje a n8n
            $response = Http::timeout(30)->post(
                'http://localhost:5678/webhook-test/chat-isp',
                [
                    'message' => $userMessage,
                ]
            );

            if ($response->successful()) {

                // Respuesta del webhook
                $data = $response->json();

                $botReply = $data['output'] ?? 'Sin respuesta';

                $this->messages[] = [
                    'role' => 'bot',
                    'content' => $botReply
                ];

            } else {

                $this->messages[] = [
                    'role' => 'bot',
                    'content' => '⚠️ Error comunicando con n8n'
                ];
            }

        } catch (\Exception $e) {

            $this->messages[] = [
                'role' => 'bot',
                'content' => '⚠️ n8n no responde'
            ];

            // Para debug
            logger()->error($e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.floating-chat');
    }
}