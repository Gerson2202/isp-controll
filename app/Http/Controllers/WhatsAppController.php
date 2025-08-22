<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TwilioService;

class WhatsAppController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
            'message' => 'required|string'
        ]);

        $response = $this->twilioService->sendWhatsAppMessage(
            $request->to,
            $request->message
        );

        if ($response['success']) {
            return response()->json($response, 200);
        }

        return response()->json($response, 400);
    }
}
