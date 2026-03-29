<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: Control de Acceso por Rol
 * 
 * Verifica que el usuario tenga el rol necesario para acceder a una ruta.
 * Se usa en las rutas así: ->middleware('role:admin')
 * 
 * Roles disponibles:
 * - admin: Acceso total (dashboard, trabajos, clientes, laboratorios, reportes)
 * - seller: Acceso limitado (trabajos, clientes — NO reportes)
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Si no está autenticado, redirigir al login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Si no tiene ninguno de los roles permitidos, error 403
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}