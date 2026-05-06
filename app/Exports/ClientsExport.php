<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ClientsExport implements FromArray, WithStyles, WithTitle, ShouldAutoSize
{
    public function title(): string
    {
        return 'Clientes';
    }

    public function array(): array
    {
        $clients = Client::with(['formulas', 'works.payments'])->orderBy('first_name')->get();

        $rows = [];
        $rows[] = ['CLIENTES - ÓPTICA UNIVERSO VISUAL — Exportado el ' . now()->format('d/m/Y H:i')];

        $rows[] = [
            'Nombre Completo', 'Tipo Doc', 'Nro Documento', 'Teléfono', 'Email',
            'Dirección', 'Fecha Nacimiento', 'WhatsApp Autorizado',
            'Última Fórmula OD', 'Última Fórmula OI', 'Fecha Último Examen',
            'Cant. Trabajos', 'Total Gastado', 'Total Pagado', 'Saldo Pendiente', 'Notas',
        ];

        foreach ($clients as $client) {
            $lastFormula = $client->formulas->sortByDesc('created_at')->first();
            $totalSpent = $client->works->sum('price_total');
            $totalPaid = $client->works->sum(fn($w) => $w->payments->sum('amount'));

            $odFormula = $lastFormula
                ? "Esf:{$lastFormula->od_sphere} Cil:{$lastFormula->od_cylinder} Eje:{$lastFormula->od_axis} ADD:{$lastFormula->od_add}"
                : '';
            $oiFormula = $lastFormula
                ? "Esf:{$lastFormula->oi_sphere} Cil:{$lastFormula->oi_cylinder} Eje:{$lastFormula->oi_axis} ADD:{$lastFormula->oi_add}"
                : '';

            $rows[] = [
                $client->full_name,
                $client->document_type ?? 'CC',
                $client->document_number,
                $client->phone,
                $client->email,
                $client->address,
                $client->birth_date?->format('d/m/Y'),
                $client->whatsapp_authorized ? 'Sí' : 'No',
                $odFormula,
                $oiFormula,
                $lastFormula?->exam_date?->format('d/m/Y'),
                $client->works->count(),
                $totalSpent,
                $totalPaid,
                $totalSpent - $totalPaid,
                $client->notes,
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = 'P';

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '103192']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '103192']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0A2060']]],
        ]);

        for ($i = 3; $i <= $lastRow; $i++) {
            if ($i % 2 === 1) {
                $sheet->getStyle("A{$i}:{$lastCol}{$i}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F7FA']],
                ]);
            }
        }

        foreach (['M', 'N', 'O'] as $col) {
            $sheet->getStyle("{$col}3:{$col}{$lastRow}")
                ->getNumberFormat()->setFormatCode('$#,##0');
        }

        $sheet->getStyle("A2:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D6DCE8']]],
        ]);

        $sheet->freezePane('A3');

        return [];
    }
}
