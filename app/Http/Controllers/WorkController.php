<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\Client;
use App\Models\Formula;
use App\Models\Laboratory;
use App\Models\Payment;
use App\Models\WorkStatusChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador: Gestión de Trabajos
 * 
 * El controlador más importante del sistema. Maneja:
 * - Listado de trabajos (con filtros por estado, laboratorio, búsqueda)
 * - Crear nuevo trabajo (con fórmula, montura, lente, precios)
 * - Ver detalle de un trabajo (timeline, pagos, QR)
 * - Actualizar estado de un trabajo
 * - Registrar abonos/pagos
 */
class WorkController extends Controller
{
    /**
     * Listado de todos los trabajos
     * Ruta: GET /trabajos
     * 
     * Soporta filtros por:
     * - ?status=ready (filtrar por estado)
     * - ?laboratory=1 (filtrar por laboratorio)
     * - ?search=María (buscar por nombre, cédula o código)
     */
    public function index(Request $request)
    {
        $query = Work::with(['client', 'laboratory']);

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por laboratorio
        if ($request->filled('laboratory')) {
            $query->where('laboratory_id', $request->laboratory);
        }

        // Búsqueda por nombre, cédula o código
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tracking_code', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('document_number', 'like', "%{$search}%");
                  });
            });
        }

        // Ordenar: más recientes primero
        $works = $query->orderByDesc('created_at')->paginate(20);

        // Laboratorios para el filtro combo
        $laboratories = Laboratory::where('is_active', true)->get();

        return view('works.index', compact('works', 'laboratories'));
    }

    /**
     * Formulario para crear un nuevo trabajo
     * Ruta: GET /trabajos/crear
     */
    public function create(Request $request)
    {
        $laboratories = Laboratory::where('is_active', true)->get();
        $clients = Client::orderBy('first_name')->get();
        
        // Si viene con un client_id preseleccionado (desde la ficha del cliente)
        $selectedClient = null;
        if ($request->filled('client_id')) {
            $selectedClient = Client::find($request->client_id);
        }

        return view('works.create', compact('laboratories', 'clients', 'selectedClient'));
    }

    /**
     * Guardar un nuevo trabajo en la base de datos
     * Ruta: POST /trabajos
     */
    public function store(Request $request)
    {
        // Validar todos los campos del formulario
        $validated = $request->validate([
            // Cliente
            'client_id' => 'required|exists:clients,id',
            
            // Fórmula
            'od_sphere' => 'nullable|numeric',
            'od_cylinder' => 'nullable|numeric',
            'od_axis' => 'nullable|integer|min:0|max:180',
            'od_add' => 'nullable|numeric',
            'od_dnp' => 'nullable|numeric',
            'oi_sphere' => 'nullable|numeric',
            'oi_cylinder' => 'nullable|numeric',
            'oi_axis' => 'nullable|integer|min:0|max:180',
            'oi_add' => 'nullable|numeric',
            'oi_dnp' => 'nullable|numeric',
            'exam_date' => 'nullable|date',
            
            // Montura
            'frame_type' => 'required|in:own,purchased',
            'frame_brand' => 'nullable|string|max:100',
            'frame_reference' => 'nullable|string|max:100',
            
            // Lente
            'lens_type' => 'required|in:monofocal,bifocal,progressive',
            'lens_material' => 'required|in:cr39,polycarbonate,high_index,trivex',
            'laboratory_id' => 'required|exists:laboratories,id',
            
            // Precios
            'price_lenses' => 'required|numeric|min:0',
            'price_frame' => 'nullable|numeric|min:0',
            'price_consultation' => 'nullable|numeric|min:0',
            'initial_payment' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,card,transfer,nequi,daviplata,other',
            
            // Extras
            'observations' => 'nullable|string|max:1000',
            'is_urgent' => 'nullable|boolean',
            'is_vip' => 'nullable|boolean',
            'is_warranty' => 'nullable|boolean',
            'estimated_delivery' => 'nullable|date',
        ], [
            'client_id.required' => 'Selecciona un cliente.',
            'lens_type.required' => 'Selecciona el tipo de lente.',
            'lens_material.required' => 'Selecciona el material.',
            'laboratory_id.required' => 'Selecciona un laboratorio.',
            'price_lenses.required' => 'Ingresa el precio de los lentes.',
        ]);

        // 1. Crear la fórmula óptica
        $formula = Formula::create([
            'client_id' => $validated['client_id'],
            'od_sphere' => $validated['od_sphere'] ?? null,
            'od_cylinder' => $validated['od_cylinder'] ?? null,
            'od_axis' => $validated['od_axis'] ?? null,
            'od_add' => $validated['od_add'] ?? null,
            'od_dnp' => $validated['od_dnp'] ?? null,
            'oi_sphere' => $validated['oi_sphere'] ?? null,
            'oi_cylinder' => $validated['oi_cylinder'] ?? null,
            'oi_axis' => $validated['oi_axis'] ?? null,
            'oi_add' => $validated['oi_add'] ?? null,
            'oi_dnp' => $validated['oi_dnp'] ?? null,
            'exam_date' => $validated['exam_date'] ?? now(),
        ]);

        // 2. Calcular el total
        $priceFrame = $validated['price_frame'] ?? 0;
        $priceConsultation = $validated['price_consultation'] ?? 0;
        $priceTotal = $validated['price_lenses'] + $priceFrame + $priceConsultation;

        // 3. Crear el trabajo con código de seguimiento automático
        $work = Work::create([
            'tracking_code' => Work::generateTrackingCode(),
            'client_id' => $validated['client_id'],
            'laboratory_id' => $validated['laboratory_id'],
            'formula_id' => $formula->id,
            'user_id' => Auth::id(),
            'status' => 'registered',
            'frame_type' => $validated['frame_type'],
            'frame_brand' => $validated['frame_brand'] ?? null,
            'frame_reference' => $validated['frame_reference'] ?? null,
            'lens_type' => $validated['lens_type'],
            'lens_material' => $validated['lens_material'],
            'treatment_antireflective' => $request->boolean('treatment_antireflective'),
            'treatment_photochromic' => $request->boolean('treatment_photochromic'),
            'treatment_blue_filter' => $request->boolean('treatment_blue_filter'),
            'treatment_polarized' => $request->boolean('treatment_polarized'),
            'price_lenses' => $validated['price_lenses'],
            'price_frame' => $priceFrame,
            'price_consultation' => $priceConsultation,
            'price_total' => $priceTotal,
            'is_urgent' => $request->boolean('is_urgent'),
            'is_vip' => $request->boolean('is_vip'),
            'is_warranty' => $request->boolean('is_warranty'),
            'estimated_delivery' => $validated['estimated_delivery'] ?? null,
            'observations' => $validated['observations'] ?? null,
        ]);

        // 4. Registrar el primer cambio de estado
        WorkStatusChange::create([
            'work_id' => $work->id,
            'user_id' => Auth::id(),
            'from_status' => null,
            'to_status' => 'registered',
            'notes' => 'Orden creada.',
        ]);

        // 5. Registrar el abono inicial (si lo hubo)
        $initialPayment = $validated['initial_payment'] ?? 0;
        if ($initialPayment > 0) {
            Payment::create([
                'work_id' => $work->id,
                'user_id' => Auth::id(),
                'amount' => $initialPayment,
                'method' => $validated['payment_method'] ?? 'cash',
                'notes' => 'Abono al crear la orden.',
            ]);
        }

        return redirect()->route('works.show', $work)
            ->with('success', '✅ Trabajo ' . $work->tracking_code . ' creado exitosamente.');
    }

    /**
     * Ver el detalle completo de un trabajo
     * Ruta: GET /trabajos/{work}
     * 
     * Muestra: timeline, QR, pagos, fórmula, historial de estados
     */
    public function show(Work $work)
    {
        // Cargar todas las relaciones necesarias
        $work->load([
            'client', 
            'laboratory', 
            'formula', 
            'user',
            'payments.user',           // Pagos con el usuario que los registró
            'statusChanges.user',      // Cambios de estado con quién los hizo
        ]);

        return view('works.show', compact('work'));
    }

    /**
     * Actualizar el estado de un trabajo
     * Ruta: PATCH /trabajos/{work}/status
     * 
     * REGLA DE NEGOCIO: No se puede marcar como "Entregado" si tiene saldo pendiente.
     * El cliente debe pagar todo antes de llevarse las gafas.
     */
    public function updateStatus(Request $request, Work $work)
    {
        $validated = $request->validate([
            'status' => 'required|in:registered,sent_to_lab,in_process,received,ready,delivered,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        $newStatus = $validated['status'];

        // REGLA: No entregar si tiene saldo pendiente
        if ($newStatus === 'delivered' && $work->pending_balance > 0) {
            return redirect()->route('works.show', $work)
                ->with('error', '⚠️ No se puede entregar. El cliente tiene un saldo pendiente de $' 
                    . number_format($work->pending_balance, 0, ',', '.') 
                    . '. Debe pagar antes de entregar.');
        }

        $oldStatus = $work->status;

        // Actualizar el estado del trabajo
        $work->update([
            'status' => $newStatus,
            'actual_delivery' => $newStatus === 'delivered' ? now() : $work->actual_delivery,
        ]);

        // Registrar el cambio en el historial
        WorkStatusChange::create([
            'work_id' => $work->id,
            'user_id' => Auth::id(),
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('works.show', $work)
            ->with('success', '✅ Estado actualizado a: ' . $work->fresh()->status_name);
    }

    /**
     * Registrar un abono/pago en un trabajo
     * Ruta: POST /trabajos/{work}/payment
     */
    public function storePayment(Request $request, Work $work)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,card,transfer,nequi,daviplata,other',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'Ingresa el monto del abono.',
            'amount.min' => 'El monto debe ser mayor a $0.',
        ]);

        Payment::create([
            'work_id' => $work->id,
            'user_id' => Auth::id(),
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('works.show', $work)
            ->with('success', '✅ Abono de $' . number_format($validated['amount'], 0, ',', '.') . ' registrado.');
    }
    /**
     * Formulario para editar un trabajo existente
     * Ruta: GET /trabajos/{work}/editar
     */
    public function edit(Work $work)
    {
        $work->load(['client', 'laboratory', 'formula', 'payments']);
        $laboratories = Laboratory::where('is_active', true)->get();

        return view('works.edit', compact('work', 'laboratories'));
    }

    /**
     * Actualizar los datos de un trabajo
     * Ruta: PUT /trabajos/{work}
     */
    public function update(Request $request, Work $work)
    {
        $validated = $request->validate([
            'frame_type' => 'required|in:own,purchased',
            'frame_brand' => 'nullable|string|max:100',
            'frame_reference' => 'nullable|string|max:100',
            'lens_type' => 'required|in:monofocal,bifocal,progressive',
            'lens_material' => 'required|in:cr39,polycarbonate,high_index,trivex',
            'laboratory_id' => 'required|exists:laboratories,id',
            'price_lenses' => 'required|numeric|min:0',
            'price_frame' => 'nullable|numeric|min:0',
            'price_consultation' => 'nullable|numeric|min:0',
            'observations' => 'nullable|string|max:1000',
            'is_urgent' => 'nullable|boolean',
            'is_vip' => 'nullable|boolean',
            'is_warranty' => 'nullable|boolean',
            'estimated_delivery' => 'nullable|date',
        ]);

        $priceFrame = $validated['price_frame'] ?? 0;
        $priceConsultation = $validated['price_consultation'] ?? 0;
        $priceTotal = $validated['price_lenses'] + $priceFrame + $priceConsultation;

        $work->update([
            'frame_type' => $validated['frame_type'],
            'frame_brand' => $validated['frame_brand'] ?? null,
            'frame_reference' => $validated['frame_reference'] ?? null,
            'lens_type' => $validated['lens_type'],
            'lens_material' => $validated['lens_material'],
            'laboratory_id' => $validated['laboratory_id'],
            'treatment_antireflective' => $request->boolean('treatment_antireflective'),
            'treatment_photochromic' => $request->boolean('treatment_photochromic'),
            'treatment_blue_filter' => $request->boolean('treatment_blue_filter'),
            'treatment_polarized' => $request->boolean('treatment_polarized'),
            'price_lenses' => $validated['price_lenses'],
            'price_frame' => $priceFrame,
            'price_consultation' => $priceConsultation,
            'price_total' => $priceTotal,
            'is_urgent' => $request->boolean('is_urgent'),
            'is_vip' => $request->boolean('is_vip'),
            'is_warranty' => $request->boolean('is_warranty'),
            'estimated_delivery' => $validated['estimated_delivery'] ?? null,
            'observations' => $validated['observations'] ?? null,
        ]);

        return redirect()->route('works.show', $work)
            ->with('success', '✅ Trabajo ' . $work->tracking_code . ' actualizado correctamente.');
    }
    /**
     * Eliminar un abono/pago
     * Ruta: DELETE /trabajos/{work}/pago/{payment}
     * 
     * Permite corregir abonos registrados por error.
     */
    public function destroyPayment(Work $work, Payment $payment)
    {
        // Verificar que el pago pertenezca a este trabajo
        if ($payment->work_id !== $work->id) {
            abort(403);
        }

        $amount = $payment->amount;
        $payment->delete();

        return redirect()->route('works.show', $work)
            ->with('success', '✅ Abono de $' . number_format($amount, 0, ',', '.') . ' eliminado correctamente.');
    }
}