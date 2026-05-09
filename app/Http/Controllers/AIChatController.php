<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;

class AIChatController extends Controller
{
    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500'
        ]);

        $service = new GeminiService();
        $respuesta = $service->ask($request->question);
        
        return response()->json([
            'success' => true,
            'answer' => $respuesta
        ]);
    }
}