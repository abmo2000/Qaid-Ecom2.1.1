<?php

namespace App\Filament\Resources\SalesAdminInvoices\Tables;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SalesAdminInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Sales Admin')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('invoices_count')
                    ->label('Total Invoices')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('invoices_total')
                    ->label('Invoices Amount')
                    ->money('EGP')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('view_invoices')
                    ->label('View Invoices')
                    ->icon('heroicon-o-document-text')
                    ->url(fn (User $record): string => OrderResource::getUrl('index', ['sales_admin_id' => $record->id])),
            ])
            ->paginated([10, 25, 50]);
    }
}
