<?php

namespace App\Http\Controllers;

use App\Models\Work;
use Illuminate\Http\Request;

/**
 * Controlador: Portal de Seguimiento (Vista del Cliente)
 * 
 * Este controlador NO requiere autenticación.
 * Es la página pública donde el cliente ingresa su código
 * de seguimiento y ve el estado de sus gafas.
 * 
 * Acceso: GET /seguimiento o GET /seguimiento/{tracking_code}
 */
class TrackingController extends Controller
{
    /**
     * Página del portal de seguimiento
     * Si llega con código, muestra el resultado directamente.
     * Ruta: GET /seguimiento/{code?}
     */
    public function index(?string $code = null)
    {
        $work = null;

        if ($code) {
            $work = Work::with(['client', 'laboratory', 'formula', 'statusChanges'])
                ->where('tracking_code', $code)
                ->first();
        }

        return view('tracking.index', compact('work', 'code'));
    }

    /**
     * Buscar un trabajo por código (formulario POST)
     * Ruta: POST /seguimiento/buscar
     */
    public function search(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        // Limpiar el código (quitar espacios)
        $code = strtoupper(trim($request->code));

        return redirect()->route('tracking', $code);
    }
}