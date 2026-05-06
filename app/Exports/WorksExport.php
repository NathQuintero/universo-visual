<?php

namespace App\Exports;

use App\Models\Work;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class WorksExport implements FromArray, WithStyles, WithTitle, ShouldAutoSize
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function title(): string
    {
        return 'Trabajos';
    }

    public function array(): array
    {
        $query = Work::with(['client', 'laboratory', 'formula', 'payments', 'user']);

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }
        if ($this->request->filled('laboratory')) {
            $query->where('laboratory_id', $this->request->laboratory);
        }
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tracking_code', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($q2) => $q2->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('document_number', 'like', "%{$search}%"));
            });
        }

        $works = $query->orderByDesc('created_at')->get();

        // Fila 1: Título
        $rows = [];
        $rows[] = ['TRABAJOS - ÓPTICA UNIVERSO VISUAL — Exportado el ' . now()->format('d/m/Y H:i')];

        // Fila 2: Instrucción
        $rows[] = ['¿SE FUE LA LUZ? Agregue los trabajos nuevos al final. Deje la columna A (Código) VACÍA para los nuevos. Cuando vuelva la luz, suba este archivo al sistema.'];

        // Fila 3: Categorías
        $rows[] = [
            'SISTEMA', 'DATOS DEL CLIENTE', '', '', '', '', '', '',
            'DATOS DEL TRABAJO', '', '', '', '', '', '', '',
            'FÓRMULA OD', '', '', '',
            'FÓRMULA OI', '', '', '',
            'PRECIOS Y PAGOS', '', '', '', 'OTROS',
        ];

        // Fila 4: Headers
        $rows[] = [
            'Código Seguimiento',
            'Nombre', 'Apellido', 'Tipo Doc', 'Nro Documento', 'Teléfono', 'Email', 'Dirección',
            'Laboratorio', 'Tipo Lente', 'Material', 'Antirreflejo', 'Fotocromático', 'Filtro Azul', 'Polarizado', 'Montura',
            'OD Esfera', 'OD Cilindro', 'OD Eje', 'OD ADD',
            'OI Esfera', 'OI Cilindro', 'OI Eje', 'OI ADD',
            '$ Lentes', '$ Montura', '$ Total', '$ Abono', 'Observaciones',
        ];

        // Datos
        $totLentes = 0; $totMontura = 0; $totTotal = 0; $totAbono = 0;

        foreach ($works as $work) {
            $f = $work->formula;
            $montura = ($work->frame_type == 'own' ? 'Propia' : 'Comprada');
            if ($work->frame_brand) $montura .= ' - ' . $work->frame_brand;
            if ($work->frame_reference) $montura .= ' ' . $work->frame_reference;

            $abono = $work->total_paid;
            $totLentes += (float)$work->price_lenses;
            $totMontura += (float)$work->price_frame;
            $totTotal += (float)$work->price_total;
            $totAbono += $abono;

            $rows[] = [
                $work->tracking_code,
                $work->client->first_name,
                $work->client->last_name,
                $work->client->document_type ?? 'CC',
                $work->client->document_number,
                $work->client->phone,
                $work->client->email,
                $work->client->address,
                $work->laboratory->name,
                $work->lens_type_name,
                $work->lens_material_name,
                $work->treatment_antireflective ? 'Sí' : 'No',
                $work->treatment_photochromic ? 'Sí' : 'No',
                $work->treatment_blue_filter ? 'Sí' : 'No',
                $work->treatment_polarized ? 'Sí' : 'No',
                $montura,
                $f?->od_sphere, $f?->od_cylinder, $f?->od_axis, $f?->od_add,
                $f?->oi_sphere, $f?->oi_cylinder, $f?->oi_axis, $f?->oi_add,
                (float)$work->price_lenses,
                (float)$work->price_frame,
                (float)$work->price_total,
                $abono,
                $work->observations,
            ];
        }

        // Fila de totales
        $rows[] = [
            '', '', '', '', '', '', '', '',
            '', '', '', '', '', '', '', '',
            '', '', '', '',
            '', '', '', '',
            $totLentes, $totMontura, $totTotal, $totAbono,
            'TOTALES',
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = 'AC';

        // Fila 1: Título
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '103192']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Fila 2: Instrucción
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '9A3412']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(30);

        // Fila 3: Categorías con merge y colores
        $catRanges = [
            ['A3:A3', '0A2060'],
            ['B3:H3', '0A2060'],
            ['I3:P3', '103192'],
            ['Q3:T3', '2563EB'],
            ['U3:X3', '2563EB'],
            ['Y3:AB3', '103192'],
            ['AC3:AC3', '103192'],
        ];
        foreach ($catRanges as [$range, $color]) {
            $sheet->mergeCells($range);
            $sheet->getStyle($range)->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        // Fila 4: Headers
        $sheet->getStyle("A4:{$lastCol}4")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '103192']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0A2060']]],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(25);

        // Filas de datos: alternadas
        for ($i = 5; $i < $lastRow; $i++) {
            if ($i % 2 === 1) {
                $sheet->getStyle("A{$i}:{$lastCol}{$i}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F7FA']],
                ]);
            }
        }

        // Fila de totales
        $sheet->getStyle("A{$lastRow}:{$lastCol}{$lastRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8EDFF']],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '103192']]],
        ]);

        // Formato moneda para columnas de precios (Y, Z, AA, AB = 25, 26, 27, 28)
        $priceCols = ['Y', 'Z', 'AA', 'AB'];
        foreach ($priceCols as $col) {
            $sheet->getStyle("{$col}5:{$col}{$lastRow}")
                ->getNumberFormat()->setFormatCode('$#,##0');
        }

        // Freeze panes
        $sheet->freezePane('A5');

        // Bordes en datos
        $sheet->getStyle("A4:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D6DCE8']]],
        ]);

        return [];
    }
}
