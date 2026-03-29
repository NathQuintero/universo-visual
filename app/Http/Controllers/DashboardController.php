<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Work;
use App\Models\Client;
use App\Models\Laboratory;
use App\Models\Payment;
use Carbon\Carbon;

/**
 * Controlador: Dashboard Principal
 * 
 * Página de inicio del sistema. Muestra:
 * - Tarjetas de resumen (trabajos activos, listos, demorados, ingresos)
 * - Alertas inteligentes
 * - Trabajos recientes
 * - Cumpleañeros de la semana
 * - Resumen rápido del día
 */
class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard
     * Ruta: GET /dashboard
     */
    public function index()
    {
        $today = Carbon::today();

        // =============================================
        // ESTADÍSTICAS GENERALES (tarjetas de arriba)
        // =============================================
        $stats = [
            // Trabajos que NO están entregados ni cancelados
            'active_works' => Work::whereNotIn('status', ['delivered', 'cancelled'])->count(),
            
            // Trabajos listos para entregar
            'ready_works' => Work::where('status', 'ready')->count(),
            
            // Trabajos en laboratorio (enviados o en proceso)
            'in_lab_works' => Work::whereIn('status', ['sent_to_lab', 'in_process'])->count(),
            
            // Trabajos demorados (más de 5 días sin cambiar estado)
            'delayed_works' => $this->getDelayedWorksCount(),
            
            // Ingresos del mes actual
            'monthly_income' => Payment::whereMonth('created_at', $today->month)
                ->whereYear('created_at', $today->year)
                ->sum('amount'),
        ];

        // =============================================
        // TRABAJOS RECIENTES (últimos 5)
        // =============================================
        $recentWorks = Work::with(['client', 'laboratory'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // =============================================
        // ALERTAS INTELIGENTES
        // =============================================
        $alerts = [];

        // Alerta: Trabajos demorados
        $delayedWorks = $this->getDelayedWorks();
        foreach ($delayedWorks as $work) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => '⏰',
                'title' => 'Trabajo demorado en laboratorio',
                'message' => $work->tracking_code . ' • ' . $work->days_elapsed . ' días sin actualización',
                'work_id' => $work->id,
            ];
        }

        // Alerta: Trabajos listos sin recoger (más de 3 días)
        $waitingPickup = Work::with('client')
            ->where('status', 'ready')
            ->get()
            ->filter(fn($w) => $w->is_waiting_pickup);
        
        if ($waitingPickup->count() > 0) {
            $names = $waitingPickup->map(fn($w) => $w->client->first_name)->implode(' y ');
            $alerts[] = [
                'type' => 'success',
                'icon' => '✅',
                'title' => $waitingPickup->count() . ' trabajo(s) listos sin recoger',
                'message' => $names . ' — ¡Avísales!',
            ];
        }

        // Alerta: Trabajos con saldo pendiente entregados
        $pendingPayments = Work::with('client')
            ->where('status', '!=', 'cancelled')
            ->get()
            ->filter(fn($w) => $w->pending_balance > 0);

        foreach ($pendingPayments->take(3) as $work) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => '💰',
                'title' => 'Saldo pendiente',
                'message' => $work->client->full_name . ' debe $' . number_format($work->pending_balance, 0, ',', '.'),
            ];
        }

        // =============================================
        // CUMPLEAÑEROS DE LA SEMANA
        // =============================================
        $birthdayClients = Client::whereNotNull('birth_date')
            ->get()
            ->filter(fn($c) => $c->isBirthdayThisWeek())
            ->sortBy(fn($c) => $c->daysUntilBirthday());

        // =============================================
        // RESUMEN DEL DÍA
        // =============================================
        $todaySummary = [
            'works_created' => Work::whereDate('created_at', $today)->count(),
            'works_delivered' => Work::where('status', 'delivered')
                ->whereDate('actual_delivery', $today)->count(),
            'today_income' => Payment::whereDate('created_at', $today)->sum('amount'),
        ];

        return view('dashboard', compact(
            'stats', 
            'recentWorks', 
            'alerts', 
            'birthdayClients', 
            'todaySummary'
        ));
    }

    /**
     * Obtener trabajos demorados
     * Un trabajo se considera demorado si lleva más de 5 días 
     * sin cambiar de estado y no está entregado ni cancelado.
     */
    private function getDelayedWorks()
    {
        return Work::with(['client', 'laboratory', 'statusChanges'])
            ->whereNotIn('status', ['delivered', 'cancelled', 'ready'])
            ->get()
            ->filter(fn($w) => $w->is_delayed);
    }

    private function getDelayedWorksCount(): int
    {
        return $this->getDelayedWorks()->count();
    }
}