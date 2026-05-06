<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Laboratory;
use App\Models\Payment;
use App\Models\Work;
use App\Models\WorkStatusChange;
use Carbon\Carbon;

/**
 * Servicio: Análisis de la pregunta del admin y consulta a la BD.
 *
 * Detecta palabras clave en español y devuelve un bloque de texto con
 * los datos reales del sistema. Ese bloque se inyecta en el prompt
 * que se envía a Groq para que la IA responda con datos concretos.
 *
 * Si no detecta ninguna palabra clave devuelve null y el chat responde
 * únicamente con el conocimiento del modelo.
 */
class ChatDataService
{
    public function analyzeAndQuery(string $message, bool $includeFinancials = true): ?string
    {
        $msg = $this->normalize($message);

        // Atajo: informe completo — todos los bloques de una vez
        if ($this->matches($msg, ['informe completo', 'reporte completo', 'informe general', 'reporte general', 'informe', 'reporte', 'panorama completo', 'genera un informe', 'generame un informe'])) {
            $blocks = [
                $this->worksOverview(),
                $this->delayedWorks(),
                $this->readyForPickup(),
            ];
            if ($includeFinancials) {
                $blocks[] = $this->monthlyIncome();
            }
            $blocks[] = $this->pendingBalances($includeFinancials);
            $blocks[] = $this->clientsSummary();
            $blocks[] = $this->birthdaysThisWeek();
            $blocks[] = $this->labStats();
            $blocks[] = $this->labPaymentsDue($includeFinancials);
            $blocks[] = $this->todaySummary($includeFinancials);
            return "DATOS DEL SISTEMA — INFORME COMPLETO:\n\n" . implode("\n\n", array_filter($blocks));
        }

        $blocks = [];

        // Detectar mención de una empleada por su nombre y devolver sus datos reales
        $employeeBlock = $this->employeeStats($msg, $includeFinancials);
        if ($employeeBlock) {
            $blocks[] = $employeeBlock;
        }

        if ($this->matches($msg, ['demorado', 'demorados', 'retrasado', 'retrasados', 'atraso', 'atrasos', 'tarde'])) {
            $blocks[] = $this->delayedWorks();
        }

        if ($includeFinancials && $this->matches($msg, ['ingreso', 'ingresos', 'venta', 'ventas', 'facturado', 'facturacion', 'factura', 'dinero', 'plata', 'gane', 'ganancia'])) {
            $blocks[] = $this->monthlyIncome();
        }

        if ($this->matches($msg, ['cliente', 'clientes', 'paciente', 'pacientes'])) {
            $blocks[] = $this->clientsSummary();
        }

        if ($this->matches($msg, ['cumpleanos', 'cumple', 'cumpleano', 'felicit'])) {
            $blocks[] = $this->birthdaysThisWeek();
        }

        if ($this->matches($msg, ['saldo', 'saldos', 'deuda', 'deudas', 'debe', 'cobrar', 'pendiente de pago', 'pendientes de pago', 'cartera'])) {
            $blocks[] = $this->pendingBalances($includeFinancials);
        }

        if ($this->matches($msg, ['laboratorio', 'laboratorios', 'lab '])) {
            $blocks[] = $this->labStats();
        }

        if ($this->matches($msg, ['debo al lab', 'debemos al lab', 'pago al lab', 'pagar al lab', 'pagar a los lab', 'deuda lab', 'deudas lab', 'deuda con el lab', 'deudas con los lab', 'pagos al lab', 'pagar laboratorio', 'pagar laboratorios', 'pago laboratorio', 'lentes por pagar', 'pago al laboratorio'])) {
            $blocks[] = $this->labPaymentsDue($includeFinancials);
        }

        if ($this->matches($msg, ['hoy', 'resumen', 'dia de hoy', 'jornada'])) {
            $blocks[] = $this->todaySummary($includeFinancials);
        }

        if ($this->matches($msg, ['listo', 'listos', 'pendientes de entrega', 'por entregar', 'sin recoger', 'recoger', 'entrega'])) {
            $blocks[] = $this->readyForPickup();
        }

        // Catch-all amplio: cualquier mención a trabajos/pedidos/órdenes/activos
        // devuelve un panorama general por estado. Esto evita que la IA
        // diga "no tengo acceso a la BD" cuando la pregunta es genérica.
        if ($this->matches($msg, ['trabajo', 'trabajos', 'pedido', 'pedidos', 'orden', 'ordenes', 'activo', 'activos', 'cuantos hay', 'estado', 'estados', 'proceso'])) {
            $blocks[] = $this->worksOverview();
        }

        $blocks = array_filter($blocks);
        if (empty($blocks)) {
            return null;
        }

        return "DATOS DEL SISTEMA (consultados en tiempo real):\n\n" . implode("\n\n", $blocks);
    }

