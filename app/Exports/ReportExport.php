<?php

namespace App\Exports;

use App\Models\Work;
use App\Models\Payment;
use App\Models\Client;
use App\Models\Laboratory;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportExport implements FromArray, WithStyles, WithTitle, ShouldAutoSize
{
    protected Carbon $startDate;
    protected Carbon $endDate;
    protected int $kpiEndRow;
    protected int $labHeaderRow;

    public function __construct(Carbon $startDate, Carbon $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Reporte';
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = ['REPORTE FINANCIERO - ÓPTICA UNIVERSO VISUAL'];
        $rows[] = ['Período: ' . $this->startDate->format('d/m/Y') . ' al ' . $this->endDate->format('d/m/Y') . ' — Exportado el ' . now()->format('d/m/Y H:i')];
        $rows[] = [];

        // KPIs
        $totalIncome = Work::whereBetween('created_at', [$this->startDate, $this->endDate])->sum('price_total');
        $totalPaid = Payment::whereBetween('created_at', [$this->startDate, $this->endDate])->sum('amount');
        $totalPending = $totalIncome - $totalPaid;
        $worksCount = Work::whereBetween('created_at', [$this->startDate, $this->endDate])->count();
        $clientsServed = Work::whereBetween('created_at', [$this->startDate, $this->endDate])->distinct('client_id')->count('client_id');
        $avgTicket = $worksCount > 0 ? $totalIncome / $worksCount : 0;
        $delivered = Work::where('status', 'delivered')->whereBetween('actual_delivery', [$this->startDate, $this->endDate])->count();
        $inProcess = Work::whereIn('status', ['sent_to_lab', 'in_process', 'received'])->count();
        $delayed = Work::whereNotIn('status', ['delivered', 'cancelled'])->get()->filter(fn($w) => $w->is_delayed)->count();

        $avgDays = 0;
        $deliveredWorks = Work::where('status', 'delivered')->whereNotNull('actual_delivery')->whereBetween('actual_delivery', [$this->startDate, $this->endDate])->get();
        if ($deliveredWorks->count() > 0) {
            $avgDays = round($deliveredWorks->avg(fn($w) => Carbon::parse($w->created_at)->diffInDays($w->actual_delivery)), 1);
        }

        $rows[] = ['INDICADORES CLAVE (KPIs)', ''];
        $rows[] = ['Total Ingresos (facturado)', $totalIncome];
        $rows[] = ['Total Cobrado', $totalPaid];
        $rows[] = ['Total Pendiente por Cobrar', $totalPending];
        $rows[] = ['Cantidad de Trabajos', $worksCount];
        $rows[] = ['Clientes Atendidos', $clientsServed];
        $rows[] = ['Ticket Promedio', $avgTicket];
        $rows[] = ['Tiempo Promedio de Entrega', $avgDays . ' días'];
        $rows[] = ['Trabajos Entregados', $delivered];
        $rows[] = ['Trabajos en Proceso', $inProcess];
        $rows[] = ['Trabajos Demorados', $delayed];
        $rows[] = [];

        $this->kpiEndRow = count($rows);

        // Rendimiento por laboratorio
        $rows[] = ['RENDIMIENTO POR LABORATORIO'];
        $this->labHeaderRow = count($rows) + 1;
        $rows[] = ['Laboratorio', 'Cant. Trabajos', 'Entregados', 'Demorados', 'Prom. Días', '% Cumplimiento'];

        $labs = Laboratory::where('is_active', true)->get();
        foreach ($labs as $lab) {
            $labWorks = $lab->works()->whereBetween('created_at', [$this->startDate, $this->endDate])->get();
            $labDelivered = $labWorks->where('status', 'delivered')->count();
            $labDelayed = $labWorks->filter(fn($w) => $w->is_delayed)->count();
            $labAvgDays = round($lab->averageDeliveryDays() ?? 0, 1);
            $compliance = $labWorks->count() > 0 ? round(($labDelivered / $labWorks->count()) * 100) : 0;

            $rows[] = [
                $lab->name,
                $labWorks->count(),
                $labDelivered,
                $labDelayed,
                $labAvgDays . ' días',
                $compliance . '%',
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Título
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '103192']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Período
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 11, 'color' => ['rgb' => '6C757D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // KPIs header
        $sheet->mergeCells('A4:B4');
        $sheet->getStyle('A4:B4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '103192']],
        ]);

        // KPIs values
        for ($i = 5; $i <= 14; $i++) {
            $sheet->getStyle("A{$i}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '4A5568']],
            ]);
            $sheet->getStyle("B{$i}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 12],
            ]);
        }

        // Moneda en KPIs
        foreach ([5, 6, 7, 10] as $r) {
            $sheet->getStyle("B{$r}")->getNumberFormat()->setFormatCode('$#,##0');
        }

        // Lab section header
        $labTitleRow = $this->kpiEndRow + 1;
        $labHeaderRow = $this->labHeaderRow;

        $sheet->mergeCells("A{$labTitleRow}:F{$labTitleRow}");
        $sheet->getStyle("A{$labTitleRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '103192']],
        ]);

        $sheet->getStyle("A{$labHeaderRow}:F{$labHeaderRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A4FD0']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '103192']]],
        ]);

        // Lab data with alternating rows
        for ($i = $labHeaderRow + 1; $i <= $lastRow; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle("A{$i}:F{$i}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F7FA']],
                ]);
            }
        }

        $sheet->getStyle("A{$labHeaderRow}:F{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D6DCE8']]],
        ]);

        return [];
    }
}
