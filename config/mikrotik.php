<?php
return [
    'host' => env('MIKROTIK_HOST', '192.168.51.1'),
    'user' => env('MIKROTIK_USER', 'GerApi'),
    'pass' => env('MIKROTIK_PASS', '12345678'),
    'port' => (int) env('MIKROTIK_PORT', 8728), // Convertir a entero
];