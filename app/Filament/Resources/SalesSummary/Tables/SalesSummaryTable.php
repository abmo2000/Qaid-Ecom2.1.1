<?php

namespace App\Filament\Resources\SalesSummary\Tables;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SalesSummaryTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Sales Summary')
            ->description('All completed invoices with full customer and order details.')
            ->query(
                Order::query()
                    ->with(['customer', 'adminCreator', 'items'])
                    ->orderByDesc('created_at')
            )
            ->columns([
                TextColumn::make('id')
                    ->label('Invoice #')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('Customer Name')
                    ->searchable()
                    ->formatStateUsing(fn ($state, Order $record): string => $record->customer?->name ?? 'Guest Customer'),

                TextColumn::make('customer.email')
                    ->label('Customer Email')
                    ->searchable()
                    ->formatStateUsing(fn ($state, Order $record): string => $record->customer?->email ?? 'No email'),

                TextColumn::make('customer.phone')
                    ->label('Phone')
                    ->searchable()
                    ->formatStateUsing(fn ($state, Order $record): string => $record->customer?->phone ?? 'No phone'),

                TextColumn::make('customer_address')
                    ->label('Delivery Address')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('adminCreator.name')
                    ->label('Sales Admin')
                    ->searchable()
                    ->default('Website Order')
                    ->formatStateUsing(fn (?string $state): string => $state ?: 'Website Order'),

                TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        OrderStatus::PENDING->value => 'warning',
                        OrderStatus::PROCESSING->value => 'info',
                        OrderStatus::SHIPPED->value => 'primary',
                        OrderStatus::RECEIVED->value => 'success',
                        OrderStatus::COMPLETED->value => 'success',
                        OrderStatus::CANCELLED->value => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => OrderStatus::tryFrom($state)?->label() ?? ucfirst($state)),

                TextColumn::make('items_total')
                    ->label('Products Price')
                    ->sortable(false)
                    ->formatStateUsing(fn ($state, Order $record): string => 'EGP ' . number_format((float) $record->items->sum('amount'), 2)),

                TextColumn::make('delivery_price')
                    ->label('Shipping')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => 'EGP ' . number_format((float) $state, 2)),

                TextColumn::make('amount')
                    ->label('Total (Products + Shipping)')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => 'EGP ' . number_format((float) $state, 2)),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->paginated([10, 25, 50]);
    }
}
