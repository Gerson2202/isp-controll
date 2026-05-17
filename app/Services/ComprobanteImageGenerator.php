<?php

namespace App\Services;

use Carbon\Carbon;

class ComprobanteImageGenerator
{
    public function generate($factura, $pago, $empresa, $ruta)
    {
        // Dimensiones
        $ancho = 550;
        $alto = 900;
        
        // Crear imagen
        $imagen = imagecreatetruecolor($ancho, $alto);
        
        // Colores
        $blanco = imagecolorallocate($imagen, 255, 255, 255);
        $negro = imagecolorallocate($imagen, 0, 0, 0);
        $gris = imagecolorallocate($imagen, 108, 117, 125);
        $grisClaro = imagecolorallocate($imagen, 233, 236, 239);
        $verde = imagecolorallocate($imagen, 46, 125, 50);
        $verdeClaro = imagecolorallocate($imagen, 232, 245, 233);
        $verdeOscuro = imagecolorallocate($imagen, 27, 94, 32);
        $azul = imagecolorallocate($imagen, 26, 35, 126);
        $azulOscuro = imagecolorallocate($imagen, 13, 17, 63);
        
        // Fondo blanco
        imagefill($imagen, 0, 0, $blanco);
        
        // Configurar fuentes (usar imagestring para texto simple)
        $y = 20;
        
        // === LOGO ===
        $logoTexto = $empresa->nombre ?? 'FERNET';
        $fuente = 5; // Tamaño de fuente predefinida (1-5)
        $textoAncho = imagefontwidth($fuente) * strlen($logoTexto);
        $xLogo = ($ancho - $textoAncho) / 2;
        imagestring($imagen, $fuente, $xLogo, $y, $logoTexto, $negro);
        $y += 30;
        
        // Slogan
        if ($empresa->slogan ?? false) {
            $sloganAncho = imagefontwidth(3) * strlen($empresa->slogan);
            $xSlogan = ($ancho - $sloganAncho) / 2;
            imagestring($imagen, 3, $xSlogan, $y, $empresa->slogan, $gris);
            $y += 30;
        }
        
        // Línea separadora
        imageline($imagen, 20, $y, $ancho - 20, $y, $grisClaro);
        $y += 20;
        
        // === HEADER VERDE ===
        imagefilledrectangle($imagen, 0, $y, $ancho, $y + 80, $verde);
        
        // Check icon (simulado con texto)
        imagestring($imagen, 5, $ancho / 2 - 10, $y + 15, "✓", $blanco);
        imagestring($imagen, 5, $ancho / 2 - 70, $y + 40, "COMPROBANTE DE PAGO", $blanco);
        
        // Número de referencia
        $ref = "#" . substr($factura->numero_factura ?? '', -8) . " • " . Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i');
        $refAncho = imagefontwidth(2) * strlen($ref);
        $xRef = ($ancho - $refAncho) / 2;
        imagestring($imagen, 2, $xRef, $y + 60, $ref, $blanco);
        
        $y += 100;
        
        // === INFORMACIÓN DEL CLIENTE ===
        // Título
        imagestring($imagen, 4, 25, $y, "INFORMACION DEL CLIENTE", $gris);
        $y += 25;
        
        // Nombre
        imagestring($imagen, 3, 25, $y, "Nombre:", $gris);
        $nombre = $factura->contrato->cliente->nombre ?? '';
        imagestring($imagen, 3, 150, $y, $nombre, $negro);
        $y += 20;
        
        // Identificación
        imagestring($imagen, 3, 25, $y, "Identificacion:", $gris);
        $identificacion = $factura->contrato->cliente->identificacion ?? '';
        imagestring($imagen, 3, 150, $y, $identificacion, $negro);
        $y += 20;
        
        // Contacto
        imagestring($imagen, 3, 25, $y, "Contacto:", $gris);
        $contacto = $factura->contrato->cliente->telefono ?? '';
        imagestring($imagen, 3, 150, $y, $contacto, $negro);
        $y += 20;
        
        // Tipo servicio
        imagestring($imagen, 3, 25, $y, "Tipo de servicio:", $gris);
        $tipoServicio = $factura->contrato->plan->nombre ?? 'Internet';
        imagestring($imagen, 3, 150, $y, $tipoServicio, $negro);
        $y += 30;
        
        // Línea separadora
        imageline($imagen, 20, $y, $ancho - 20, $y, $grisClaro);
        $y += 15;
        
        // === DETALLES DE PAGO ===
        imagestring($imagen, 4, 25, $y, "DETALLES DE PAGO", $gris);
        $y += 25;
        
        // Fecha de pago
        imagestring($imagen, 3, 25, $y, "Fecha de pago:", $gris);
        $fechaPago = Carbon::parse($pago->fecha_pago)->format('d/m/Y');
        imagestring($imagen, 3, 200, $y, $fechaPago, $negro);
        $y += 20;
        
        // Periodo
        imagestring($imagen, 3, 25, $y, "Periodo del servicio:", $gris);
        $periodo = $factura->periodo ?? 'junio 2026';
        imagestring($imagen, 3, 200, $y, $periodo, $negro);
        $y += 20;
        
        // Método
        imagestring($imagen, 3, 25, $y, "Metodo:", $gris);
        $metodo = ucfirst($pago->metodo_pago ?? '');
        imagestring($imagen, 3, 200, $y, $metodo, $negro);
        $y += 20;
        
        // Monto (destacado)
        imagestring($imagen, 3, 25, $y, "Monto:", $gris);
        $monto = "$ " . number_format($pago->monto ?? 0, 0, ',', '.');
        imagestring($imagen, 5, 200, $y, $monto, $verdeOscuro);
        $y += 25;
        
        // Saldo pendiente
        imagestring($imagen, 3, 25, $y, "Saldo pendiente:", $gris);
        imagestring($imagen, 3, 200, $y, "$ 0", $verde);
        $y += 20;
        
        // Comprobante
        imagestring($imagen, 3, 25, $y, "Comprobante:", $gris);
        imagestring($imagen, 3, 200, $y, $factura->numero_factura ?? '', $negro);
        $y += 20;
        
        // Estado
        imagestring($imagen, 3, 25, $y, "Estado:", $gris);
        imagestring($imagen, 3, 200, $y, "PAGADA", $verde);
        $y += 30;
        
        // Línea decorativa
        imageline($imagen, 20, $y, $ancho - 20, $y, $verdeClaro);
        $y += 15;
        
        // === FOOTER AZUL ===
        imagefilledrectangle($imagen, 0, $y, $ancho, $alto, $azul);
        
        // Nombre empresa
        $empresaNombre = $empresa->nombre ?? 'FERNET';
        $nombreAncho = imagefontwidth(4) * strlen($empresaNombre);
        $xNombre = ($ancho - $nombreAncho) / 2;
        imagestring($imagen, 4, $xNombre, $y + 20, $empresaNombre, $blanco);
        
        // Información contacto
        $telefono = $empresa->telefono ?? '';
        $email = $empresa->email ?? '';
        $contacto = "📞 $telefono  |  ✉ $email";
        $contactoAncho = imagefontwidth(2) * strlen($contacto);
        $xContacto = ($ancho - $contactoAncho) / 2;
        imagestring($imagen, 2, $xContacto, $y + 45, $contacto, $blanco);
        
        // Mensaje legal
        $mensaje = "Este documento es un comprobante de pago valido";
        $mensajeAncho = imagefontwidth(2) * strlen($mensaje);
        $xMensaje = ($ancho - $mensajeAncho) / 2;
        imagestring($imagen, 2, $xMensaje, $y + 65, $mensaje, $blanco);
        
        // Guardar imagen
        imagepng($imagen, $ruta);
        imagedestroy($imagen);
        
        return $ruta;
    }
}