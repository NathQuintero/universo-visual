<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use App\Models\Work;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

    /**
     * Detalle del laboratorio: lentes que se le han comprado, agrupados por
     * urgencia de pago (15 días desde que recibimos el lente).
     */
    public function show(Laboratory $laboratory)
    {
        $works = $laboratory->works()
            ->with(['client', 'statusChanges' => fn($q) => $q->where('to_status', 'received')])
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('created_at')
            ->get();

        // Agrupar por estado de pago al lab
        $groups = [
            'overdue' => collect(),
            'due' => collect(),
            'due_soon' => collect(),
            'pending' => collect(),
            'not_received' => collect(),
            'paid' => collect(),
        ];
        foreach ($works as $w) {
            $key = $w->lab_payment_status;
            if (isset($groups[$key])) $groups[$key]->push($w);
        }

        $stats = [
            'total_works' => $works->count(),
            'overdue_count' => $groups['overdue']->count(),
            'due_count' => $groups['due']->count(),
            'due_soon_count' => $groups['due_soon']->count(),
            'pending_count' => $groups['pending']->count(),
            'paid_count' => $groups['paid']->count(),
            'unpaid_amount' => (float) $works->whereIn('lab_payment_status', ['overdue', 'due', 'due_soon', 'pending'])->sum('lab_cost'),
            'overdue_amount' => (float) $groups['overdue']->sum('lab_cost') + (float) $groups['due']->sum('lab_cost'),
            'paid_total_amount' => (float) $groups['paid']->sum('lab_cost'),
        ];

        return view('laboratories.show', compact('laboratory', 'groups', 'stats'));
    }

    /**
     * Marca un trabajo como pagado al laboratorio.
     * Ruta: POST /laboratorios/{laboratory}/pagar/{work}
     */
    public function markWorkPaid(Request $request, Laboratory $laboratory, Work $work)
    {
        if ($work->laboratory_id !== $laboratory->id) {
            abort(403, 'El trabajo no pertenece a este laboratorio.');
        }

        $work->update([
            'lab_paid_at' => Carbon::today(),
        ]);

        return redirect()->route('laboratories.show', $laboratory)
            ->with('success', '✅ Pago al laboratorio registrado para el trabajo ' . $work->tracking_code . '.');
    }

    /**
     * Deshace el pago al laboratorio (por si fue un error).
     */
    public function unmarkWorkPaid(Request $request, Laboratory $laboratory, Work $work)
    {
        if ($work->laboratory_id !== $laboratory->id) {
            abort(403);
        }

        $work->update(['lab_paid_at' => null]);

        return redirect()->route('laboratories.show', $laboratory)
            ->with('success', '↩️ Pago al lab del trabajo ' . $work->tracking_code . ' deshecho.');
    }
}