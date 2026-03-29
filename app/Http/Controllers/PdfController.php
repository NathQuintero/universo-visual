<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\Client;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Controlador: Generación de PDFs
 * 
 * Genera documentos PDF para:
 * - Ficha del trabajo (factura/recibo con fórmula, pagos, QR)
 * - Ficha del cliente (datos, fórmula actual, historial)
 * - Reportes (estadísticas del período)
 * 
 * Usa la librería DomPDF (barryvdh/laravel-dompdf)
 */
class PdfController extends Controller
{
    /**
     * PDF: Recibo/Factura de un Trabajo
     * Ruta: GET /pdf/trabajo/{work}
     * 
     * Incluye: datos del cliente, fórmula, especificaciones del lente,
     * detalle de precios, historial de abonos, código de seguimiento.
     */
    public function work(Work $work)
    {
        $work->load(['client', 'laboratory', 'formula', 'payments.user', 'statusChanges.user', 'user']);

        $businessName = Setting::getValue('business_name', 'Óptica Universo Visual');
        $businessPhone = Setting::getValue('business_phone', '6071234567');
        $businessAddress = Setting::getValue('business_address', 'C.C. La Isla, Local 205, Bucaramanga');

        $pdf = Pdf::loadView('pdf.work', compact('work', 'businessName', 'businessPhone', 'businessAddress'));
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('Recibo_' . $work->tracking_code . '.pdf');
    }

    /**
     * PDF: Ficha completa del Cliente
     * Ruta: GET /pdf/cliente/{client}
     * 
     * Incluye: datos personales, fórmula actual, historial de trabajos,
     * resumen de pagos.
     */
    public function client(Client $client)
    {
        $client->load(['works.laboratory', 'works.payments', 'formulas']);

        $businessName = Setting::getValue('business_name', 'Óptica Universo Visual');
        $businessPhone = Setting::getValue('business_phone', '6071234567');
        $businessAddress = Setting::getValue('business_address', 'C.C. La Isla, Local 205, Bucaramanga');

        $pdf = Pdf::loadView('pdf.client', compact('client', 'businessName', 'businessPhone', 'businessAddress'));
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('Cliente_' . $client->document_number . '.pdf');
    }

    /**
     * PDF: Reporte de Estadísticas
     * Ruta: GET /pdf/reportes
     * 
     * Incluye: KPIs, top clientes, rendimiento de laboratorios,
     * listado de trabajos del período.
     */
    public function report(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now()->endOfMonth();

        // KPIs
        $kpis = [
            'total_income' => \App\Models\Payment::whereBetween('created_at', [$startDate, $endDate])->sum('amount'),
            'clients_served' => Work::whereBetween('created_at', [$startDate, $endDate])->distinct('client_id')->count('client_id'),
            'works_created' => Work::whereBetween('created_at', [$startDate, $endDate])->count(),
            'works_delivered' => Work::where('status', 'delivered')->whereBetween('actual_delivery', [$startDate, $endDate])->count(),
        ];

        // Top clientes
        $topClients = Client::withCount('works')
            ->orderByDesc('works_count')
            ->limit(10)
            ->get()
            ->map(function ($client) {
                $client->total_spent = $client->works->sum('price_total');
                return $client;
            });

        // Laboratorios
        $laboratories = \App\Models\Laboratory::where('is_active', true)->get()->map(function ($lab) {
            return [
                'name' => $lab->name,
                'total_works' => $lab->works()->count(),
                'avg_days' => round($lab->averageDeliveryDays() ?? 0, 1),
                'compliance' => $lab->complianceRate(),
            ];
        });

        // Trabajos del período
        $works = Work::with(['client', 'laboratory'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->get();

        $businessName = Setting::getValue('business_name', 'Óptica Universo Visual');

        $pdf = Pdf::loadView('pdf.report', compact(
            'kpis', 'topClients', 'laboratories', 'works',
            'startDate', 'endDate', 'businessName'
        ));
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('Reporte_' . $startDate->format('Y-m-d') . '_a_' . $endDate->format('Y-m-d') . '.pdf');
    }
    /**
     * PDF público: Ver recibo en el navegador (sin descargar)
     * Ruta: GET /recibo/{work}
     * 
     * Este link se envía por WhatsApp para que el cliente
     * abra el PDF de su recibo directamente en el navegador.
     * No requiere login.
     */
    public function publicWork(Work $work)
    {
        $work->load(['client', 'laboratory', 'formula', 'payments.user', 'statusChanges.user', 'user']);

        $businessName = Setting::getValue('business_name', 'Óptica Universo Visual');
        $businessPhone = Setting::getValue('business_phone', '6071234567');
        $businessAddress = Setting::getValue('business_address', 'C.C. La Isla, Local 205, Bucaramanga');

        $pdf = Pdf::loadView('pdf.work', compact('work', 'businessName', 'businessPhone', 'businessAddress'));
        $pdf->setPaper('letter', 'portrait');

        // stream() muestra el PDF en el navegador en vez de descargarlo
        return $pdf->stream('Recibo_' . $work->tracking_code . '.pdf');
    }
}