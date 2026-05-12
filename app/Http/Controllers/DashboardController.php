<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Work;
use App\Models\Client;
use App\Models\Laboratory;
use App\Models\Payment;
use App\Models\User;
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
            'active_works' => Work::whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'ready_works' => Work::where('status', 'ready')->count(),
            'in_lab_works' => Work::whereIn('status', ['sent_to_lab', 'in_process'])->count(),
            'delayed_works' => $this->getDelayedWorksCount(),
            'monthly_income' => Payment::whereMonth('created_at', $today->month)
                ->whereYear('created_at', $today->year)
                ->sum('amount'),
        ];

        $recentWorks = Work::with(['client', 'laboratory'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Lista corta de alertas (para la columna derecha del dashboard)
        $alerts = $this->buildAlerts();

        $birthdayClients = Client::whereNotNull('birth_date')
            ->get()
            ->filter(fn($c) => $c->isBirthdayThisWeek())
            ->sortBy(fn($c) => $c->daysUntilBirthday());

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
     * Página dedicada de alertas: lista completa con botones de acción
     * y detalle expandible.
     * Ruta: GET /alertas
     */
    public function alerts()
    {
        $alerts = $this->buildAlerts();

        // Agrupar para mostrar contadores por categoría
        $groups = [
            'danger'  => collect($alerts)->where('type', 'danger')->values(),
            'warning' => collect($alerts)->where('type', 'warning')->values(),
            'success' => collect($alerts)->where('type', 'success')->values(),
            'info'    => collect($alerts)->where('type', 'info')->values(),
        ];

        return view('alerts.index', compact('alerts', 'groups'));
    }

    /**
     * Conteo rápido de alertas (usado por el sidebar para el badge).
     * Versión ligera: no construye los detalles de cada alerta.
     */
    public static function alertsCountFor(?User $user = null): int
    {
        $count = 0;

        // Demorados
        $count += Work::with('statusChanges')
            ->whereNotIn('status', ['delivered', 'cancelled', 'ready'])
            ->get()
            ->filter(fn($w) => $w->is_delayed)
            ->count();

        // Listos sin recoger
        $count += Work::where('status', 'ready')
            ->get()
            ->filter(fn($w) => $w->is_waiting_pickup)
            ->count();

        // Saldos pendientes (cap a 20 para coincidir con buildAlerts)
        $pendingCount = Work::where('status', '!=', 'cancelled')
            ->get()
            ->filter(fn($w) => $w->pending_balance > 0)
            ->count();
        $count += min($pendingCount, 20);

        // Pagos al laboratorio agrupados (overdue, due, due_soon → cada uno es 1 alerta)
        $labWorks = Work::with('statusChanges')
            ->where('status', '!=', 'cancelled')
            ->whereNull('lab_paid_at')
            ->get();
        foreach (['overdue', 'due', 'due_soon'] as $st) {
            if ($labWorks->filter(fn($w) => $w->lab_payment_status === $st)->isNotEmpty()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Construye la lista completa de alertas del sistema.
     * Cada alerta tiene: id, type, icon, title, message,
     * details (array), actions (array de botones).
     */
    private function buildAlerts(): array
    {
        $alerts = [];

        // -------------------------------------------------
        // 1) Trabajos demorados en laboratorio (>5 días)
        // -------------------------------------------------
        foreach ($this->getDelayedWorks() as $work) {
            $actions = [
                [
                    'label' => 'Ver trabajo',
                    'icon'  => '📋',
                    'url'   => route('works.show', $work),
                    'style' => 'primary',
                ],
            ];

            if ($work->client->phone && $work->client->whatsapp_authorized) {
                $waPhone = '57' . preg_replace('/[^0-9]/', '', $work->client->phone);
                $msg = "Hola {$work->client->first_name}, te escribimos desde Óptica Universo Visual. "
                    . "Queríamos darte una actualización sobre tu pedido {$work->tracking_code}. "
                    . "Estamos coordinando con el laboratorio para que llegue lo antes posible. "
                    . "Cualquier novedad te avisamos. ¡Gracias por tu paciencia!";
                $actions[] = [
                    'label' => 'WhatsApp al cliente',
                    'icon'  => '💬',
                    'url'   => 'https://wa.me/' . $waPhone . '?text=' . rawurlencode($msg),
                    'style' => 'whatsapp',
                    'target' => '_blank',
                ];
            }

            $alerts[] = [
                'id'      => 'delayed-' . $work->id,
                'type'    => 'danger',
                'icon'    => '⏰',
                'title'   => 'Trabajo demorado en laboratorio',
                'message' => $work->tracking_code . ' • ' . $work->days_elapsed . ' días sin actualización',
                'details' => [
                    'Cliente'        => $work->client->full_name,
                    'Teléfono'       => $work->client->phone ?: '—',
                    'Laboratorio'    => $work->laboratory->name,
                    'Estado actual'  => $work->status_emoji . ' ' . $work->status_name,
                    'Tipo de lente'  => $work->lens_type_name,
                    'Tratamientos'   => $work->treatments_text,
                    'Días en el sistema' => $work->days_elapsed . ' días',
                ],
                'actions' => $actions,
            ];
        }

        // -------------------------------------------------
        // 2) Trabajos listos sin recoger (>3 días)
        // -------------------------------------------------
        $waitingPickup = Work::with('client', 'laboratory')
            ->where('status', 'ready')
            ->get()
            ->filter(fn($w) => $w->is_waiting_pickup);

        foreach ($waitingPickup as $work) {
            $actions = [
                [
                    'label' => 'Ver trabajo',
                    'icon'  => '📋',
                    'url'   => route('works.show', $work),
                    'style' => 'primary',
                ],
            ];

            if ($work->client->phone && $work->client->whatsapp_authorized) {
                $waPhone = '57' . preg_replace('/[^0-9]/', '', $work->client->phone);
                $msg = "Hola {$work->client->first_name}, ¡tus gafas ya están listas en Óptica Universo Visual! "
                    . "Te esperamos para que pases a recogerlas cuando quieras. "
                    . "Código de seguimiento: {$work->tracking_code}.";
                $actions[] = [
                    'label' => 'Avisar por WhatsApp',
                    'icon'  => '💬',
                    'url'   => 'https://wa.me/' . $waPhone . '?text=' . rawurlencode($msg),
                    'style' => 'whatsapp',
                    'target' => '_blank',
                ];
            }

            $alerts[] = [
                'id'      => 'pickup-' . $work->id,
                'type'    => 'success',
                'icon'    => '✅',
                'title'   => 'Listo sin recoger',
                'message' => $work->client->first_name . ' — ' . $work->tracking_code,
                'details' => [
                    'Cliente'       => $work->client->full_name,
                    'Teléfono'      => $work->client->phone ?: '—',
                    'Tipo de lente' => $work->lens_type_name,
                    'Total'         => '$' . number_format($work->price_total, 0, ',', '.'),
                    'Saldo'         => '$' . number_format($work->pending_balance, 0, ',', '.'),
                ],
                'actions' => $actions,
            ];
        }

        // -------------------------------------------------
        // 3) Trabajos con saldo pendiente
        // -------------------------------------------------
        $pendingPayments = Work::with('client')
            ->where('status', '!=', 'cancelled')
            ->get()
            ->filter(fn($w) => $w->pending_balance > 0)
            ->sortByDesc('pending_balance');

        foreach ($pendingPayments->take(20) as $work) {
            $actions = [
                [
                    'label' => 'Ver trabajo',
                    'icon'  => '📋',
                    'url'   => route('works.show', $work),
                    'style' => 'primary',
                ],
            ];

            if ($work->client->phone && $work->client->whatsapp_authorized) {
                $waPhone = '57' . preg_replace('/[^0-9]/', '', $work->client->phone);
                $msg = "Hola {$work->client->first_name}, te saludamos desde Óptica Universo Visual. "
                    . "Te recordamos amablemente que tu pedido {$work->tracking_code} tiene un saldo pendiente de "
                    . "$" . number_format($work->pending_balance, 0, ',', '.') . ". "
                    . "Cuando puedas, nos cuentas para coordinar el pago. ¡Gracias!";
                $actions[] = [
                    'label' => 'Recordar por WhatsApp',
                    'icon'  => '💬',
                    'url'   => 'https://wa.me/' . $waPhone . '?text=' . rawurlencode($msg),
                    'style' => 'whatsapp',
                    'target' => '_blank',
                ];
            }

            $alerts[] = [
                'id'      => 'balance-' . $work->id,
                'type'    => 'warning',
                'icon'    => '💰',
                'title'   => 'Saldo pendiente',
                'message' => $work->client->full_name . ' debe $' . number_format($work->pending_balance, 0, ',', '.'),
                'details' => [
                    'Cliente'      => $work->client->full_name,
                    'Teléfono'     => $work->client->phone ?: '—',
                    'Código'       => $work->tracking_code,
                    'Total'        => '$' . number_format($work->price_total, 0, ',', '.'),
                    'Abonado'      => '$' . number_format($work->total_paid, 0, ',', '.'),
                    'Saldo'        => '$' . number_format($work->pending_balance, 0, ',', '.'),
                    'Estado'       => $work->status_emoji . ' ' . $work->status_name,
                ],
                'actions' => $actions,
            ];
        }

        // -------------------------------------------------
        // 4) Pagos al laboratorio
        // -------------------------------------------------
        $labWorks = Work::with(['statusChanges', 'laboratory', 'client'])
            ->where('status', '!=', 'cancelled')
            ->whereNull('lab_paid_at')
            ->get();

        $labOverdue = $labWorks->filter(fn($w) => $w->lab_payment_status === 'overdue');
        $labDue     = $labWorks->filter(fn($w) => $w->lab_payment_status === 'due');
        $labDueSoon = $labWorks->filter(fn($w) => $w->lab_payment_status === 'due_soon');

        if ($labOverdue->count() > 0) {
            $byLabSummary = $labOverdue->groupBy('laboratory.name')
                ->map(fn($items, $name) => $items->count() . ' en ' . $name)
                ->values()->implode(', ');

            $details = [];
            foreach ($labOverdue->take(8) as $w) {
                $details[$w->tracking_code] = $w->laboratory->name . ' • ' . $w->days_owed_to_lab . ' días';
            }

            $alerts[] = [
                'id'      => 'lab-overdue',
                'type'    => 'danger',
                'icon'    => '🔥',
                'title'   => $labOverdue->count() . ' lente(s) FUERA DE PLAZO con el laboratorio',
                'message' => 'Pasaron 30 días sin pagar — ' . $byLabSummary,
                'details' => $details,
                'actions' => [
                    [
                        'label' => 'Ver laboratorios',
                        'icon'  => '🏭',
                        'url'   => route('laboratories.index'),
                        'style' => 'primary',
                    ],
                ],
            ];
        }

        if ($labDue->count() > 0) {
            $byLabSummary = $labDue->groupBy('laboratory.name')
                ->map(fn($items, $name) => $items->count() . ' en ' . $name)
                ->values()->implode(', ');

            $details = [];
            foreach ($labDue->take(8) as $w) {
                $details[$w->tracking_code] = $w->laboratory->name . ' • ' . $w->days_owed_to_lab . ' días';
            }

            $alerts[] = [
                'id'      => 'lab-due',
                'type'    => 'warning',
                'icon'    => '⏰',
                'title'   => $labDue->count() . ' lente(s) +15 días sin pagar al lab',
                'message' => 'Coordinar pago al laboratorio cuanto antes. ' . $byLabSummary,
                'details' => $details,
                'actions' => [
                    [
                        'label' => 'Ver laboratorios',
                        'icon'  => '🏭',
                        'url'   => route('laboratories.index'),
                        'style' => 'primary',
                    ],
                ],
            ];
        }

        if ($labDueSoon->count() > 0) {
            $byLabSummary = $labDueSoon->groupBy('laboratory.name')
                ->map(fn($items, $name) => $items->count() . ' en ' . $name)
                ->values()->implode(', ');

            $details = [];
            foreach ($labDueSoon->take(8) as $w) {
                $details[$w->tracking_code] = $w->laboratory->name . ' • ' . $w->days_owed_to_lab . ' días';
            }

            $alerts[] = [
                'id'      => 'lab-due-soon',
                'type'    => 'warning',
                'icon'    => '📅',
                'title'   => $labDueSoon->count() . ' lente(s) próximos a 15 días',
                'message' => 'Preparar pago al laboratorio en los próximos días. ' . $byLabSummary,
                'details' => $details,
                'actions' => [
                    [
                        'label' => 'Ver laboratorios',
                        'icon'  => '🏭',
                        'url'   => route('laboratories.index'),
                        'style' => 'primary',
                    ],
                ],
            ];
        }

        return $alerts;
    }

    /**
     * Trabajos demorados: >5 días sin cambio de estado y aún activos.
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