    // ==========================================
    // CONSULTAS
    // ==========================================

    private function delayedWorks(): string
    {
        $works = Work::with('client')
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->get()
            ->filter(fn($w) => $w->is_delayed)
            ->take(10);

        if ($works->isEmpty()) {
            return "TRABAJOS DEMORADOS: 0 (todo al día).";
        }

        $lines = ["TRABAJOS DEMORADOS (más de 5 días sin avance): {$works->count()}"];
        foreach ($works as $w) {
            $lines[] = "- {$w->tracking_code} | {$w->client->full_name} | estado: {$w->status_name} | hace {$w->days_elapsed} días";
        }
        return implode("\n", $lines);
    }

    private function monthlyIncome(): string
    {
        $now = Carbon::now();
        $thisMonth = (float) Work::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('price_total');

        $last = $now->copy()->subMonth();
        $lastMonth = (float) Work::whereYear('created_at', $last->year)
            ->whereMonth('created_at', $last->month)
            ->sum('price_total');

        $paid = (float) Payment::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('amount');

        $diff = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : null;

        $lines = [
            "INGRESOS MES ACTUAL ({$now->translatedFormat('F Y')}):",
            "- Facturado (price_total trabajos creados): $" . number_format($thisMonth, 0, ',', '.'),
            "- Cobrado (pagos recibidos): $" . number_format($paid, 0, ',', '.'),
            "- Mes anterior: $" . number_format($lastMonth, 0, ',', '.'),
        ];
        if ($diff !== null) {
            $lines[] = "- Variación vs mes anterior: " . ($diff >= 0 ? "+{$diff}%" : "{$diff}%");
        }
        return implode("\n", $lines);
    }

    private function clientsSummary(): string
    {
        $total = Client::count();
        $newThisMonth = Client::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        return "CLIENTES:\n- Total registrados: {$total}\n- Nuevos este mes: {$newThisMonth}";
    }

    private function birthdaysThisWeek(): string
    {
        $clients = Client::whereNotNull('birth_date')
            ->get()
            ->filter(fn($c) => $c->isBirthdayThisWeek())
            ->take(15);

        if ($clients->isEmpty()) {
            return "CUMPLEAÑOS ESTA SEMANA: ninguno.";
        }

        $lines = ["CUMPLEAÑOS ESTA SEMANA: {$clients->count()}"];
        foreach ($clients as $c) {
            $days = $c->daysUntilBirthday();
            $when = $c->isBirthdayToday() ? '¡hoy!' : "en {$days} días";
            $lines[] = "- {$c->full_name} ({$c->birth_date->format('d/m')}) — {$when}";
        }
        return implode("\n", $lines);
    }

    private function pendingBalances(bool $includeFinancials = true): string
    {
        $works = Work::with('client')
            ->whereNotIn('status', ['cancelled'])
            ->get()
            ->filter(fn($w) => $w->pending_balance > 0)
            ->sortByDesc(fn($w) => $w->pending_balance);

        if ($works->isEmpty()) {
            return "SALDOS PENDIENTES: ninguno (todos al día).";
        }

        $lines = ["SALDOS PENDIENTES:"];
        $lines[] = "- Trabajos con saldo: {$works->count()}";

        if ($includeFinancials) {
            $total = $works->sum(fn($w) => $w->pending_balance);
            $lines[] = "- Total por cobrar: $" . number_format($total, 0, ',', '.');
        }

        $lines[] = "- Top 5 (clientes a contactar para cobrar):";
        foreach ($works->take(5) as $w) {
            $amount = $includeFinancials
                ? ' | $' . number_format($w->pending_balance, 0, ',', '.')
                : '';
            $lines[] = "  · {$w->tracking_code} | {$w->client->full_name} | tel: " . ($w->client->phone ?? 's/n') . $amount;
        }
        return implode("\n", $lines);
    }

