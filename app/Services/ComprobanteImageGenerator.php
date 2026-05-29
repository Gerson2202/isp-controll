<?php

namespace App\Services;

use Carbon\Carbon;

class ComprobanteImageGenerator
{
    private $fuenteRegular;
    private $fuenteBold;

    public function generate($factura, $pago, $empresa, $ruta)
    {
        $ancho = 550;
        $alto = 920;

        // Cargar fuentes Roboto

        $this->fuenteRegular = public_path('fonts/OpenSans-Regular.ttf');
        $this->fuenteBold = public_path('fonts/OpenSans-Regular.ttf');

        if (!file_exists($this->fuenteRegular)) {
            $this->fuenteRegular = public_path('fonts/Roboto-Regular.ttf');
            $this->fuenteBold = public_path('fonts/Roboto-Bold.ttf');
        }

        // === FONDO EXTERIOR GRIS (para que resalte) ===
        $imagen = imagecreatetruecolor($ancho, $alto);
        $grisFondo = imagecolorallocate($imagen, 220, 222, 225);
        imagefill($imagen, 0, 0, $grisFondo);

        // === CONTENEDOR BLANCO CON BORDES REDONDEADOS ===
        $containerPadding = 15;
        $containerAncho = $ancho - ($containerPadding * 2);
        $containerAlto = $alto - ($containerPadding * 2);
        $containerX = $containerPadding;
        $containerY = $containerPadding;

        $blanco = imagecolorallocate($imagen, 255, 255, 255);

        $radius = 20;
        $this->roundedRectangle(
            $imagen,
            $containerX,
            $containerY,
            $containerX + $containerAncho,
            $containerY + $containerAlto,
            $radius,
            $blanco
        );

        // Colores internos
        $negro = imagecolorallocate($imagen, 33, 37, 41);
        $gris = imagecolorallocate($imagen, 108, 117, 125);
        $grisClaro = imagecolorallocate($imagen, 230, 232, 235); // Más oscuro para que se vea
        $verde = imagecolorallocate($imagen, 46, 125, 50);
        $verdeOscuro = imagecolorallocate($imagen, 27, 94, 32);
        $azul = imagecolorallocate($imagen, 26, 35, 126);

        $y = $containerY + 25;

        // === LOGO DE LA EMPRESA ===
        $logoCargado = false;

        if (!empty($empresa->logo)) {
            $logoPath = storage_path('app/public/' . $empresa->logo);
            if (file_exists($logoPath)) {
                $logo = @imagecreatefromstring(file_get_contents($logoPath));
                if ($logo) {
                    $logoAncho = imagesx($logo);
                    $logoAlto = imagesy($logo);
                    $nuevoAlto = 80;
                    $nuevoAncho = ($logoAncho * $nuevoAlto) / $logoAlto;

                    $logoX = $containerX + ($containerAncho - $nuevoAncho) / 2;

                    imagecopyresampled(
                        $imagen,
                        $logo,
                        $logoX,
                        $y,
                        0,
                        0,
                        $nuevoAncho,
                        $nuevoAlto,
                        $logoAncho,
                        $logoAlto
                    );
                    $logoCargado = true;
                    $y += $nuevoAlto + 5; // Reducido de 10 a 5
                    imagedestroy($logo);
                }
            }
        }

        if (!$logoCargado) {
            $nombreEmpresa = $empresa->nombre ?? 'Telecomunicaciones';
            $this->textoCentrado($imagen, 26, $containerX + $containerAncho / 2, $y + 30, $nombreEmpresa, $verdeOscuro, true);
            $y += 45; // Reducido de 60 a 45

            if ($empresa->slogan) {
                $this->textoCentrado($imagen, 11, $containerX + $containerAncho / 2, $y, $empresa->slogan, $gris, false);
                $y += 20; // Reducido de 25 a 20
            }
        } else {
            if ($empresa->slogan) {
                $this->textoCentrado($imagen, 11, $containerX + $containerAncho / 2, $y + 5, $empresa->slogan, $gris, false);
                $y += 20; // Reducido de 30 a 20
            }
        }

        // Línea separadora (más visible)
        $y += 5; // Reducido de 10 a 5
        imageline($imagen, $containerX + 25, $y, $containerX + $containerAncho - 25, $y, $grisClaro);
        // Segunda línea para que sea más notoria
        imageline($imagen, $containerX + 25, $y + 2, $containerX + $containerAncho - 25, $y + 2, $grisClaro);
        $y += 15; // Reducido de 20 a 15

        // === HEADER VERDE===
        // PAGO EXITOSO"
        $headerHeight = 88;
        $this->roundedRectangle($imagen, $containerX, $y, $containerX + $containerAncho, $y + $headerHeight, 10, $verde);

        // Título principal "PAGO EXITOSO"
        $this->textoCentrado($imagen, 16, $containerX + $containerAncho / 2, $y + 28, "PAGO EXITOSO", $blanco, true);

        // Subtítulo "COMPROBANTE DE PAGO"
        $this->textoCentrado($imagen, 11, $containerX + $containerAncho / 2, $y + 58, "COMPROBANTE DE PAGO", $blanco, false);

        // Referencia y fecha
        $ref = "#" . substr($factura->numero_factura ?? '', -8);
        $fecha = Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i');
        $refTexto = $ref . " • " . $fecha;
        $this->textoCentrado($imagen, 8, $containerX + $containerAncho / 2, $y + 72, $refTexto, $blanco, false);

        $y += $headerHeight + 20;

        // === INFORMACIÓN DEL CLIENTE ===
        $this->textoIzquierda($imagen, 13, $containerX + 25, $y, "INFORMACIÓN DEL CLIENTE", $verde, true);
        $y += 25;
        // Recuadro gris con más espacio superior
        $recuadroAlto = 105; // Aumentado de 85 a 95
        $this->roundedRectangle($imagen, $containerX + 20, $y - 5, $containerX + $containerAncho - 20, $y + $recuadroAlto, 8, $grisClaro);

        $campos = [
            'Nombre' => $factura->contrato->cliente->nombre ?? '',
            'Identificación' => $factura->contrato->cliente->identificacion ?? '',
            'Contacto' => $factura->contrato->cliente->telefono ?? '',
            'Tipo de servicio' => $factura->contrato->plan->nombre ?? 'Internet'
        ];

        $filaY = $y + 8; // Texto comienza 8px más abajo (dentro del recuadro)
        foreach ($campos as $label => $valor) {
            $this->textoIzquierda($imagen, 10, $containerX + 35, $filaY, $label . ':', $gris, true);
            $this->textoIzquierda($imagen, 10, $containerX + 150, $filaY, $valor, $negro, false);
            $filaY += 22;
        }

        // === DESPUÉS DEL RECUADRO DE INFORMACIÓN DEL CLIENTE ===
        $y = $filaY + 25; // Aumentado de 15 a 25

        // === DETALLES DE PAGO ===
        $y += 10; // Margen adicional antes del título
        $this->textoIzquierda($imagen, 13, $containerX + 25, $y, "DETALLES DE PAGO", $verde, true);
        $y += 20; // Espacio entre título y recuadro

        // Recuadro gris para detalles
        $recuadroDetallesAlto = 175;
        $this->roundedRectangle($imagen, $containerX + 20, $y, $containerX + $containerAncho - 20, $y + $recuadroDetallesAlto, 8, $grisClaro);

        // LIMITE DERECHO para alinear valores al extremo
        $limiteDerecho = $containerX + $containerAncho - 30;

        $detalles = [
            'Fecha de pago' => Carbon::parse($pago->fecha_pago)->format('d/m/Y'),
            'Periodo del servicio' => $factura->periodo ?? 'junio 2026',
            'Método' => ucfirst($pago->metodo_pago ?? ''),
            'Monto pagado' => '$ ' . number_format($pago->monto ?? 0, 0, ',', '.'),
            'Saldo pendiente' => '$ ' . number_format($factura->saldo_pendiente ?? 0, 0, ',', '.'),
            'Comprobante' => $factura->numero_factura ?? '',
            'Estado' => strtoupper($factura->estado ?? 'pendiente')
        ];

        // Color del estado (solo pendiente o pagada)
        if (strtolower($factura->estado ?? '') == 'pendiente') {
            $colorEstado = imagecolorallocate($imagen, 255, 193, 7); // Amarillo
        } else {
            $colorEstado = $verde; // Verde para pagada
        }

        $filaY = $y + 12;
        foreach ($detalles as $label => $valor) {
            // Etiqueta a la izquierda
            $this->textoIzquierda($imagen, 10, $containerX + 35, $filaY, $label . ':', $gris, true);

            // Valor alineado a la DERECHA
            if ($label == 'Monto pagado') {
                $this->textoDerecha($imagen, 15, $limiteDerecho, $filaY, $valor, $verdeOscuro, true);
            } elseif ($label == 'Estado') {
                $this->textoDerecha($imagen, 11, $limiteDerecho, $filaY, $valor, $colorEstado, true);
            } else {
                $this->textoDerecha($imagen, 10, $limiteDerecho, $filaY, $valor, $negro, false);
            }
            $filaY += 22;
        }

        $y = $filaY + 20;

        // Línea decorativa
        imageline($imagen, $containerX + 30, $y, $containerX + $containerAncho - 30, $y, $grisClaro);
        $y += 15;
        // === FOOTER AZUL ===
        $footerHeight = 80;
        $this->roundedRectangle($imagen, $containerX, $y, $containerX + $containerAncho, $y + $footerHeight, 10, $azul);

        $nombreEmpresa = $empresa->nombre ?? 'Tu empresa';
        $this->textoCentrado($imagen, 13, $containerX + $containerAncho / 2, $y + 25, $nombreEmpresa, $blanco, true);

        $telefono = $empresa->telefono ?? '********';
        $email = $empresa->email ?? 'info@isprotik.com';
        $contacto = "📞 $telefono     |     ✉ $email";
        $this->textoCentrado($imagen, 9, $containerX + $containerAncho / 2, $y + 48, $contacto, $blanco, false);

        $this->textoCentrado($imagen, 8, $containerX + $containerAncho / 2, $y + 68, "Este documento es un comprobante de pago válido", $blanco, false);

        // Guardar imagen
        imagepng($imagen, $ruta, 9);
        imagedestroy($imagen);

        return $ruta;
    }

