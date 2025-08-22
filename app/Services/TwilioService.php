<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $this->from = config('services.twilio.whatsapp_from');
        
        $this->client = new Client($sid, $token);
    }

    public function sendWhatsAppMessage(string $to, string $message)
    {
        try {
            $response = $this->client->messages->create(
                "whatsapp:{$to}",
                [
                    'from' => $this->from,
                    'body' => $message
                ]
            );
            
            return [
                'success' => true,
                'message' => 'Mensaje enviado correctamente',
                'data' => $response
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}