    /**
     * Lentes que la óptica le debe a los laboratorios, agrupados por
     * urgencia (>30 días, 15-30, 12-15) y desglosados por laboratorio.
     */
    private function labPaymentsDue(bool $includeFinancials = true): string
    {
        $works = Work::with(['laboratory', 'client', 'statusChanges' => fn($q) => $q->where('to_status', 'received')])
            ->where('status', '!=', 'cancelled')
            ->whereNull('lab_paid_at')
            ->get();

        $overdue = $works->filter(fn($w) => $w->lab_payment_status === 'overdue');
        $due     = $works->filter(fn($w) => $w->lab_payment_status === 'due');
        $dueSoon = $works->filter(fn($w) => $w->lab_payment_status === 'due_soon');

        if ($overdue->isEmpty() && $due->isEmpty() && $dueSoon->isEmpty()) {
            return "PAGOS AL LABORATORIO: ninguno urgente. Todo en plazo cómodo (<12 días) o ya pagado.";
        }

        $lines = ["PAGOS AL LABORATORIO PENDIENTES:"];

        if ($overdue->isNotEmpty()) {
            $lines[] = "";
            $lines[] = "🔥 PASARON 30 DÍAS — fuera del plazo del lab ({$overdue->count()})";
            foreach ($overdue->take(10) as $w) {
                $cost = $includeFinancials && $w->lab_cost > 0
                    ? ' | $' . number_format($w->lab_cost, 0, ',', '.')
                    : '';
                $lines[] = "  · {$w->tracking_code} | {$w->laboratory->name} | {$w->days_owed_to_lab} días" . $cost;
            }
        }

        if ($due->isNotEmpty()) {
            $lines[] = "";
            $lines[] = "⏰ +15 DÍAS sin pagar ({$due->count()})";
            foreach ($due->take(10) as $w) {
                $cost = $includeFinancials && $w->lab_cost > 0
                    ? ' | $' . number_format($w->lab_cost, 0, ',', '.')
                    : '';
                $lines[] = "  · {$w->tracking_code} | {$w->laboratory->name} | {$w->days_owed_to_lab} días" . $cost;
            }
        }

        if ($dueSoon->isNotEmpty()) {
            $lines[] = "";
            $lines[] = "📅 PRÓXIMOS A 15 DÍAS ({$dueSoon->count()})";
            foreach ($dueSoon->take(10) as $w) {
                $cost = $includeFinancials && $w->lab_cost > 0
                    ? ' | $' . number_format($w->lab_cost, 0, ',', '.')
                    : '';
                $lines[] = "  · {$w->tracking_code} | {$w->laboratory->name} | {$w->days_owed_to_lab} días" . $cost;
            }
        }

        if ($includeFinancials) {
            $totalDebt = (float) $overdue->sum('lab_cost') + (float) $due->sum('lab_cost') + (float) $dueSoon->sum('lab_cost');
            $lines[] = "";
            $lines[] = "💰 Total a pagar a laboratorios: $" . number_format($totalDebt, 0, ',', '.');
        }

        return implode("\n", $lines);
    }

    private function labStats(): string
    {
        $labs = Laboratory::where('is_active', true)->get();

        if ($labs->isEmpty()) {
            return "LABORATORIOS: ninguno activo.";
        }

        $lines = ["RENDIMIENTO DE LABORATORIOS:"];
        foreach ($labs as $lab) {
            $active = $lab->activeWorks()->count();
            $delivered = $lab->works()->where('status', 'delivered')->count();
            $compliance = $lab->complianceRate();
            $lines[] = "- {$lab->name}: {$active} activos | {$delivered} entregados | cumplimiento {$compliance}%";
        }
        return implode("\n", $lines);
    }

    private function todaySummary(bool $includeFinancials = true): string
    {
        $today = Carbon::today();
        $worksCreated = Work::whereDate('created_at', $today)->count();
        $paymentsCount = Payment::whereDate('created_at', $today)->count();
        $statusChanges = WorkStatusChange::whereDate('created_at', $today)->count();
        $delivered = Work::whereDate('updated_at', $today)->where('status', 'delivered')->count();

        $paymentsLine = $includeFinancials
            ? "- Pagos registrados: {$paymentsCount} (total: $" . number_format((float) Payment::whereDate('created_at', $today)->sum('amount'), 0, ',', '.') . ")"
            : "- Pagos registrados: {$paymentsCount}";

        return "RESUMEN DE HOY ({$today->translatedFormat('l j \\d\\e F')}):\n"
            . "- Trabajos creados: {$worksCreated}\n"
            . "- Cambios de estado: {$statusChanges}\n"
            . $paymentsLine . "\n"
            . "- Entregados hoy: {$delivered}";
    }

