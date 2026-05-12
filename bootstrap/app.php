<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/**
 * Configuración principal de la aplicación Laravel
 * 
 * Aquí se registran los middlewares personalizados.
 * El middleware 'role' permite proteger rutas por rol de usuario.
 */
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Confiar en el proxy de Render (X-Forwarded-Proto = https, etc.)
        // Necesario para que Auth, URL::current() y los enlaces generados
        // usen el esquema https detrás del balanceador.
        $middleware->trustProxies(at: '*');

        // Registrar middleware de roles: ->middleware('role:admin')
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();