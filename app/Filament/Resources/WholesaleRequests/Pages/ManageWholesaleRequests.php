<?php

namespace App\Filament\Resources\WholesaleRequests\Pages;

use App\Exports\WholesaleRequestsExport;
use App\Filament\Resources\WholesaleRequests\WholesaleRequestResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use Maatwebsite\Excel\Facades\Excel;

class ManageWholesaleRequests extends ManageRecords
{
    protected static string $resource = WholesaleRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => Excel::download(
                    new WholesaleRequestsExport($this->getFilteredTableQuery()),
                    'wholesale_requests_' . now()->format('Y-m-d') . '.xlsx',
                )),
        ];
    }
}