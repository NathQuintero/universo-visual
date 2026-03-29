<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Laboratory;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Controlador: Reportes y Estadísticas
 * 
 * Solo accesible por administradores.
 * Muestra KPIs, gráficos, top clientes, rendimiento de laboratorios.
 * Es el ÚNICO lugar con exportar PDF y Excel.
 */
class ReportController extends Controller
{
    /**
     * Página principal de reportes
     * Ruta: GET /reportes
     */
    public function index(Request $request)
    {
        // Rango de fechas (por defecto: mes actual)
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : Carbon::now()->endOfMonth();

        // Filtro de laboratorio (opcional)
        $labFilter = $request->laboratory_id;

        // =============================================
        // KPIs PRINCIPALES
        // =============================================
        $kpis = [
            'total_income' => Payment::whereBetween('created_at', [$startDate, $endDate])->sum('amount'),
            'clients_served' => Work::whereBetween('created_at', [$startDate, $endDate])
                ->distinct('client_id')->count('client_id'),
            'works_created' => Work::whereBetween('created_at', [$startDate, $endDate])->count(),
            'works_delivered' => Work::where('status', 'delivered')
                ->whereBetween('actual_delivery', [$startDate, $endDate])->count(),
        ];

        // Ticket promedio
        $kpis['avg_ticket'] = $kpis['works_created'] > 0 
            ? $kpis['total_income'] / $kpis['works_created'] 
            : 0;

        // Tiempo promedio de entrega (en días)
        $kpis['avg_delivery_days'] = Work::where('status', 'delivered')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('actual_delivery')
            ->selectRaw('AVG(DATEDIFF(actual_delivery, created_at)) as avg')
            ->value('avg') ?? 0;

        // =============================================
        // INGRESOS MENSUALES (últimos 7 meses para el gráfico)
        // =============================================
        $monthlyIncome = [];
        for ($i = 6; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyIncome[] = [
                'month' => $month->translatedFormat('M'),
                'amount' => Payment::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('amount'),
            ];
        }

        // =============================================
        // TOP 5 CLIENTES (por cantidad de trabajos)
        // =============================================
        $topClients = Client::withCount('works')
            ->orderByDesc('works_count')
            ->limit(5)
            ->get()
            ->map(function ($client) {
                $client->total_spent = $client->works->sum('price_total');
                return $client;
            });

        // =============================================
        // RENDIMIENTO POR LABORATORIO
        // =============================================
        $laboratories = Laboratory::where('is_active', true)->get()->map(function ($lab) {
            return [
                'name' => $lab->name,
                'total_works' => $lab->works()->count(),
                'active_works' => $lab->activeWorks()->count(),
                'avg_days' => round($lab->averageDeliveryDays() ?? 0, 1),
                'compliance' => $lab->complianceRate(),
            ];
        });

        // =============================================
        // DISTRIBUCIÓN POR LABORATORIO (para gráfico circular)
        // =============================================
        $labDistribution = Laboratory::where('is_active', true)->get()->map(function ($lab) use ($startDate, $endDate) {
            return [
                'name' => $lab->name,
                'count' => $lab->works()->whereBetween('created_at', [$startDate, $endDate])->count(),
            ];
        });

        // Laboratorios para el filtro
        $allLaboratories = Laboratory::where('is_active', true)->get();

        return view('reports.index', compact(
            'kpis', 
            'monthlyIncome', 
            'topClients', 
            'laboratories',
            'labDistribution',
            'allLaboratories',
            'startDate', 
            'endDate'
        ));
    }

    /**
     * Resumen diario
     * Ruta: GET /resumen-diario
     */
    public function dailySummary()
    {
        $today = Carbon::today();

        $summary = [
            'works_created' => Work::whereDate('created_at', $today)->count(),
            'works_delivered' => Work::where('status', 'delivered')
                ->whereDate('actual_delivery', $today)->count(),
            'today_income' => Payment::whereDate('created_at', $today)->sum('amount'),
        ];

        // Trabajos ingresados hoy
        $todayWorks = Work::with('client')
            ->whereDate('created_at', $today)
            ->get();

        // Trabajos entregados hoy
        $deliveredToday = Work::with('client')
            ->where('status', 'delivered')
            ->whereDate('actual_delivery', $today)
            ->get();

        // Pendientes por resolver
        $pendingAlerts = [];

        // Gafas listas sin recoger
        $waitingPickup = Work::with('client')
            ->where('status', 'ready')
            ->get()
            ->filter(fn($w) => $w->is_waiting_pickup);

        foreach ($waitingPickup as $work) {
            $pendingAlerts[] = [
                'type' => 'pickup',
                'work' => $work,
                'message' => 'Gafas listas sin recoger (' . $work->days_elapsed . ' días)',
            ];
        }

        // Trabajos demorados
        $delayed = Work::with(['client', 'laboratory'])
            ->whereNotIn('status', ['delivered', 'cancelled', 'ready'])
            ->get()
            ->filter(fn($w) => $w->is_delayed);

        foreach ($delayed as $work) {
            $pendingAlerts[] = [
                'type' => 'delayed',
                'work' => $work,
                'message' => 'Demorado ' . $work->days_elapsed . ' días en ' . $work->laboratory->name,
            ];
        }

        // Saldos pendientes
        $pendingPayments = Work::with('client')
            ->whereNotIn('status', ['cancelled'])
            ->get()
            ->filter(fn($w) => $w->pending_balance > 0);

        foreach ($pendingPayments as $work) {
            $pendingAlerts[] = [
                'type' => 'payment',
                'work' => $work,
                'message' => 'Saldo pendiente $' . number_format($work->pending_balance, 0, ',', '.'),
            ];
        }

        return view('reports.daily', compact('summary', 'todayWorks', 'deliveredToday', 'pendingAlerts'));
    }
}