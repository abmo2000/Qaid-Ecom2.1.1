<?php

namespace App\Filament\Resources\SalesSummary\Pages;

use App\Exports\SalesSummaryExport;
use App\Filament\Resources\SalesSummary\SalesSummaryResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListSalesSummary extends ListRecords
{
    protected static string $resource = SalesSummaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return Excel::download(new SalesSummaryExport, 'Sales-Summary-Report.xlsx');
                }),
        ];
    }
}
