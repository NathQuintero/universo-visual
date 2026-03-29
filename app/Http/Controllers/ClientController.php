<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\Formula;
use Illuminate\Http\Request;

/**
 * Controlador: Gestión de Clientes
 * 
 * Maneja el CRUD de clientes y sus funcionalidades:
 * - Listado con búsqueda y filtros
 * - Crear/editar clientes
 * - Ver ficha completa (fórmula, historial de trabajos)
 * - Cumpleañeros del mes
 */
class ClientController extends Controller
{
    /**
     * Listado de clientes con búsqueda y filtros
     * Ruta: GET /clientes
     */
    public function index(Request $request)
    {
        $query = Client::withCount('works');

        // Búsqueda por nombre, cédula o teléfono
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'active':
                    $query->whereHas('works', fn($q) => 
                        $q->whereNotIn('status', ['delivered', 'cancelled'])
                    );
                    break;
                case 'pending_balance':
                    // Clientes que tienen trabajos con saldo pendiente
                    $query->whereHas('works', fn($q) => 
                        $q->where('status', '!=', 'cancelled')
                    );
                    break;
                case 'new':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
            }
        }

        $clients = $query->orderBy('first_name')->paginate(20);

        // Estadísticas
        $stats = [
            'total' => Client::count(),
            'new_this_month' => Client::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
            'with_active_works' => Client::whereHas('works', fn($q) =>
                $q->whereNotIn('status', ['delivered', 'cancelled'])
            )->count(),
            'with_pending_balance' => $this->clientsWithPendingBalance(),
        ];

        return view('clients.index', compact('clients', 'stats'));
    }

    /**
     * Formulario para crear un nuevo cliente
     * Ruta: GET /clientes/crear
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Guardar un nuevo cliente
     * Ruta: POST /clientes
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'document_type' => 'required|in:CC,TI,CE,PA,RC',
            'document_number' => 'required|string|max:20|unique:clients,document_number',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'whatsapp_authorized' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ], [
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'document_number.required' => 'La cédula es obligatoria.',
            'document_number.unique' => 'Ya existe un cliente con esa cédula.',
        ]);

        $validated['whatsapp_authorized'] = $request->boolean('whatsapp_authorized');

        $client = Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', '✅ Cliente ' . $client->full_name . ' creado exitosamente.');
    }

    /**
     * Ver ficha completa de un cliente
     * Ruta: GET /clientes/{client}
     * 
     * Muestra: datos personales, fórmula actual, historial de trabajos,
     * historial de fórmulas (evolución visual)
     */
    public function show(Client $client)
    {
        $client->load([
            'works.laboratory',        // Trabajos con su laboratorio
            'works.payments',          // Trabajos con sus pagos
            'formulas',                // Todas las fórmulas (historial visual)
        ]);

        return view('clients.show', compact('client'));
    }

    /**
     * Formulario para editar un cliente
     * Ruta: GET /clientes/{client}/editar
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Actualizar datos de un cliente
     * Ruta: PUT /clientes/{client}
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'document_type' => 'required|in:CC,TI,CE,PA,RC',
            'document_number' => 'required|string|max:20|unique:clients,document_number,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'whatsapp_authorized' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['whatsapp_authorized'] = $request->boolean('whatsapp_authorized');

        $client->update($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', '✅ Datos actualizados correctamente.');
    }

    /**
     * Cumpleañeros del mes
     * Ruta: GET /clientes/cumpleanos
     */
    public function birthdays()
    {
        $clients = Client::whereNotNull('birth_date')
            ->whereMonth('birth_date', now()->month)
            ->get()
            ->sortBy(fn($c) => $c->daysUntilBirthday());

        return view('clients.birthdays', compact('clients'));
    }

    /**
     * Contar clientes con saldo pendiente
     */
    private function clientsWithPendingBalance(): int
    {
        return Client::all()->filter(fn($c) => $c->total_pending_balance > 0)->count();
    }
}