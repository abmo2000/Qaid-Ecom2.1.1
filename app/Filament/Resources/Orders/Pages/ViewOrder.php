<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('invoice')
                ->label('Invoice')
                ->icon('heroicon-o-document-text')
                ->url(fn (Order $record): string => route('dashboard.orders.invoice', $record))
                ->openUrlInNewTab(),
            Action::make('edit')
                ->label('Edit Order')
                ->icon('heroicon-o-pencil-square')
                ->url(fn (Order $record): string => OrderResource::getUrl('edit', ['record' => $record])),
        ];
    }
}
