<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

// Importa tu comando (ajusta el namespace si es necesario)
use App\Console\Commands\CorteMasivoClientes;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    // Registra tus comandos artisan personalizados aquí
    ->withCommands([
        CorteMasivoClientes::class, // agrega aquí tu comando
    ])
    ->withSchedule(function (Illuminate\Console\Scheduling\Schedule $schedule) {
    $schedule->command('clientes:corte-masivo')->monthlyOn(4, '00:00');
    })
    
    ->create();