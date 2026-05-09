<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class GeminiService
{
    public function ask($question)
    {
        try {

            // 🔥 1. Detectar módulo (mejor precisión)
            $modulo = $this->detectarModulo($question);

            // 🔥 2. Construir prompt inteligente
            $mensaje = $this->buildPrompt($question, $modulo);

            // 🔥 3. Llamar a Gemini (FIX: guardas respuesta)
            $respuesta = Http::timeout(30)->withHeaders([
                'x-goog-api-key' => env('GEMINI_API_KEY'),
                'Content-Type' => 'application/json'
            ])->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent',
                [
                    'contents' => [
                        ['parts' => [['text' => $mensaje]]]
                    ]
                ]
            );

            // 🔥 4. Manejo de errores real
            if ($respuesta->failed()) {
                return "❌ Error Gemini:\nStatus: " . $respuesta->status() .
                    "\nBody: " . $respuesta->body();
            }

            // 🔥 5. Obtener SQL
            $sql = $respuesta->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // limpiar markdown
            $sql = preg_replace('/```sql\n?(.*?)\n?```/s', '$1', $sql);
            $sql = preg_replace('/```\n?(.*?)\n?```/s', '$1', $sql);

            // limpiar espacios
            $sql = trim($sql);
            $sql = str_replace(["\n", "\r", "\t"], " ", $sql);
            $sql = preg_replace('/\s+/', ' ', $sql);
            // 🔥 eliminar punto y coma que rompe LIMIT
            $sql = str_replace(';', '', $sql);
            // 🔒 6. Validar SQL
            $sql = $this->validarSQL($sql);

            // ⚡ 7. Ejecutar
            $resultados = DB::select($sql);

            // 💬 8. Formatear
            return $this->formatResults($resultados, $sql);
        } catch (\Exception $e) {
            return "❌ Error: " . $e->getMessage();
        }
    }

    // 🧠 Detecta intención
    private function detectarModulo($question)
    {
        $q = strtolower($question);

        if (str_contains($q, 'pago')) return 'pagos';
        if (str_contains($q, 'factura')) return 'facturacion';
        if (str_contains($q, 'cliente')) return 'clientes';
        if (str_contains($q, 'contrato')) return 'contratos';
        if (str_contains($q, 'plan')) return 'planes';
        if (str_contains($q, 'nodo')) return 'red';
        if (str_contains($q, 'ticket')) return 'soporte';

        return 'general';
    }

    // 🧠 Prompt PRO optimizado
    private function buildPrompt($question, $modulo)
    {
        $contextoExtra = '';

        if ($modulo === 'pagos') {
            $contextoExtra = "Enfócate en pagos, facturas y clientes.";
        } elseif ($modulo === 'clientes') {
            $contextoExtra = "Enfócate en clientes y contratos.";
        } elseif ($modulo === 'facturacion') {
            $contextoExtra = "Enfócate en facturas, pagos y estado de deuda.";
        }

        return "
        Eres un experto en SQL para MySQL especializado en sistemas ISP.

        $contextoExtra

        BASE DE DATOS:

        TABLAS:
        - clientes (id, nombre, telefono, direccion, estado, correo)
        - contratos (id, cliente_id, plan_id, estado, fecha_inicio, precio)
        - facturas (id, contrato_id, fecha_emision, fecha_vencimiento, monto_total, saldo_pendiente, estado)
        - pagos (id, factura_id, monto, fecha_pago)
        - plans (id, nombre, precio, velocidad_bajada, velocidad_subida)
        - nodos (id, nombre, ip)
        - tickets (id, cliente_id, estado, fecha_cierre)

        RELACIONES:
        - clientes.id = contratos.cliente_id
        - contratos.id = facturas.contrato_id
        - facturas.id = pagos.factura_id
        - contratos.plan_id = plans.id
        - plans.nodo_id = nodos.id
        - tickets.cliente_id = clientes.id

        REGLAS:
        1. SIEMPRE usar JOIN correcto
        2. pagos NO tiene cliente_id
        3. Para obtener cliente desde pagos:
        pagos → facturas → contratos → clientes
        4. NO usar SELECT *
        5. SIEMPRE usar LIMIT 50
        6. Usar columnas reales (clientes.correo NO email)
        7. Evitar consultas pesadas sin WHERE

        EJEMPLOS:

        Último pago:
        SELECT clientes.nombre, pagos.monto, pagos.fecha_pago
        FROM pagos
        JOIN facturas ON pagos.factura_id = facturas.id
        JOIN contratos ON facturas.contrato_id = contratos.id
        JOIN clientes ON contratos.cliente_id = clientes.id
        ORDER BY pagos.fecha_pago DESC
        LIMIT 1;

        Clientes en mora:
        SELECT clientes.nombre, facturas.saldo_pendiente
        FROM facturas
        JOIN contratos ON facturas.contrato_id = contratos.id
        JOIN clientes ON contratos.cliente_id = clientes.id
        WHERE facturas.estado = 'vencida'
        LIMIT 50;

        Pregunta: $question

        Responde SOLO con SQL.
        ";
    }

    // 🔒 Seguridad PRO
    private function validarSQL($sql)
    {
        $bloqueadas = ['SLEEP', 'BENCHMARK', 'OUTFILE', 'LOAD_FILE'];

        foreach ($bloqueadas as $bad) {
            if (stripos($sql, $bad) !== false) {
                throw new \Exception("Consulta no permitida");
            }
        }

        if (!preg_match('/^\s*SELECT/i', $sql)) {
            throw new \Exception("Solo SELECT permitido");
        }

        // limitar JOINs
        if (substr_count(strtoupper($sql), 'JOIN') > 5) {
            throw new \Exception("Demasiados JOINs");
        }

        // forzar LIMIT
        if (!str_contains(strtoupper($sql), 'LIMIT')) {
            $sql .= " LIMIT 50";
        }

        return $sql;
    }

    // 💬 Respuesta inteligente
    private function formatResults($resultados, $sql)
    {
        if (empty($resultados)) {
            return "📭 No se encontraron resultados.";
        }

        // 📊 métricas
        $lowerSql = strtolower($sql);

        // 📊 métricas inteligentes
        if (count($resultados) === 1 && count((array)$resultados[0]) === 1) {

            $valor = array_values((array)$resultados[0])[0];
            $valorFormateado = number_format($valor, 0, ',', '.');

            if (str_contains($lowerSql, 'sum') && str_contains($lowerSql, 'pagos')) {
                return "💰 Total recaudado: $" . $valorFormateado;
            }

            if (str_contains($lowerSql, 'count') && str_contains($lowerSql, 'clientes')) {
                return "👥 Total de clientes: " . $valorFormateado;
            }

            if (str_contains($lowerSql, 'facturas') && str_contains($lowerSql, 'vencida')) {
                return "⚠️ Total en deuda: $" . $valorFormateado;
            }

            return "📊 Resultado: " . $valorFormateado;
        }

        $output = "📋 Resultados:\n\n";

        foreach ($resultados as $i => $fila) {
            $fila = (array)$fila;

            $output .= "🔹 Registro " . ($i + 1) . ":\n";

            foreach ($fila as $k => $v) {
                if ($v === null || $v === '') continue;

                if (is_string($v) && strlen($v) > 50) {
                    $v = substr($v, 0, 50) . '...';
                }

                $output .= "   • $k: $v\n";
            }

            $output .= "\n";

            if ($i >= 49) break;
        }

        return $output;
    }
}