    /**
     * Si el mensaje menciona el nombre de una empleada, devuelve sus
     * estadísticas reales (cantidad de trabajos, valor facturado, pagos
     * recibidos). Si menciona "trabajadora/vendedora/empleada" sin nombre,
     * devuelve un comparativo de todas. Esto evita que la IA invente cifras.
     */
    private function employeeStats(string $msg, bool $includeFinancials = true): ?string
    {
        $employees = Employee::all();
        if ($employees->isEmpty()) return null;

        // 1) Buscar mención específica por nombre (Maira, Nelly...)
        $matched = collect();
        foreach ($employees as $emp) {
            $name = $this->normalize($emp->name);
            $first = explode(' ', $name)[0] ?? $name;
            if (mb_strlen($first) < 4) continue; // evitar falsos positivos con nombres cortos
            if (preg_match('/\b' . preg_quote($first, '/') . '\b/u', $msg)) {
                $matched->push($emp);
            }
        }

        // 2) Si no hubo match por nombre, ¿pregunta genérica por trabajadoras?
        $askedAll = $matched->isEmpty()
            && $this->matches($msg, ['trabajadora', 'trabajadoras', 'vendedora', 'vendedoras', 'empleada', 'empleadas']);

        if ($matched->isEmpty() && !$askedAll) return null;

        $targets = $askedAll ? $employees : $matched;
        $now = Carbon::now();

        $lines = ['ESTADÍSTICAS POR TRABAJADORA (datos reales de la BD):'];
        foreach ($targets as $emp) {
            $works = $emp->works()->get();
            $payments = $emp->payments()->get();

            $worksTotal = $works->count();
            $worksThisMonth = $works->filter(fn($w) => $w->created_at->isSameMonth($now))->count();
            $worksActive = $works->whereNotIn('status', ['delivered', 'cancelled'])->count();
            $worksDelivered = $works->where('status', 'delivered')->count();

            $paymentsCount = $payments->count();
            $paymentsThisMonthCount = $payments->filter(fn($p) => $p->created_at->isSameMonth($now))->count();

            $lines[] = "";
            $lines[] = "👩‍💼 {$emp->name}" . ($emp->is_active ? '' : ' (inactiva)');
            $lines[] = "  · Trabajos atendidos: {$worksTotal} (este mes: {$worksThisMonth})";
            $lines[] = "  · En curso: {$worksActive} | Entregados: {$worksDelivered}";
            $lines[] = "  · Pagos recibidos: {$paymentsCount} (este mes: {$paymentsThisMonthCount})";

            if ($includeFinancials) {
                $valueTotal = (float) $works->sum('price_total');
                $valueThisMonth = (float) $works->filter(fn($w) => $w->created_at->isSameMonth($now))->sum('price_total');
                $cashedTotal = (float) $payments->sum('amount');
                $cashedThisMonth = (float) $payments->filter(fn($p) => $p->created_at->isSameMonth($now))->sum('amount');

                $lines[] = "  · Valor facturado total: $" . number_format($valueTotal, 0, ',', '.')
                    . " (este mes: $" . number_format($valueThisMonth, 0, ',', '.') . ")";
                $lines[] = "  · Total cobrado: $" . number_format($cashedTotal, 0, ',', '.')
                    . " (este mes: $" . number_format($cashedThisMonth, 0, ',', '.') . ")";
            }
        }

        return implode("\n", $lines);
    }

    private function worksOverview(): string
    {
        $byStatus = Work::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $labels = [
            'registered'  => '📝 Registrados',
            'sent_to_lab' => '📦 Enviados al lab',
            'in_process'  => '🔬 En proceso',
            'received'    => '📬 Recibidos',
            'ready'       => '✅ Listos',
            'delivered'   => '🎉 Entregados',
            'cancelled'   => '❌ Cancelados',
        ];

        $total = array_sum($byStatus);
        $active = 0;
        foreach (['registered', 'sent_to_lab', 'in_process', 'received', 'ready'] as $s) {
            $active += $byStatus[$s] ?? 0;
        }

        $lines = ["TRABAJOS — PANORAMA GENERAL:"];
        $lines[] = "- Total histórico: {$total}";
        $lines[] = "- Activos (sin entregar/cancelar): {$active}";
        foreach ($labels as $key => $label) {
            $count = $byStatus[$key] ?? 0;
            $lines[] = "  · {$label}: {$count}";
        }
        return implode("\n", $lines);
    }

    private function readyForPickup(): string
    {
        $works = Work::with('client')->where('status', 'ready')->get();

        if ($works->isEmpty()) {
            return "TRABAJOS LISTOS PARA ENTREGA: ninguno.";
        }

        $lines = ["TRABAJOS LISTOS PARA ENTREGA: {$works->count()}"];
        foreach ($works->take(10) as $w) {
            $waiting = $w->is_waiting_pickup ? ' ⚠️ (>3 días esperando)' : '';
            $lines[] = "- {$w->tracking_code} | {$w->client->full_name} | tel: " . ($w->client->phone ?? 's/n') . $waiting;
        }
        return implode("\n", $lines);
    }

    // ==========================================
    // HELPERS
    // ==========================================

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $from = ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'];
        $to = ['a', 'e', 'i', 'o', 'u', 'n', 'u'];
        return str_replace($from, $to, $text);
    }

    private function matches(string $haystack, array $needles): bool
    {
        foreach ($needles as $n) {
            if (str_contains($haystack, $n)) return true;
        }
        return false;
    }
}
