<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use Illuminate\Http\Request;

/**
 * Controlador: Gestión de Laboratorios
 * 
 * CRUD de laboratorios aliados. Muestra estadísticas de rendimiento:
 * trabajos activos, promedio de entrega, porcentaje de cumplimiento.
 */
class LaboratoryController extends Controller
{
    /**
     * Listado de laboratorios con estadísticas
     * Ruta: GET /laboratorios
     */
    public function index()
    {
        $laboratories = Laboratory::withCount([
            'works as active_works_count' => fn($q) => 
                $q->whereNotIn('status', ['delivered', 'cancelled']),
            'works as delayed_works_count' => fn($q) => 
                $q->whereNotIn('status', ['delivered', 'cancelled', 'ready']),
        ])->get();

        return view('laboratories.index', compact('laboratories'));
    }

    /**
     * Guardar un nuevo laboratorio
     * Ruta: POST /laboratorios
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'contact_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
        ], [
            'name.required' => 'El nombre del laboratorio es obligatorio.',
        ]);

        Laboratory::create($validated);

        return redirect()->route('laboratories.index')
            ->with('success', '✅ Laboratorio creado exitosamente.');
    }

    /**
     * Actualizar datos de un laboratorio
     * Ruta: PUT /laboratorios/{laboratory}
     */
    public function update(Request $request, Laboratory $laboratory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'contact_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $laboratory->update($validated);

        return redirect()->route('laboratories.index')
            ->with('success', '✅ Laboratorio actualizado.');
    }
}