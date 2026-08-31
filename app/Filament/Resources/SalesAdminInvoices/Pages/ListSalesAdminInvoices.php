<?php

namespace App\Filament\Resources\SalesAdminInvoices\Pages;

use App\Filament\Resources\SalesAdminInvoices\SalesAdminInvoicesResource;
use Filament\Resources\Pages\ListRecords;

class ListSalesAdminInvoices extends ListRecords
{
    protected static string $resource = SalesAdminInvoicesResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
