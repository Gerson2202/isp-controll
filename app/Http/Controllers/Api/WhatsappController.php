<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    public function consulta(Request $request, $cedula = null)
    {
        // Obtener cédula del parámetro GET, POST o URL
        $cedula = $cedula ?? $request->input('cedula');

        if (!$cedula) {
            return response()->json([
                'success' => false,
                'message' => 'Cédula requerida',
                'code' => 'CEDULA_REQUERIDA'
            ], 400);
        }

        try {
            $cliente = Cliente::where('cedula', $cedula)
                ->with([
                    'contratos.plan',
                    'contratos.facturas'
                ])
                ->first();

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado',
                    'code' => 'CLIENTE_NO_ENCONTRADO'
                ], 404);
            }

            $contrato = $cliente->contratos->first();

            if (!$contrato) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente sin contrato',
                    'code' => 'SIN_CONTRATO'
                ], 404);
            }

            $plan = $contrato->plan;

            $facturasPendientes = $contrato->facturas
                ->whereIn('estado', ['pendiente', 'vencida']);

            $deudaTotal = $facturasPendientes->sum('saldo_pendiente');

            $proximaFactura = $facturasPendientes
                ->sortBy('fecha_vencimiento')
                ->first();

            // Formatear mensaje para WhatsApp
            $mensaje = "📋 *INFORMACIÓN DE SU CUENTA*\n\n";
            $mensaje .= "👤 *Cliente:* " . $cliente->nombre . "\n";
            $mensaje .= "📱 *Teléfono:* " . $cliente->telefono . "\n";
            $mensaje .= "🌐 *Plan:* " . ($plan?->nombre ?? 'N/A') . "\n";
            $mensaje .= "⚡ *Velocidad:* " . ($plan?->velocidad_bajada ?? 'N/A') . " Mbps ↓\n";

            if ($deudaTotal > 0) {
                $mensaje .= "\n⚠️ *DEUDA PENDIENTE*\n";
                $mensaje .= "💰 *Monto:* $" . number_format($deudaTotal, 2) . "\n";
                $mensaje .= "📅 *Vencimiento:* " . ($proximaFactura?->fecha_vencimiento ?? 'N/A') . "\n";
            } else {
                $mensaje .= "\n✅ *Su cuenta está al día*\n";
            }

            return response()->json([
                'success' => true,
                'mensaje_formateado' => $mensaje,
                'cliente' => [
                    'nombre' => $cliente->nombre,
                    'telefono' => $cliente->telefono,
                    'cedula' => $cliente->cedula,
                    'plan' => $plan?->nombre,
                    'velocidad_bajada' => $plan?->velocidad_bajada,
                    'velocidad_subida' => $plan?->velocidad_subida,
                    'deuda_total' => $deudaTotal,
                    'fecha_vencimiento' => optional($proximaFactura)->fecha_vencimiento,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'code' => 'ERROR_CONSULTA'
            ], 500);
        }
    }
}