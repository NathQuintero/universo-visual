<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador: Login / Autenticación
 * 
 * Maneja el inicio y cierre de sesión de los usuarios.
 * Los usuarios son los empleados de la óptica (admin o vendedor).
 * 
 * Credenciales de prueba:
 * - Admin: admin@universovisual.com / admin123
 * - Vendedor: vendedor@universovisual.com / vendedor123
 */
class LoginController extends Controller
{
    /**
     * Mostrar la página de login
     * Ruta: GET /login
     */
    public function showLoginForm()
    {
        // Si ya está logueado, redirigir al dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Procesar el intento de login
     * Ruta: POST /login
     */
    public function login(Request $request)
    {
        // Validar que los campos no estén vacíos
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Ingresa tu correo electrónico.',
            'email.email' => 'Ingresa un correo válido.',
            'password.required' => 'Ingresa tu contraseña.',
        ]);

        // Intentar autenticar
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Verificar que el usuario esté activo
            if (!Auth::user()->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Tu cuenta está desactivada. Contacta al administrador.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // Si falló el login
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden.',
        ])->onlyInput('email');
    }

    /**
     * Cerrar sesión
     * Ruta: POST /logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}