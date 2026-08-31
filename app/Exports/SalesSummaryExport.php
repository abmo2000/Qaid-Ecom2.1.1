<?php

namespace App\Exports;

use App\Enums\OrderStatus;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesSummaryExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new SalesOrdersSheet,
            new SalesSummarySheet,
        ];
    }
}

class SalesOrdersSheet implements FromArray, WithTitle, WithStyles
{
    public function title(): string
    {
        return 'Sales Orders';
    }

    public function array(): array
    {
        $orders = Order::query()
            ->with(['customer', 'adminCreator', 'items'])
            ->orderByDesc('created_at')
            ->get();

        $rows = [[
            'Invoice #',
            'Order ID',
            'Customer Name',
            'Customer Email',
            'Phone',
            'Delivery Address',
            'Sales Admin',
            'Payment Method',
            'Status',
            'Products Price',
            'Shipping',
            'Total',
            'Created At',
        ]];

        foreach ($orders as $order) {
            $rows[] = [
                (int) $order->id,
                (string) $order->order_id,
                $order->customer?->name ?? 'Guest Customer',
                $order->customer?->email ?? 'No email',
                $order->customer?->phone ?? 'No phone',
                $order->customer_address ?? '',
                $order->adminCreator?->name ?? 'Website Order',
                $order->payment_method ?? '',
                OrderStatus::tryFrom((string) $order->status)?->label() ?? ucfirst((string) $order->status),
                (float) $order->items->sum('amount'),
                (float) ($order->delivery_price ?? 0),
                (float) $order->amount,
                $order->created_at?->format('Y-m-d H:i:s') ?? '',
            ];
        }

        $totalRows = count($rows);
        $rows[] = [
            'TOTAL',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            $this->sumColumn($orders, 'products'),
            $this->sumColumn($orders, 'shipping'),
            $this->sumColumn($orders, 'total'),
            '',
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet): void
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $sheet->freezePane('A2');
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10, 'name' => 'Arial'],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
        ]);

        $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
            'font' => ['size' => 10, 'name' => 'Arial'],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getStyle('A2:A' . $highestRow)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('J2:L' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00 "EGP"');
        $sheet->getStyle('M2:M' . $highestRow)->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm');

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(26);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(45);
        $sheet->getColumnDimension('G')->setWidth(16);
        $sheet->getColumnDimension('H')->setWidth(16);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(12);
        $sheet->getColumnDimension('L')->setWidth(14);
        $sheet->getColumnDimension('M')->setWidth(18);

        $sheet->setCellValue('A' . ($highestRow), 'TOTAL');
        $sheet->getStyle('A' . ($highestRow) . ':I' . ($highestRow))->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '000000'], 'name' => 'Arial'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
        ]);

        $sheet->getStyle('J' . ($highestRow) . ':L' . ($highestRow))->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '000000'], 'name' => 'Arial'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
            'numberFormat' => ['formatCode' => '#,##0.00 "EGP"'],
        ]);
    }

    protected function sumColumn(iterable $orders, string $type): float
    {
        $total = 0;

        foreach ($orders as $order) {
            if ($type === 'products') {
                $total += (float) $order->items->sum('amount');
                continue;
            }

            if ($type === 'shipping') {
                $total += (float) ($order->delivery_price ?? 0);
                continue;
            }

            $total += (float) $order->amount;
        }

        return $total;
    }
}

class SalesSummarySheet implements FromArray, WithTitle, WithStyles
{
    public function title(): string
    {
        return 'Summary';
    }

    public function array(): array
    {
        $generated = date('Y-m-d');

        return [
            ['Sales Summary Report', '', '', ''],
            ['Generated: ' . $generated . '  |  Source: Sales Orders sheet', '', '', ''],
            [],
            ['Total Orders', '=COUNTA(\'Sales Orders\'!A2:A20)'],
            ['Total Revenue', '=SUM(\'Sales Orders\'!L2:L20)'],
            ['Average Order Value', '=AVERAGE(\'Sales Orders\'!L2:L20)'],
            [],
            [],
            ['Orders by Status', '', ''],
            ['Status', 'Order Count', 'Revenue'],
            ['Pending', '=COUNTIF(\'Sales Orders\'!$I$2:$I$20,A11)', '=SUMIF(\'Sales Orders\'!$I$2:$I$20,A11,\'Sales Orders\'!$L$2:$L$20)'],
            ['Completed', '=COUNTIF(\'Sales Orders\'!$I$2:$I$20,A12)', '=SUMIF(\'Sales Orders\'!$I$2:$I$20,A12,\'Sales Orders\'!$L$2:$L$20)'],
            ['Cancelled', '=COUNTIF(\'Sales Orders\'!$I$2:$I$20,A13)', '=SUMIF(\'Sales Orders\'!$I$2:$I$20,A13,\'Sales Orders\'!$L$2:$L$20)'],
            ['Shipped', '=COUNTIF(\'Sales Orders\'!$I$2:$I$20,A14)', '=SUMIF(\'Sales Orders\'!$I$2:$I$20,A14,\'Sales Orders\'!$L$2:$L$20)'],
            [],
            [],
            ['Revenue by Sales Admin', '', ''],
            ['Sales Admin', 'Order Count', 'Revenue'],
            ['Abdullah Mo', '=COUNTIF(\'Sales Orders\'!$G$2:$G$20,A19)', '=SUMIF(\'Sales Orders\'!$G$2:$G$20,A19,\'Sales Orders\'!$L$2:$L$20)'],
            ['Website Order', '=COUNTIF(\'Sales Orders\'!$G$2:$G$20,A20)', '=SUMIF(\'Sales Orders\'!$G$2:$G$20,A20,\'Sales Orders\'!$L$2:$L$20)'],
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');

        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1F4E78'], 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getStyle('A2:D2')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '808080'], 'size' => 10, 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getStyle('A4:B6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
        ]);

        $sheet->getStyle('A9:C9')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10, 'name' => 'Arial'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E78']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
        ]);

        $sheet->getStyle('A10:C14')->applyFromArray([
            'font' => ['size' => 10, 'name' => 'Arial'],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
        ]);

        $sheet->getStyle('A17:C17')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10, 'name' => 'Arial'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E78']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
        ]);

        $sheet->getStyle('A18:C20')->applyFromArray([
            'font' => ['size' => 10, 'name' => 'Arial'],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
        ]);

        $sheet->getColumnDimension('A')->setWidth(22);
        $sheet->getColumnDimension('B')->setWidth(16);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(15);

        $sheet->getStyle('B4:B6')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('C11:C14')->getNumberFormat()->setFormatCode('#,##0.00 "EGP"');
        $sheet->getStyle('C19:C20')->getNumberFormat()->setFormatCode('#,##0.00 "EGP"');
    }
}
