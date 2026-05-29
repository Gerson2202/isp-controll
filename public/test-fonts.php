<?php
$fontsDir = __DIR__ . '/public/fonts/';
echo "Buscando fuentes en: $fontsDir<br>";
$files = glob($fontsDir . '*.ttf');
foreach ($files as $file) {
    echo "✅ Encontrado: " . basename($file) . "<br>";
}