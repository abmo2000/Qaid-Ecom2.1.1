<?php

namespace App\Exports;

use App\Models\WholesaleRequest;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WholesaleRequestsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, ShouldAutoSize
{
    public function __construct(private readonly Builder $query)
    {
    }

    public function query(): Builder
    {
        return $this->query->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return ['Business Name', 'Phone Number', 'Message', 'Date Submitted', 'Status', 'Language', 'IP Address', 'Operating System'];
    }

    public function map($request): array
    {
        return [
            $request->business_name,
            $request->phone,
            $request->message ?? '',
            $request->created_at?->format('Y-m-d H:i:s') ?? '',
            match ($request->status) {
                'contacted' => 'Contacted',
                'closed' => 'Closed',
                default => 'New',
            },
            strtoupper((string) ($request->locale ?: '')),
            $request->ip_address ?? '',
            $request->os ?? '',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $highestRow = max(1, $sheet->getHighestRow());

        $sheet->freezePane('A2');
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E78']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
        ]);
        $sheet->getStyle('A2:H' . $highestRow)->applyFromArray([
            'font' => ['size' => 10, 'name' => 'Arial'],
            'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 28,
            'B' => 18,
            'C' => 55,
            'D' => 20,
            'E' => 14,
            'F' => 12,
            'G' => 18,
            'H' => 20,
        ];
    }
}