    /**
     * Dibuja un rectángulo con esquinas redondeadas
     */
    private function roundedRectangle($imagen, $x1, $y1, $x2, $y2, $radius, $color)
    {
        imagefilledrectangle($imagen, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
        imagefilledrectangle($imagen, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);

        imagefilledarc($imagen, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $color, IMG_ARC_PIE);
        imagefilledarc($imagen, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 360, $color, IMG_ARC_PIE);
        imagefilledarc($imagen, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $color, IMG_ARC_PIE);
        imagefilledarc($imagen, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 0, 90, $color, IMG_ARC_PIE);
    }

    private function textoCentrado($imagen, $tamano, $x, $y, $texto, $color, $negrita = false)
    {
        $fuente = $negrita ? $this->fuenteBold : $this->fuenteRegular;

        if ($fuente && file_exists($fuente)) {
            $caja = imagettfbbox($tamano, 0, $fuente, $texto);
            $anchoTexto = $caja[2] - $caja[0];
            $xCentrado = $x - ($anchoTexto / 2);
            imagettftext($imagen, $tamano, 0, $xCentrado, $y, $color, $fuente, $texto);
        } else {
            $size = $tamano > 14 ? 5 : ($tamano > 10 ? 3 : 2);
            $xCentrado = $x - (strlen($texto) * ($tamano / 2.5));
            imagestring($imagen, $size, $xCentrado, $y - 8, $texto, $color);
        }
    }

    private function textoIzquierda($imagen, $tamano, $x, $y, $texto, $color, $negrita = false)
    {
        $fuente = $negrita ? $this->fuenteBold : $this->fuenteRegular;

        if ($fuente && file_exists($fuente)) {
            imagettftext($imagen, $tamano, 0, $x, $y, $color, $fuente, $texto);
        } else {
            $size = $tamano > 14 ? 5 : ($tamano > 10 ? 3 : 2);
            imagestring($imagen, $size, $x, $y - 6, $texto, $color);
        }
    }

    // NUEVA FUNCIÓN: Texto alineado a la derecha
    private function textoDerecha($imagen, $tamano, $x, $y, $texto, $color, $negrita = false)
    {
        $fuente = $negrita ? $this->fuenteBold : $this->fuenteRegular;

        if ($fuente && file_exists($fuente)) {
            $caja = imagettfbbox($tamano, 0, $fuente, $texto);
            $anchoTexto = $caja[2] - $caja[0];
            $xDerecha = $x - $anchoTexto;
            imagettftext($imagen, $tamano, 0, $xDerecha, $y, $color, $fuente, $texto);
        } else {
            $size = $tamano > 14 ? 5 : ($tamano > 10 ? 3 : 2);
            $xDerecha = $x - (strlen($texto) * ($tamano / 1.8));
            imagestring($imagen, $size, $xDerecha, $y - 6, $texto, $color);
        }
    }
}
