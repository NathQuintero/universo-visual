<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

/**
 * Controlador: Gestión de Trabajadoras (Empleadas)
 *
 * CRUD de las empleadas físicas (Maira, Nelly, ...). Solo admin.
 * Las trabajadoras NO inician sesión: comparten una cuenta de usuario
 * y se identifican al momento de registrar cada venta o pago.
 */
class EmployeeController extends Controller
{
    /** Listado con estadísticas de cada empleada */
    public function index()
    {
        $employees = Employee::withCount([
            'works as total_works',
            'works as active_works' => fn($q) => $q->whereNotIn('status', ['delivered', 'cancelled']),
            'payments as total_payments',
        ])->orderByDesc('is_active')->orderBy('name')->get();

        return view('employees.index', compact('employees'));
    }

    /**
     * Perfil de una trabajadora: trabajos atendidos + pagos recibidos.
     * Solo admin puede entrar (la ruta está bajo middleware role:admin).
     */
    public function show(Employee $employee)
    {
        $works = $employee->works()
            ->with(['client', 'laboratory'])
            ->orderByDesc('created_at')
            ->get();

        $payments = $employee->payments()
            ->with(['work.client'])
            ->orderByDesc('created_at')
            ->get();

        // Resumen agregado
        $now = \Carbon\Carbon::now();
        $stats = [
            'works_total' => $works->count(),
            'works_active' => $works->whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'works_delivered' => $works->where('status', 'delivered')->count(),
            'works_cancelled' => $works->where('status', 'cancelled')->count(),
            'works_value_total' => (float) $works->sum('price_total'),
            'works_this_month' => $works->filter(fn($w) => $w->created_at->isSameMonth($now))->count(),
            'works_value_this_month' => (float) $works->filter(fn($w) => $w->created_at->isSameMonth($now))->sum('price_total'),
            'payments_total_count' => $payments->count(),
            'payments_total_amount' => (float) $payments->sum('amount'),
            'payments_this_month_amount' => (float) $payments->filter(fn($p) => $p->created_at->isSameMonth($now))->sum('amount'),
        ];

        return view('employees.show', compact('employee', 'works', 'payments', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:employees,name',
            'phone' => 'nullable|string|max:30',
        ], [
            'name.required' => 'El nombre de la trabajadora es obligatorio.',
            'name.unique' => 'Ya existe una trabajadora con ese nombre.',
        ]);

        Employee::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('employees.index')
            ->with('success', '✅ Trabajadora agregada correctamente.');
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:employees,name,' . $employee->id,
            'phone' => 'nullable|string|max:30',
            'is_active' => 'nullable|boolean',
        ]);

        $employee->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('employees.index')
            ->with('success', '✅ Trabajadora actualizada.');
    }
}
